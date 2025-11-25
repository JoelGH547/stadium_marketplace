<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\VendorModel;
use App\Models\CustomerModel;
use App\Models\StadiumModel;
use App\Models\BookingModel; // [1] เปิดใช้งาน Model นี้

class DashboardController extends BaseController
{
    protected $vendorModel;
    protected $customerModel;
    protected $stadiumModel;
    protected $bookingModel; // [2] ประกาศตัวแปร

    public function __construct()
    {
        $this->vendorModel   = new VendorModel();
        $this->customerModel = new CustomerModel();
        $this->stadiumModel  = new StadiumModel();
        $this->bookingModel  = new BookingModel(); // [3] เรียกใช้งาน
    }

    public function index()
    {
        // 1. นับ Vendor รออนุมัติ
        $pendingVendors = $this->vendorModel->where('status', 'pending')->countAllResults();

        // 2. นับลูกค้าใหม่ (ใน 24 ชม.)
        $timeLimit = date('Y-m-d H:i:s', strtotime('-24 hours'));
        $newCustomers = $this->customerModel->where('created_at >=', $timeLimit)->countAllResults();

        // 3. นับสนามทั้งหมด
        $totalStadiums = $this->stadiumModel->countAllResults();

        // 4. [เพิ่มใหม่] นับยอดจอง "วันนี้"
        $today = date('Y-m-d');
        // ใช้ like เพื่อหาวันที่ปัจจุบันจาก timestamp (เช่น 2025-11-24 %)
        $todayBookings = $this->bookingModel->like('created_at', $today, 'after')->countAllResults();
        
        // 5. ดึงรายการล่าสุด
        $recentVendors = $this->vendorModel->orderBy('id', 'DESC')->findAll(5);
        $recentCustomers = $this->customerModel->orderBy('id', 'DESC')->findAll(5);

        $data = [
            'title'           => 'Admin Dashboard',
            'pendingCount'    => $pendingVendors,
            'newCustomerCount'=> $newCustomers,
            'stadiumCount'    => $totalStadiums,
            'todayBookings'   => $todayBookings, // [4] ส่งค่านี้ไปหน้า View
            'recentVendors'   => $recentVendors,
            'recentCustomers' => $recentCustomers
        ];

        return view('admin/dashboard', $data);
    }
}