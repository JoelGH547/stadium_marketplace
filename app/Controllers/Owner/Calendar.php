<?php

namespace App\Controllers\Owner;

use App\Controllers\BaseController;
use App\Models\BookingModel;
use App\Models\StadiumModel;

class Calendar extends BaseController
{
    protected $bookingModel;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
    }

    public function index()
    {
        // Ideally pass stadiums/subfields for filter dropdowns if needed
        return view('owner/calendar/index');
    }

    public function getEvents()
    {
        $subfieldId = $this->request->getGet('subfield_id');

        $ownerId = session()->get('owner_id'); 
        $bookings = $this->bookingModel->getAllBookings($ownerId); 

        $events = [];
        foreach ($bookings as $booking) {
            // Filter by Subfield ID if provided. Table uses 'field_id'.
            if ($subfieldId && ($booking['field_id'] ?? null) != $subfieldId) {
                continue;
            }

            $color = '#6c757d'; // Default secondary
            if ($booking['status'] == 'approved' || $booking['status'] == 'paid') $color = '#198754';
            if ($booking['status'] == 'pending') $color = '#ffc107';
            if ($booking['status'] == 'rejected' || $booking['status'] == 'cancelled') $color = '#dc3545';

            $events[] = [
                'id' => $booking['id'],
                'title' => ($booking['customer_name'] ?? 'User'),
                'start' => $booking['booking_start_time'],
                'end' => $booking['booking_end_time'],
                'color' => $color,
                'extendedProps' => [
                    'status' => $booking['status'],
                    'price' => $booking['total_price']
                ]
            ];
        }

        return $this->response->setJSON($events);
    }
}
