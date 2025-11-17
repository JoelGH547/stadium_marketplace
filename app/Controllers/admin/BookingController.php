<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\BookingModel;

class BookingController extends BaseController
{
    protected $bookingModel;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
    }

    /**
     * 1. (สำหรับลิงก์ 'admin/bookings/new')
     * แสดง "การจองใหม่" (จ่ายเงินแล้ว & Admin ยังไม่เห็น)
     */
    public function indexNew()
    {
        $data = [
            'title' => 'New Bookings (Confirmed, Unread)',
            'bookings' => $this->bookingModel
                ->select('bookings.*, stadiums.name as stadium_name, customers.username as customer_name')
                ->join('stadiums', 'stadiums.id = bookings.stadium_id')
                ->join('customers', 'customers.id = bookings.customer_id')
                ->where('bookings.status', 'confirmed')
                ->where('bookings.is_viewed_by_admin', 0)
                ->orderBy('bookings.created_at', 'DESC')
                ->findAll(),
        ];
        
        // (สำคัญ) เมื่อ Admin โหลดหน้านี้... 
        // ให้อัปเดต 'is_viewed_by_admin' ทั้งหมดเป็น 1 (อ่านแล้ว)
        $this->bookingModel
            ->where('status', 'confirmed')
            ->where('is_viewed_by_admin', 0)
            ->set(['is_viewed_by_admin' => 1])
            ->update();

        // เราจะสร้าง View นี้ในขั้นตอนต่อไป
        return view('admin/bookings/index', $data);
    }

    /**
     * 2. (สำหรับลิงก์ 'admin/bookings/pending')
     * แสดง "การจองรอจ่าย" (Pending)
     */
    public function indexPending()
    {
        $data = [
            'title' => 'Pending Bookings (Awaiting Payment)',
            'bookings' => $this->bookingModel
                ->select('bookings.*, stadiums.name as stadium_name, customers.username as customer_name')
                ->join('stadiums', 'stadiums.id = bookings.stadium_id')
                ->join('customers', 'customers.id = bookings.customer_id')
                ->where('bookings.status', 'pending')
                ->orderBy('bookings.created_at', 'DESC')
                ->findAll(),
        ];
        
        return view('admin/bookings/index', $data); // (ใช้ View เดียวกัน)
    }

    /**
     * 3. (สำหรับ Admin) ฟังก์ชัน "ยกเลิก" การจอง
     */
    public function cancel($booking_id = null)
    {
        // (อัปเดตสถานะเป็น 'cancelled')
        $this->bookingModel->update($booking_id, [
            'status' => 'cancelled'
        ]);

        return redirect()->back()->with('success', 'Booking (ID: '.$booking_id.') has been cancelled.');
    }
}