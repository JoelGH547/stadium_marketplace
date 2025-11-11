<?php

namespace App\Controllers;

use App\Controllers\BaseController;

// 1. ⬇️ ลบ UserModel และนำเข้า 3 Models ใหม่ (บวก 2 Models ที่มีอยู่) ⬇️
use App\Models\AdminModel;
use App\Models\VendorModel;
use App\Models\CustomerModel;
use App\Models\StadiumModel;
use App\Models\CategoryModel;
// (เราไม่ใช้ UserModel แล้ว)

class DashboardController extends BaseController
{
    public function index()
    {
        // 2. ⬇️ โหลด Models ทั้ง 5 ตัว ⬇️
        $adminModel    = new AdminModel();
        $vendorModel   = new VendorModel();
        $customerModel = new CustomerModel();
        $stadiumModel  = new StadiumModel();
        $categoryModel = new CategoryModel();
        
        // (เราจะไม่ดึงข้อมูล User ที่ Login อยู่... เพราะ Filter จัดการให้แล้ว)
        // (และเราไม่รู้ว่า User มาจากตารางไหนในหน้านี้)

        // 3. ⬇️ เตรียมข้อมูลส่งไปให้ View (นับข้อมูลจาก 5 ตาราง) ⬇️
        $data = [
            'title' => 'Admin Dashboard',
            
            // --- ข้อมูลสรุป (Stats) ---
            'total_stadiums'   => $stadiumModel->countAllResults(),
            'total_categories' => $categoryModel->countAllResults(),
            
            // ⬇️ เปลี่ยนจาก $total_users (เก่า) เป็น 3 ตัวแปรใหม่ ⬇️
            'total_admins'    => $adminModel->countAllResults(),
            'total_vendors'   => $vendorModel->countAllResults(),
            'total_customers' => $customerModel->countAllResults(),
        ];

        // โหลด View ของ Dashboard (ที่อยู่ในโฟลเดอร์ admin)
        return view('admin/dashboard', $data);
    }
}