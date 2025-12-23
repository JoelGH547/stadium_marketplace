<?php

namespace App\Controllers\Owner;

use App\Controllers\BaseController;
use App\Models\BookingModel;

class Bookings extends BaseController
{
    protected $bookingModel;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
    }

    public function index()
    {
        $ownerId = session()->get('owner_id');

        $data['bookings'] = $this->bookingModel->getAllBookings($ownerId);
        
        $status = $this->request->getGet('status');
        if ($status) {
            $data['bookings'] = array_filter($data['bookings'], function($b) use ($status) {
                return $b['status'] == $status;
            });
        }

        return view('owner/bookings/index', $data);
    }

    public function detail($id)
    {
        $ownerId = session()->get('owner_id');
        $booking = $this->bookingModel->where('vendor_id', $ownerId)->find($id);
        
        if ($booking) {
             return $this->response->setJSON($booking);
        }
        return $this->response->setJSON(['error' => 'Booking not found or access denied']);
    }

    public function approve($id)
    {
        $ownerId = session()->get('owner_id');
        $booking = $this->bookingModel->where('vendor_id', $ownerId)->find($id);

        if (!$booking) {
            return redirect()->back()->with('error', 'ไม่พบรายการจองหรือไม่มีสิทธิ์ดำเนินการ');
        }

        $this->bookingModel->update($id, ['status' => 'approved']);
        return redirect()->back()->with('success', 'อนุมัติรายการจองสำเร็จ');
    }

    public function reject($id)
    {
        $ownerId = session()->get('owner_id');
        $booking = $this->bookingModel->where('vendor_id', $ownerId)->find($id);

        if (!$booking) {
            return redirect()->back()->with('error', 'ไม่พบรายการจองหรือไม่มีสิทธิ์ดำเนินการ');
        }

        $this->bookingModel->update($id, ['status' => 'rejected']);
        return redirect()->back()->with('success', 'ปฏิเสธรายการจองเรียบร้อยแล้ว');
    }
}
