<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StadiumModel;
use App\Models\BookingModel;
use App\Models\StadiumFieldModel;
use App\Models\FieldItemModel; // ✅ เพิ่ม Model นี้เข้ามาใหม่

class BookingController extends BaseController
{
    protected $stadiumModel;
    protected $bookingModel;
    protected $fieldItemModel; // ✅ ประกาศ property

    public function __construct()
    {
        $this->stadiumModel   = new StadiumModel();
        $this->bookingModel   = new BookingModel();
        $this->fieldItemModel = new FieldItemModel(); // ✅ Init Model
    }

    // --- 1. หน้าดูรายละเอียดสนาม (พร้อมเลือกสนามย่อย + สินค้า) ---
    public function viewStadium($stadium_id = null)
    {
        // 1. ดึงข้อมูลสนามหลัก
        $stadium = $this->stadiumModel->getStadiumsWithCategory($stadium_id);

        if (!$stadium) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('ไม่พบสนามที่ต้องการจอง');
        }

        // 2. ดึงข้อมูลสนามย่อย (Fields) ที่สถานะ Active
        $fieldModel = new StadiumFieldModel();
        $fields = $fieldModel->where('stadium_id', $stadium_id)
                             ->where('status', 'active')
                             ->findAll();

        // 3. ✅ ดึงข้อมูลสินค้า/บริการเสริม (Add-ons)
        $addons = []; // ตัวแปรสำหรับเก็บสินค้ากรณี Single

        if (($stadium['booking_type'] ?? '') == 'complex') {
            // กรณี Complex: สินค้าจะผูกกับ "สนามย่อย" แต่ละอัน
            // เราจะวนลูปยัดข้อมูลสินค้าใส่เข้าไปในอาร์เรย์ของแต่ละ Field เลย
            foreach ($fields as &$field) {
                $field['addons'] = $this->fieldItemModel->getItemsByField($field['id']);
            }
            unset($field); // ตัด reference ทิ้งเพื่อความปลอดภัย
        } else {
            // กรณี Single: สินค้าผูกกับ "stadium_id" โดยตรง (field_id = NULL)
            $addons = $this->fieldItemModel->getItemsByField(null, $stadium_id);
        }

        $data = [
            'title'   => 'จองสนาม: ' . esc($stadium['name']),
            'stadium' => $stadium,
            'fields'  => $fields, // ถ้าเป็น Complex ในนี้จะมี key ['addons'] ติดไปด้วย
            'addons'  => $addons  // ถ้าเป็น Single จะใช้ตัวแปรนี้แสดงผล
        ];

        return view('customer/booking_form', $data);
    }

    // --- 2. ประมวลผลการจอง ---
    public function processBooking()
    {
        // Validation
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

        // รับค่าจากฟอร์ม
        $stadiumId = $this->request->getPost('stadium_id');
        $fieldId   = $this->request->getPost('field_id');
        $date      = $this->request->getPost('booking_date');
        $time      = $this->request->getPost('start_time');
        $hours     = (int) $this->request->getPost('hours');

        // คำนวณวันเวลา
        $startDateTime = date('Y-m-d H:i:s', strtotime("$date $time"));
        $endDateTime   = date('Y-m-d H:i:s', strtotime("$startDateTime + $hours hours"));

        // ดึงข้อมูลราคาจากสนามหลัก (หรือสนามย่อยถ้ามี) - *ส่วนนี้อาจต้องปรับปรุงในอนาคตให้ดึงราคาจาก field_id จริงๆ*
        // แต่ตอนนี้ใช้ตาม logic เดิมไปก่อน
        $stadium = $this->stadiumModel->find($stadiumId);
        
        // *หมายเหตุ: ถ้าจะให้แม่นยำควรดึงราคาจาก StadiumFieldModel ตาม $fieldId*
        $fieldModel = new StadiumFieldModel();
        $fieldData = $fieldModel->find($fieldId);
        $pricePerHour = $fieldData ? $fieldData['price'] : $stadium['price']; // ใช้ราคาจากสนามย่อยถ้ามี

        $totalPrice = $pricePerHour * $hours;

        // TODO: ในอนาคตต้องมารับค่า 'addons' ที่ลูกค้าติ๊กเลือกตรงนี้ แล้วบวกราคาเพิ่มเข้าไปใน $totalPrice
        // และบันทึกลงตาราง booking_items (ที่ต้องสร้างเพิ่ม)

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

        // ส่งไปหน้าจ่ายเงิน
        return redirect()->to('customer/payment/checkout/' . $newBookingId)
                         ->with('success', 'การจองถูกสร้างเรียบร้อย! กรุณาตรวจสอบและชำระเงิน');
    }

    // --- 3. หน้า Checkout ---
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

    // --- 4. ประมวลผลการจ่ายเงิน ---
    public function processPayment()
    {
        $booking_id = $this->request->getPost('booking_id');
        $file = $this->request->getFile('slip_image');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads/slips', $newName);

            $this->bookingModel->update($booking_id, [
                'slip_image' => $newName,
                'status' => 'pending' // สถานะยังคงเป็น pending รอแอดมินกด Approve
            ]);
            
            return redirect()->to('customer/payment/success/' . $booking_id);
        } else {
            return redirect()->back()->with('error', 'กรุณาแนบสลิปโอนเงินให้ถูกต้อง (รองรับไฟล์ภาพเท่านั้น)');
        }
    }

    // --- 5. หน้าจ่ายเงินสำเร็จ ---
    public function paymentSuccess($booking_id = null)
    {
        $data = [
            'title' => 'การชำระเงินสำเร็จ!',
            'booking_id' => $booking_id,
        ];
        return view('customer/payment_success', $data);
    }
}