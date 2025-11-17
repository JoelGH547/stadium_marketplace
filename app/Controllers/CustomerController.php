<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StadiumModel; // ⬅️ 1. (เพิ่ม) นำเข้า StadiumModel

class CustomerController extends BaseController
{
    /**
     * นี่คือหน้า Dashboard/หน้าหลัก ของ "Customer"
     * (หลังจากที่ Customer ล็อกอินแล้ว)
     */
    public function index()
    {
        // 2. ⬇️ โหลด Model ⬇️
        $stadiumModel = new StadiumModel();

        // 3. ⬇️ เตรียม Data ⬇️
        $data = [
            'title' => 'Stadium Booking Dashboard',

            // 4. ⬇️ ดึง "สนามกีฬาทั้งหมด" (ที่ JOIN กับ Category แล้ว) ⬇️
            // (เราใช้ฟังก์ชัน getStadiumsWithCategory() ที่มีใน StadiumModel อยู่แล้ว)
            'stadiums' => $stadiumModel->getStadiumsWithCategory(),
        ];

        // 5. ⬇️ โหลด View (หน้า HTML) ที่เราจะสร้างในขั้นตอนต่อไป ⬇️
        return view('customer/dashboard', $data);
    }
}
