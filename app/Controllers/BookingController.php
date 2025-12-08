<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StadiumModel;
use App\Models\BookingModel;
use App\Models\StadiumFieldModel;
use App\Models\FieldItemModel; 

class BookingController extends BaseController
{
    protected $stadiumModel;
    protected $bookingModel;
    protected $fieldItemModel; 

    public function __construct()
    {
        $this->stadiumModel   = new StadiumModel();
        $this->bookingModel   = new BookingModel();
        $this->fieldItemModel = new FieldItemModel(); 
    }

    
    public function viewStadium($stadium_id = null)
    {
        
        $stadium = $this->stadiumModel->getStadiumsWithCategory($stadium_id);

        if (!$stadium) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('ไม่พบสนามที่ต้องการจอง');
        }

        
        $fieldModel = new StadiumFieldModel();
        $fields = $fieldModel->where('stadium_id', $stadium_id)
                             ->where('status', 'active')
                             ->findAll();

        
        $addons = []; 

        if (($stadium['booking_type'] ?? '') == 'complex') {
            
            foreach ($fields as &$field) {
                $field['addons'] = $this->fieldItemModel->getItemsByField($field['id']);
            }
            unset($field); 
        } else {
            
            $addons = $this->fieldItemModel->getItemsByField(null, $stadium_id);
        }

        $data = [
            'title'   => 'จองสนาม: ' . esc($stadium['name']),
            'stadium' => $stadium,
            'fields'  => $fields, 
            'addons'  => $addons  
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

        $totalPrice = $pricePerHour * $hours;

        

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
}