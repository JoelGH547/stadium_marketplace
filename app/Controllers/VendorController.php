<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\VendorModel;
use Appiter\Model;

class VendorController extends BaseController
{
    protected $vendorModel;

    public function __construct()
    {
        $this->vendorModel = new VendorModel();
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

        // 3. ดึงข้อมูล Vendor ที่ล็อคอินอยู่ (เพื่อแสดงชื่อ)
        $vendor = $this->vendorModel->find($vendorId);

        // 4. เตรียมข้อมูลส่งไปให้ View
        $data = [
            'title' => 'Vendor Dashboard',
            'vendor' => $vendor,
            // 💡 เพิ่ม Stats สำหรับ Vendor ที่นี่ในอนาคต:
            // 'total_stadiums_owned' => $this->stadiumModel->where('vendor_id', $vendorId)->countAllResults(),
            // 'total_bookings' => ...
        ];

        // 5. โหลด View ของ Dashboard
        return view('vendor/dashboard', $data);
    }
    
    // 💡 สามารถเพิ่มฟังก์ชัน CRUD สำหรับ "จัดการสนาม" ของ Vendor ได้ที่นี่
    // public function manageStadiums() { ... }
}