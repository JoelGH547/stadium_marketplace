<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BookingModel;

class BookingController extends BaseController
{
    protected $bookingModel;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
    }

    // แสดงหน้า index (View ที่คุณมี)
    public function index()
    {
        $data = [
            'title'    => 'รายการการจองทั้งหมด',
            'bookings' => $this->bookingModel->getAllBookings()
        ];
        return view('admin/bookings/index', $data);
    }

    // 1. ฟังก์ชันรับค่าจาก Modal (แก้ไขสถานะ)
    public function updateStatus()
    {
        $id = $this->request->getPost('booking_id');
        $status = $this->request->getPost('status');

        if ($id && $status) {
            $this->bookingModel->update($id, ['status' => $status]);
            return redirect()->to(base_url('admin/bookings'))->with('success', 'แก้ไขสถานะเรียบร้อยแล้ว');
        }
        return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการแก้ไข');
    }

    // 2. ฟังก์ชันอนุมัติด่วน (ปุ่มเขียว)
    public function approve($id)
    {
        // เปลี่ยนสถานะเป็น confirmed (หรือ paid ตามระบบคุณ)
        $this->bookingModel->update($id, ['status' => 'confirmed']);
        return redirect()->back()->with('success', 'อนุมัติรายการเรียบร้อยแล้ว');
    }

    // 3. ฟังก์ชันยกเลิกด่วน (ปุ่มแดง)
    public function cancel($id)
    {
        $this->bookingModel->update($id, ['status' => 'cancelled']);
        return redirect()->back()->with('success', 'ยกเลิกรายการเรียบร้อยแล้ว');
    }
}