<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\VendorModel;
use App\Models\CustomerModel;
use App\Models\StadiumModel;
// use App\Models\BookingModel; // (ถ้ามีระบบจองแล้ว ให้เปิดใช้อันนี้)

class DashboardController extends BaseController
{
    protected $vendorModel;
    protected $customerModel;
    protected $stadiumModel;

    public function __construct()
    {
        $this->vendorModel   = new VendorModel();
        $this->customerModel = new CustomerModel();
        $this->stadiumModel  = new StadiumModel();
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

        // 4. (Optional) นับยอดจองวันนี้
        // $todayBookings = ... (ถ้ามี BookingModel ค่อยมาเพิ่ม)
        
        // 5. ดึงรายการล่าสุด (ตัวอย่าง: ดึง Vendor 5 คนล่าสุด)
        $recentVendors = $this->vendorModel->orderBy('id', 'DESC')->findAll(5);
        $recentCustomers = $this->customerModel->orderBy('id', 'DESC')->findAll(5);

        $data = [
            'title'           => 'Admin Dashboard',
            'pendingCount'    => $pendingVendors,
            'newCustomerCount'=> $newCustomers,
            'stadiumCount'    => $totalStadiums,
            'recentVendors'   => $recentVendors,
            'recentCustomers' => $recentCustomers
        ];

        return view('admin/dashboard', $data);
    }
}