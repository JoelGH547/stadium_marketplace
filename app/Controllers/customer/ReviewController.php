<?php

namespace App\Controllers\customer;

use App\Controllers\BaseController;
use App\Models\BookingModel;
use App\Models\StadiumReviewModel;

class ReviewController extends BaseController
{
    public function create($bookingId = null)
    {
        if (! session()->get('customer_logged_in')) {
            return redirect()->to(site_url('customer/login'));
        }

        $customerId = (int) session()->get('customer_id');
        $bookingId  = (int) ($bookingId ?? 0);

        if ($bookingId <= 0) {
            return redirect()->to(site_url('sport/booking_history'))
                ->with('error', 'ไม่พบรายการจองที่ต้องการรีวิว');
        }

        $bookingModel = new BookingModel();
        $booking = $bookingModel->where('id', $bookingId)
            ->where('customer_id', $customerId)
            ->first();

        if (! $booking) {
            return redirect()->to(site_url('sport/booking_history'))
                ->with('error', 'ไม่พบรายการจอง หรือคุณไม่มีสิทธิ์เข้าถึง');
        }

        $status = strtolower((string) ($booking['status'] ?? ''));
        $endTs  = strtotime((string) ($booking['booking_end_time'] ?? ''));

        if ($status !== 'confirmed') {
            return redirect()->to(site_url('sport/booking_history'))
                ->with('error', 'รีวิวได้เฉพาะรายการที่ยืนยันแล้วเท่านั้น');
        }

        if (! $endTs || $endTs >= time()) {
            return redirect()->to(site_url('sport/booking_history'))
                ->with('error', 'ยังไม่สามารถรีวิวได้ (ต้องหมดช่วงเวลาที่จองก่อน)');
        }

        $reviewModel = new StadiumReviewModel();
        if ($reviewModel->existsForBooking($bookingId)) {
            return redirect()->to(site_url('sport/booking_history'))
                ->with('error', 'รายการนี้ถูกรีวิวไปแล้ว');
        }

        return view('public/review_create', [
            'title'   => 'เขียนรีวิว',
            'booking' => $booking,
        ]);
    }

    public function store()
    {
        if (! session()->get('customer_logged_in')) {
            return redirect()->to(site_url('customer/login'));
        }

        $customerId = (int) session()->get('customer_id');
        $bookingId  = (int) $this->request->getPost('booking_id');

        $rating  = (int) $this->request->getPost('rating');
        $comment = trim((string) $this->request->getPost('comment'));

        if ($bookingId <= 0) {
            return redirect()->to(site_url('sport/booking_history'))
                ->with('error', 'ไม่พบรายการจองที่ต้องการรีวิว');
        }

        if ($rating < 1 || $rating > 5) {
            return redirect()->back()->withInput()->with('error', 'กรุณาให้คะแนน 1-5 ดาว');
        }

        if (mb_strlen($comment) > 2000) {
            return redirect()->back()->withInput()->with('error', 'ข้อความรีวิวต้องยาวไม่เกิน 2000 ตัวอักษร');
        }

        $bookingModel = new BookingModel();
        $booking = $bookingModel->where('id', $bookingId)
            ->where('customer_id', $customerId)
            ->first();

        if (! $booking) {
            return redirect()->to(site_url('sport/booking_history'))
                ->with('error', 'ไม่พบรายการจอง หรือคุณไม่มีสิทธิ์เข้าถึง');
        }

        $status = strtolower((string) ($booking['status'] ?? ''));
        $endTs  = strtotime((string) ($booking['booking_end_time'] ?? ''));

        if ($status !== 'confirmed') {
            return redirect()->to(site_url('sport/booking_history'))
                ->with('error', 'รีวิวได้เฉพาะรายการที่ยืนยันแล้วเท่านั้น');
        }

        if (! $endTs || $endTs >= time()) {
            return redirect()->to(site_url('sport/booking_history'))
                ->with('error', 'ยังไม่สามารถรีวิวได้ (ต้องหมดช่วงเวลาที่จองก่อน)');
        }

        $reviewModel = new StadiumReviewModel();
        if ($reviewModel->existsForBooking($bookingId)) {
            return redirect()->to(site_url('sport/booking_history'))
                ->with('error', 'รายการนี้ถูกรีวิวไปแล้ว');
        }

        $reviewModel->insert([
            'booking_id'  => $bookingId,
            'customer_id' => $customerId,
            'stadium_id'  => (int) ($booking['stadium_id'] ?? 0),
            'field_id'    => !empty($booking['field_id']) ? (int) $booking['field_id'] : null,
            'rating'      => $rating,
            'comment'     => $comment !== '' ? $comment : null,
            'status'      => 'published',
        ]);

        return redirect()->to(site_url('sport/booking_history'))
            ->with('success', 'ขอบคุณสำหรับรีวิว!');
    }
}
