<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StadiumModel;
use App\Models\BookingModel;
use App\Models\StadiumFieldModel; 

class BookingController extends BaseController
{
    protected $stadiumModel;
    protected $bookingModel;

    public function __construct()
    {
        $this->stadiumModel   = new StadiumModel();
        $this->bookingModel   = new BookingModel();
    }

    
    public function viewStadium($stadium_id = null)
    {
        $stadium = $this->stadiumModel->getStadiumsWithCategory($stadium_id);

        if (!$stadium) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('ไม่พบสนามที่ต้องการจอง');
        }

        // Get fields with facilities and items
        $fieldModel = new StadiumFieldModel();
        $fields = $fieldModel->where('stadium_id', $stadium_id)
                             ->where('status', 'active')
                             ->findAll();

        foreach ($fields as &$field) {
            // Get facilities for this field
            $db = \Config\Database::connect();
            $field['facilities'] = $db->table('stadium_facilities')
                ->select('stadium_facilities.id as stadium_facility_id, facility_types.name')
                ->join('facility_types', 'facility_types.id = stadium_facilities.facility_type_id')
                ->where('stadium_facilities.field_id', $field['id'])
                ->get()->getResultArray();

            // For each facility, get vendor items
            foreach ($field['facilities'] as &$facility) {
                $facility['items'] = $db->table('vendor_items')
                    ->where('stadium_facility_id', $facility['stadium_facility_id'])
                    ->where('status', 'active')
                    ->get()->getResultArray();
            }
        }

        $data = [
            'title'   => 'จองสนาม: ' . esc($stadium['name']),
            'stadium' => $stadium,
            'fields'  => $fields,
        ];

        return view('customer/booking_form', $data);
    }

    
    public function processBooking()
    {
        
        $rules = [
            'stadium_id'   => 'required|integer',
            'field_id'     => 'required|integer',
            'booking_date' => 'required|valid_date',
            'start_time'   => 'required',
            'hours'        => 'required|integer|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        
        $stadiumId = $this->request->getPost('stadium_id');
        $fieldId   = $this->request->getPost('field_id');
        $date      = $this->request->getPost('booking_date');
        $time      = $this->request->getPost('start_time');
        $hours     = (int) $this->request->getPost('hours');

        
        $startDateTime = date('Y-m-d H:i:s', strtotime("$date $time"));
        $endDateTime   = date('Y-m-d H:i:s', strtotime("$startDateTime + $hours hours"));

        
        $stadium = $this->stadiumModel->find($stadiumId);
        
        
        $fieldModel = new StadiumFieldModel();
        $fieldData = $fieldModel->find($fieldId);
        $pricePerHour = $fieldData ? $fieldData['price'] : $stadium['price'];

        // Calculate Item Add-ons
        $selectedItems = $this->request->getPost('items') ?? [];
        $itemTotal = 0;
        if (!empty($selectedItems)) {
            $itemModel = new \App\Models\VendorItemModel();
            $itemsData = $itemModel->whereIn('id', $selectedItems)->findAll();
            foreach ($itemsData as $item) {
                $itemTotal += (float)$item['price'];
            }
        }

        $totalPrice = ($pricePerHour * $hours) + $itemTotal;

        $data = [
            'stadium_id'       => $stadiumId,
            'field_id'         => $fieldId,
            'customer_id'      => session()->get('user_id'),
            'vendor_id'        => $stadium['vendor_id'],
            'booking_start_time' => $startDateTime,
            'booking_end_time'   => $endDateTime,
            'total_price'      => $totalPrice,
            'status'           => 'pending', 
            'is_viewed_by_admin' => 0, 
        ];
        
        $this->bookingModel->insert($data);
        $newBookingId = $this->bookingModel->getInsertID();

        
        return redirect()->to('customer/payment/checkout/' . $newBookingId)
                         ->with('success', 'การจองถูกสร้างเรียบร้อย! กรุณาตรวจสอบและชำระเงิน');
    }

    
    public function checkout($booking_id = null)
    {
        $booking = $this->bookingModel
            ->select('bookings.*, stadiums.name as stadium_name')
            ->join('stadiums', 'stadiums.id = bookings.stadium_id')
            ->find($booking_id);

        if (!$booking || $booking['status'] != 'pending') {
             return redirect()->to('customer/dashboard')->with('error', 'ไม่พบรายการ หรือรายการนี้ถูกดำเนินการไปแล้ว');
        }

        $start = strtotime($booking['booking_start_time']);
        $end   = strtotime($booking['booking_end_time']);

        $booking['booking_date'] = date('d/m/Y', $start);
        $booking['start_time']   = date('H:i', $start);
        $booking['end_time']     = date('H:i', $end);
        $booking['hours_count']  = ($end - $start) / 3600;

        $data = [
            'title'   => 'ยืนยันการชำระเงิน',
            'booking' => $booking,
        ];

        return view('customer/payment_checkout', $data);
    }

    
    public function processPayment()
    {
        $booking_id = $this->request->getPost('booking_id');
        $file = $this->request->getFile('slip_image');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads/slips', $newName);

            $this->bookingModel->update($booking_id, [
                'slip_image' => $newName,
                'status' => 'pending' 
            ]);
            
            return redirect()->to('customer/payment/success/' . $booking_id);
        } else {
            return redirect()->back()->with('error', 'กรุณาแนบสลิปโอนเงินให้ถูกต้อง (รองรับไฟล์ภาพเท่านั้น)');
        }
    }

    
    public function paymentSuccess($booking_id = null)
    {
        $data = [
            'title' => 'การชำระเงินสำเร็จ!',
            'booking_id' => $booking_id,
        ];
        return view('customer/payment_success', $data);
    }

    /**
     * API: Check field availability for a specific date
     */
    public function checkAvailability()
    {
        $fieldId = $this->request->getGet('field_id');
        $date    = $this->request->getGet('date');

        if (!$fieldId || !$date) {
            return $this->response->setJSON(['error' => 'Missing parameters']);
        }

        // Fetch bookings for this field on this date
        $bookings = $this->bookingModel
            ->where('field_id', $fieldId)
            ->where('status !=', 'cancelled')
            ->where("DATE(booking_start_time)", $date)
            ->findAll();

        $slots = [];
        foreach ($bookings as $b) {
            $slots[] = [
                'start' => date('H:i', strtotime($b['booking_start_time'])),
                'end'   => date('H:i', strtotime($b['booking_end_time'])),
                'status' => $b['status']
            ];
        }

        return $this->response->setJSON([
            'date' => $date,
            'field_id' => $fieldId,
            'booked_slots' => $slots
        ]);
    }
}
