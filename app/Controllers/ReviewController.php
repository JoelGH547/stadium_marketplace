<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ReviewModel;
use App\Models\BookingModel;

class ReviewController extends BaseController
{
    protected $reviewModel;
    protected $bookingModel;

    public function __construct()
    {
        $this->reviewModel = new ReviewModel();
        $this->bookingModel = new BookingModel();
    }

    /**
     * Handle review submission from Customer
     */
    public function submit()
    {
        $rules = [
            'booking_id' => 'required|integer',
            'rating'     => 'required|integer|greater_than[0]|less_than[6]',
            'comment'    => 'required|min_length[5]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error', 'errors' => $this->validator->getErrors()]);
        }

        $bookingId  = $this->request->getPost('booking_id');
        $rating     = $this->request->getPost('rating');
        $comment    = $this->request->getPost('comment');

        // Verify booking exists and belongs to the user
        $booking = $this->bookingModel->find($bookingId);
        if (!$booking || $booking['customer_id'] != session()->get('user_id')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบข้อมูลการจอง หรือสิทธิ์ไม่ถูกต้อง']);
        }

        // Time Logic: Only allow review if booking session has ended
        $endTime = strtotime($booking['booking_end_time']);
        if (time() <= $endTime) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'คุณจะสามารถรีวิวได้หลังจากเวลาใช้งานสนามเสร็จสิ้นลงแล้วเท่านั้น']);
        }

        // Check if already reviewed
        $exists = $this->reviewModel->where('booking_id', $bookingId)->first();
        if ($exists) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'คุณได้รีวิวรายการนี้ไปแล้ว']);
        }

        $data = [
            'booking_id'  => $bookingId,
            'customer_id' => session()->get('user_id'),
            'stadium_id'  => $booking['stadium_id'],
            'rating'      => $rating,
            'comment'     => $comment,
            'status'      => 'approved' // Default to approved, Admin can hide later
        ];

        if ($this->reviewModel->insert($data)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'ขอบคุณสำหรับการรีวิวครับ!']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่สามารถบันทึกรีวิวได้']);
    }
}
