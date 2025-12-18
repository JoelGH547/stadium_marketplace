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

    // API for FullCalendar events
    public function api()
    {
        $fieldId = $this->request->getGet('field_id');
        
        $builder = $this->bookingModel->builder();
        $builder->select('bookings.id, bookings.booking_start_time, bookings.booking_end_time, bookings.status, customers.full_name');
        $builder->join('customers', 'customers.id = bookings.customer_id', 'left');
        
        if ($fieldId) {
            $builder->where('bookings.field_id', $fieldId);
        }

        // Exclude cancelled bookings
        $builder->where('bookings.status !=', 'cancelled');

        $bookings = $builder->get()->getResultArray();

        $events = [];
        foreach ($bookings as $b) {
            $status = strtolower($b['status']);
            $color = '#3b82f6'; // Default Blue
            if ($status == 'pending') $color = '#f59e0b'; // Orange
            if ($status == 'confirmed' || $status == 'paid') $color = '#ef4444'; // Red (Busy)

            $events[] = [
                'id'    => $b['id'],
                'title' => ($b['full_name'] ?? 'Walk-in') . ' (' . ucfirst($status) . ')',
                'start' => $b['booking_start_time'],
                'end'   => $b['booking_end_time'],
                'color' => $color
            ];
        }

        return $this->response->setJSON($events);
    }
}