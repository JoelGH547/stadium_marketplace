<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;

// (นำเข้า Models ทั้งหมด)
use App\Models\AdminModel;
use App\Models\VendorModel;
use App\Models\CustomerModel;
use App\Models\StadiumModel;
use App\Models\CategoryModel;
use App\Models\BookingModel; 

class DashboardController extends BaseController
{
    public function index()
    {
        // 1. ⬇️ โหลด Models (ครบ 6 ตัว) ⬇️
        $adminModel    = new AdminModel();
        $vendorModel   = new VendorModel();
        $customerModel = new CustomerModel();
        $stadiumModel  = new StadiumModel();
        $categoryModel = new CategoryModel();
        $bookingModel  = new BookingModel(); 
        
        // 2. ⬇️ Logic การนับ "Receive" (ครบ 3 ส่วน) ⬇️

        // (ส่วน 3.1: Vendor)
        $total_pending_vendors = $vendorModel->where('status', 'pending')->countAllResults();

        // (ส่วน 3.2: Booking)
        // 💡 (นี่ไงครับ! ตัวแปรที่ขาดไป) 💡
        $total_new_bookings = $bookingModel
            ->where('status', 'confirmed')
            ->where('is_viewed_by_admin', 0)
            ->countAllResults();
        $total_pending_bookings = $bookingModel
            ->where('status', 'pending')
            ->countAllResults();

        // (ส่วน 3.3: Customer) 
        $yesterday = date('Y-m-d H:i:s', strtotime('-24 hours'));
        $total_new_customers = $customerModel
            ->where('created_at >', $yesterday)
            ->countAllResults();


        // 3. ⬇️ เตรียมข้อมูลส่งไปให้ View ⬇️
        $data = [
            'title' => 'Admin Dashboard',
            
            // --- Stats เดิม ---
            'total_stadiums'   => $stadiumModel->countAllResults(),
            'total_categories' => $categoryModel->countAllResults(),
            'total_admins'    => $adminModel->countAllResults(),
            'total_vendors'   => $vendorModel->countAllResults(),
            'total_customers' => $customerModel->countAllResults(),

            // --- Stats ใหม่ (Receive) ---
            'total_pending_vendors' => $total_pending_vendors,
            'total_new_bookings' => $total_new_bookings,       // ⬅️ (ส่งตัวแปรนี้)
            'total_pending_bookings' => $total_pending_bookings, 
            'total_new_customers' => $total_new_customers, 
        ];

        return view('admin/dashboard', $data);
    }
}