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
        // Filter bookings by the logged-in owner's ID
        // Note: 'owner_id' is set in Field.php during login/store, assuming Auth sets it too. 
        // If not set, this might return empty, which is safer than showing all.
        $ownerId = session()->get('owner_id');

        $data['bookings'] = $this->bookingModel->getAllBookings($ownerId);
        
        // Filter by status if requested
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
        $booking = $this->bookingModel->find($id);
        
        // Get related details (manual join or separate queries if not using getAllBookings logic for single item)
        // Let's reuse getAllBookings and filter, or just basic find.
        // getAllBookings returns array.
        // For detail modal, simple find is returned as JSON usually.
        
        if ($booking) {
             // Fetch additional info if needed
             return $this->response->setJSON($booking);
        }
        return $this->response->setJSON(['error' => 'Booking not found']);
    }

    public function approve($id)
    {
        $this->bookingModel->update($id, ['status' => 'approved']);
        return redirect()->back()->with('success', 'อนุมัติรายการจองสำเร็จ');
    }

    public function reject($id)
    {
        $this->bookingModel->update($id, ['status' => 'rejected']);
        return redirect()->back()->with('success', 'ปฏิเสธรายการจองเรียบร้อยแล้ว');
    }
}
