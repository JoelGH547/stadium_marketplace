<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BookingModel;

class BookingController extends BaseController
{
    protected $bookingModel;

    public function __construct()
    {
        helper('booking_format');
        $this->bookingModel = new BookingModel();
    }

    
    public function index()
    {
        $data = [
            'title'    => 'รายการการจองทั้งหมด',
            'bookings' => $this->bookingModel->getAllBookings()
        ];
        return view('admin/bookings/index', $data);
    }

    
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

    
    public function approve($id)
    {
        
        $this->bookingModel->update($id, ['status' => 'confirmed']);
        return redirect()->back()->with('success', 'อนุมัติรายการเรียบร้อยแล้ว');
    }

    
    public function cancel($id)
    {
        $this->bookingModel->update($id, ['status' => 'cancelled']);
        return redirect()->back()->with('success', 'ยกเลิกรายการเรียบร้อยแล้ว');
    }
}