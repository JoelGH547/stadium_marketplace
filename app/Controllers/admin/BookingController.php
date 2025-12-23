<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BookingModel;

class BookingController extends BaseController
{
    protected $bookingModel;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
    }

    // แสดงรายการจองทั้งหมด (Global View)
    public function index()
    {
        // ใช้ฟังก์ชัน getAllBookings ที่เราเขียนใน Model (ที่ Join ตารางมาแล้ว)
        $data = [
            'title'    => 'จัดการการจองทั้งหมด (Global Bookings)',
            'bookings' => $this->bookingModel->getAllBookings()
        ];

        return view('admin/bookings/index', $data);
    }

    // อนุมัติการจอง (เปลี่ยนสถานะเป็น Paid)
    public function approve($id)
    {
        $this->bookingModel->update($id, [
            'status' => 'paid',
            'is_viewed_by_admin' => 1
        ]);
        return redirect()->back()->with('success', 'อนุมัติการชำระเงินเรียบร้อยแล้ว (Status: Paid)');
    }

    // ยกเลิกการจอง (เปลี่ยนสถานะเป็น Cancelled)
    public function cancel($id)
    {
        $this->bookingModel->update($id, [
            'status' => 'cancelled',
            'is_viewed_by_admin' => 1
        ]);
        return redirect()->back()->with('success', 'ยกเลิกการจองเรียบร้อยแล้ว');
    }
}