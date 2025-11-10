<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class AdminController extends BaseController
{
    /**
     * นี่คือหน้าหลักของ Admin (เช่น /admin)
     * (เรายังไม่ได้ใช้)
     */
    public function index()
    {
        // ...
    }

    /**
     * นี่คือฟังก์ชันสำหรับ /admin/test
     * มันจะไปโหลด "ไฟล์" ที่ /app/Views/admin/test.php
     */
    public function test()
    {
        return view('admin/test');
    }
}