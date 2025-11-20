<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StadiumModel;
use App\Models\BookingModel;

class BookingController extends BaseController
{
    protected $stadiumModel;
    protected $bookingModel;

    public function __construct()
    {
        $this->stadiumModel = new StadiumModel();
        $this->bookingModel = new BookingModel();
    }

    // --- 1. หน้าดูรายละเอียดสนาม (ฟอร์มจอง) ---
    public function viewStadium($stadium_id = null)
    {
        $stadium = $this->stadiumModel->getStadiumsWithCategory($stadium_id);

        if (!$stadium) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('ไม่พบสนามที่ต้องการจอง');
        }

        $data = [
            'title' => 'จองสนาม: ' . esc($stadium['name']),
            'stadium' => $stadium,
        ];

        return view('customer/booking_form', $data);
    }

    // --- 2. ประมวลผลการจอง ---
    public function processBooking()
    {
        // Validation
        $rules = [
            'stadium_id' => 'required|integer',
            'booking_date' => 'required|valid_date',
            'start_time' => 'required',
            'hours' => 'required|integer|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // รับค่าจากฟอร์ม
        $stadiumId = $this->request->getPost('stadium_id');
        $date = $this->request->getPost('booking_date');
        $time = $this->request->getPost('start_time');
        $hours = (int) $this->request->getPost('hours');

        // คำนวณวันเวลา
        $startDateTime = date('Y-m-d H:i:s', strtotime("$date $time"));
        $endDateTime = date('Y-m-d H:i:s', strtotime("$startDateTime + $hours hours"));

        $stadium = $this->stadiumModel->find($stadiumId);
        $totalPrice = $stadium['price'] * $hours;

        $data = [
            'stadium_id' => $stadiumId,
            'customer_id' => session()->get('user_id'),
            'vendor_id' => $stadium['vendor_id'],
            'booking_start_time' => $startDateTime,
            'booking_end_time'   => $endDateTime,
            'total_price' => $totalPrice,
            'status' => 'pending', 
            'is_viewed_by_admin' => 0, 
        ];
        
        $this->bookingModel->insert($data);
        $newBookingId = $this->bookingModel->getInsertID();

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

        // แปลงวันที่เวลา
        $start = strtotime($booking['booking_start_time']);
        $end   = strtotime($booking['booking_end_time']);

        $booking['booking_date'] = date('d/m/Y', $start);
        $booking['start_time']   = date('H:i', $start);
        $booking['end_time']     = date('H:i', $end);
        $booking['hours_count']  = ($end - $start) / 3600;

        $data = [
            'title' => 'ยืนยันการชำระเงิน',
            'booking' => $booking,
        ];

        return view('customer/payment_checkout', $data);
    }

    // --- 4. [แก้ไขใหม่] ประมวลผลการจ่ายเงิน (อัปโหลดสลิป) ---
    public function processPayment()
    {
        $booking_id = $this->request->getPost('booking_id');
        
        // 1. ตรวจสอบว่ามีการอัปโหลดไฟล์รูปภาพมาหรือไม่
        $file = $this->request->getFile('slip_image');

        // ตรวจสอบความถูกต้องของไฟล์ (ต้องมีไฟล์, เป็นรูปภาพ, และยังไม่ได้ย้าย)
        if ($file && $file->isValid() && !$file->hasMoved()) {
            
            // สร้างชื่อไฟล์ใหม่แบบสุ่ม (เพื่อไม่ให้ชื่อซ้ำกัน)
            $newName = $file->getRandomName();

            // ย้ายไฟล์ไปที่ public/uploads/slips
            // ** หมายเหตุ: ระบบจะสร้างโฟลเดอร์ให้เองถ้ายังไม่มี **
            $file->move(ROOTPATH . 'public/uploads/slips', $newName);

            // 2. อัปเดตฐานข้อมูล
            $this->bookingModel->update($booking_id, [
                'slip_image' => $newName,
                'status' => 'pending' // สถานะยังคงรอตรวจสอบ (เพื่อให้ Admin มากด Approve)
            ]);
            
            // ไปหน้าสำเร็จ
            return redirect()->to('customer/payment/success/' . $booking_id);

        } else {
            // กรณีไม่ได้แนบไฟล์ หรือไฟล์ผิดพลาด
            return redirect()->back()
                ->with('error', 'กรุณาแนบสลิปโอนเงินให้ถูกต้อง (รองรับไฟล์ภาพเท่านั้น)');
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