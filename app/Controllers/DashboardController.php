<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StadiumModel; // เปลี่ยนจาก ProductModel
use App\Models\CategoryModel;
use App\Models\UserModel;

class DashboardController extends BaseController
{
    public function index()
    {
        // โหลด Models ที่จำเป็น
        $stadiumModel = new StadiumModel(); // เปลี่ยนจาก ProductModel
        $categoryModel = new CategoryModel();
        $userModel = new UserModel();
        
        // (เราจะดึงข้อมูล User ที่ Login อยู่มาด้วย)
        // $userModel = new UserModel(); // ซ้ำ ไม่จำเป็นต้องประกาศใหม่
        $user = $userModel->find(session()->get('user_id'));

        // เตรียมข้อมูลส่งไปให้ View
        $data = [
            'title' => 'Admin Dashboard',
            'user' => $user,
            
            // --- ข้อมูลสรุป (Stats) ---
            'total_stadiums' => $stadiumModel->countAllResults(), // เปลี่ยนจาก total_products
            'total_categories' => $categoryModel->countAllResults(),
            'total_users' => $userModel->countAllResults(),
            
            // (ลบคอมเมนต์ low_stock_products ออก เพราะเราไม่ใช้ stock แล้ว)
        ];

        // โหลด View ของ Dashboard (ที่อยู่ในโฟลเดอร์ admin)
        return view('admin/dashboard', $data);
    }
}