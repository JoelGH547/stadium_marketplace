<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ReviewModel;

class ReviewController extends BaseController
{
    protected $reviewModel;

    public function __construct()
    {
        $this->reviewModel = new ReviewModel();
    }

    /**
     * Display all reviews for Admin
     */
    public function index()
    {
        $data = [
            'title'   => 'จัดการรีวิวสนาม',
            'reviews' => $this->reviewModel->getReviewsWithDetails()
        ];

        return view('admin/reviews/index', $data);
    }

    /**
     * Toggle Review status (Approve/Hide)
     */
    public function toggleStatus($id)
    {
        $review = $this->reviewModel->find($id);
        if (!$review) {
            return redirect()->back()->with('error', 'ไม่พบรีวิวที่ต้องการ');
        }

        $newStatus = ($review['status'] == 'approved') ? 'hidden' : 'approved';
        $this->reviewModel->update($id, ['status' => $newStatus]);

        return redirect()->back()->with('success', 'ปรับเปลี่ยนสถานะรีวิวเรียบร้อยแล้ว');
    }

    /**
     * Delete a review
     */
    public function delete($id)
    {
        if ($this->reviewModel->delete($id)) {
            return redirect()->back()->with('success', 'ลบรีวิวเรียบร้อยแล้ว');
        }
        return redirect()->back()->with('error', 'ไม่สามารถลบรีวิวได้');
    }
}
