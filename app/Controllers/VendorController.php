<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\VendorModel;
use App\Models\StadiumModel; // ⬅️ 1. นำเข้า StadiumModel

class VendorController extends BaseController
{
    protected $vendorModel;
    protected $stadiumModel; // ⬅️ 2. ประกาศ StadiumModel

    public function __construct()
    {
        $this->vendorModel = new VendorModel();
        $this->stadiumModel = new StadiumModel(); // ⬅️ 3. สร้าง Instance
    }

    public function index()
    {
        // 1. ดึง ID และ Role จาก Session
        $vendorId = session()->get('user_id');
        $vendorRole = session()->get('role');

        // 2. ตรวจสอบให้แน่ใจว่าเป็น Vendor (กันเหนียว)
        if ($vendorRole !== 'vendor') {
            return redirect()->to(base_url('login'))->with('error', 'Authentication failure.');
        }

        // 3. ดึงข้อมูล Vendor ที่ล็อคอินอยู่
        $vendor = $this->vendorModel->find($vendorId);
        
        // 4. ⬇️ (อัปเกรด) นับจำนวนสนามที่เป็นของ Vendor คนนี้ ⬇️
        // (เราใช้ฟังก์ชันใหม่ที่เราเพิ่งสร้างใน StadiumModel)
        $myStadiums = $this->stadiumModel->getStadiumsByVendor($vendorId);
        $totalStadiumsOwned = count($myStadiums);

        // 5. เตรียมข้อมูลส่งไปให้ View
        $data = [
            'title' => 'Vendor Dashboard',
            'vendor' => $vendor,
            'total_stadiums_owned' => $totalStadiumsOwned, // ⬅️ 5. ส่งตัวแปร "นับ" ไป
            // 'total_bookings' => ... (อนาคต)
        ];

        // 6. โหลด View ของ Dashboard
        return view('vendor/dashboard', $data);
    }
    
    // (เราจะสร้าง Controller ใหม่สำหรับ CRUD สนาม... ไม่ทำในนี้)
}