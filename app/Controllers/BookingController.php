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

    // --- 2. ประมวลผลการจอง (คำนวณราคา & เวลา) ---
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

        // 1. รวมวันที่+เวลา เป็น format 'Y-m-d H:i:s' (เพื่อลง DB)
        $startDateTime = date('Y-m-d H:i:s', strtotime("$date $time"));
        
        // 2. คำนวณเวลาจบ (Start + Hours)
        $endDateTime = date('Y-m-d H:i:s', strtotime("$startDateTime + $hours hours"));

        // ดึงข้อมูลสนามเพื่อเอา ราคา และ Vendor ID
        $stadium = $this->stadiumModel->find($stadiumId);
        
        // 3. คำนวณราคารวม
        $totalPrice = $stadium['price'] * $hours;

        $data = [
            'stadium_id' => $stadiumId,
            'customer_id' => session()->get('user_id'),
            'vendor_id' => $stadium['vendor_id'], // (สำคัญ: ต้องมีเพื่อแก้ Error Foreign Key)
            
            // ใช้ชื่อคอลัมน์ตาม Database ของคุณ
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

    // --- 3. หน้า Checkout (ตรวจสอบรายการ) ---
    public function checkout($booking_id = null)
    {
        $booking = $this->bookingModel
            ->select('bookings.*, stadiums.name as stadium_name')
            ->join('stadiums', 'stadiums.id = bookings.stadium_id')
            ->find($booking_id);

        if (!$booking || $booking['status'] != 'pending') {
             return redirect()->to('customer/dashboard')->with('error', 'ไม่พบรายการ หรือรายการนี้ถูกดำเนินการไปแล้ว');
        }

        // 4. แปลงข้อมูลกลับมาเป็นรูปแบบที่ View เข้าใจง่ายๆ 
        // (เพราะ View เราใช้ $booking['booking_date'], $booking['start_time'])
        $start = strtotime($booking['booking_start_time']);
        $end   = strtotime($booking['booking_end_time']);

        $booking['booking_date'] = date('d/m/Y', $start);
        $booking['start_time']   = date('H:i', $start);
        $booking['end_time']     = date('H:i', $end);
        
        // คำนวณจำนวนชั่วโมงเพื่อโชว์ในหน้า View
        $booking['hours_count']  = ($end - $start) / 3600;

        $data = [
            'title' => 'ยืนยันการชำระเงิน',
            'booking' => $booking,
        ];

        return view('customer/payment_checkout', $data);
    }

    // --- 4. ประมวลผลการจ่ายเงิน (จำลอง) ---
    public function processPayment()
    {
        $booking_id = $this->request->getPost('booking_id');
        
        // (จำลองการจ่ายเงินสำเร็จ)
        $payment_success = true; 

        if ($payment_success) {
            // อัปเดตสถานะเป็น confirmed
            $this->bookingModel->update($booking_id, [
                'status' => 'confirmed'
            ]);
            
            // Redirect ทันที (ไม่มี echo เพื่อแก้หน้าขาว)
            return redirect()->to('customer/payment/success/' . $booking_id);
        } else {
            return redirect()->back()->with('error', 'การชำระเงินล้มเหลว');
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