<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StadiumModel; 

class CustomerController extends BaseController
{
    
    public function index()
    {
        $stadiumModel = new StadiumModel();
        $bookingModel = new \App\Models\BookingModel();

        $userId = session()->get('user_id');

        $data = [
            'title'    => 'Stadium Booking Dashboard',
            'stadiums' => $stadiumModel->getStadiumsWithCategory(),
            'myBookings' => $bookingModel->where('customer_id', $userId)
                                         ->orderBy('created_at', 'DESC')
                                         ->limit(5)
                                         ->findAll()
        ];

        // Enrich bookings with stadium and field names
        $reviewModel = new \App\Models\ReviewModel();
        foreach ($data['myBookings'] as &$b) {
            $field = (new \App\Models\StadiumFieldModel())->find($b['field_id']);
            $stadium = $stadiumModel->find($b['stadium_id']);
            $b['field_name'] = $field['name'] ?? 'N/A';
            $b['stadium_name'] = $stadium['name'] ?? 'N/A';
            
            // Check if already reviewed
            $b['is_reviewed'] = $reviewModel->where('booking_id', $b['id'])->countAllResults() > 0;

            // Time Logic: Only allow review if booking session has ended
            $endTime = strtotime($b['booking_end_time']);
            $now = time();
            $b['can_review'] = ($now > $endTime);
        }

        return view('customer/dashboard', $data);
    }
}
