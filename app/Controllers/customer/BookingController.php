<?php

namespace App\Controllers\customer;

use App\Controllers\BaseController;
use App\Models\BookingModel;

class BookingController extends BaseController
{
    public function index()
    {
        // 1. Check Authentication
        if (! session()->get('customer_logged_in')) {
            return redirect()->to(site_url('customer/login'));
        }

        $customerId = session()->get('customer_id');

        // 2. Fetch Bookings
        $bookingModel = new BookingModel();
        $bookings = $bookingModel->getBookingsByCustomerId($customerId);

        // 3. Prepare data for View
        $data = [
            'bookings' => $bookings,
            'title'    => 'รายการจองของฉัน'
        ];

        return view('public/booking_status', $data);
    }
}
