<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;
use App\Models\VendorModel;
use App\Models\CustomerModel;

class UserController extends BaseController
{
    protected $adminModel;
    protected $vendorModel;
    protected $customerModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
        $this->vendorModel = new VendorModel();
        $this->customerModel = new CustomerModel();
    }

    /**
     * Display a listing of the resource.
     * (ฟังก์ชัน index เดิม... ที่แสดง User ทั้ง 3 ประเภท)
     */
    public function index()
    {
        $data = [
            'title' => 'User Management',
            'admins'    => $this->adminModel->findAll(),
            'vendors'   => $this->vendorModel->findAll(), // (อันนี้ดึงมาทั้งหมด)
            'customers' => $this->customerModel->findAll(),
        ];
        
        return view('admin/users/index', $data); 
    }

    /**
     * (ฟังก์ชัน create, store, edit, update, delete)
     * (... โค้ด CRUD เดิมทั้งหมดของคุณอยู่ที่นี่ ... )
     */
    public function create()
    {
        // ... (โค้ดเดิม) ...
        $data = ['title' => 'Add New User'];
        return view('admin/users/create', $data);
    }

    public function store()
    {
        // ... (โค้ดเดิม) ...
        // ... (โค้ด Validation) ...
        // ... (โค้ด Hashing ที่เราแก้แล้ว) ...
        // ... (โค้ด $model->save($data)) ...
        return redirect()->to('admin/users')->with('success', 'User created successfully.');
    }

    public function edit($role = null, $id = null)
    {
        // ... (โค้ดเดิม) ...
        // ... (โค้ด Switch Case) ...
        // ... (โค้ด $model->find($id)) ...
        $data = [/*...*/];
        return view('admin/users/edit', $data); 
    }

    public function update($role = null, $id = null)
    {
        // ... (โค้ดเดิม) ...
        // ... (โค้ด Validation) ...
        // ... (โค้ด Hashing ที่เราแก้แล้ว) ...
        // ... (โค้ด $model->update($id, $data)) ...
        return redirect()->to('admin/users')->with('success', 'User updated successfully.');
    }

    public function delete($role = null, $id = null)
    {
        // ... (โค้ดเดิม) ...
        // ... (โค้ด Switch Case) ...
        // ... (โค้ด $model->delete($id)) ...
        return redirect()->to('admin/users')->with('success', 'User deleted successfully.');
    }


    // --- ⬇️ (เพิ่ม) ส่วน "อนุมัติ Vendor" (ข้อ 3.1) ⬇️ ---

    /**
     * 1. (สำหรับลิงก์ 'admin/vendors/pending')
     * แสดง "รายชื่อ Vendor" ที่รออนุมัติ
     */
    public function pendingList()
    {
        $data = [
            'title' => 'Pending Vendor Approvals',
            // (ดึง "เฉพาะ" Vendor ที่มี status = 'pending')
            'vendors' => $this->vendorModel
                ->where('status', 'pending')
                ->orderBy('created_at', 'ASC')
                ->findAll(),
        ];

        // เราจะสร้าง View นี้ในขั้นตอนต่อไป
        return view('admin/users/pending_vendors', $data);
    }

    /**
     * 2. (สำหรับลิงก์ 'admin/vendors/approve/ID')
     * อัปเดตสถานะเป็น 'approved'
     */
    public function approveVendor($vendor_id = null)
    {
        $this->vendorModel->update($vendor_id, [
            'status' => 'approved'
        ]);

        return redirect()->back()->with('success', 'Vendor (ID: '.$vendor_id.') has been APPROVED.');
    }

    /**
     * 3. (สำหรับลิงก์ 'admin/vendors/reject/ID')
     * อัปเดตสถานะเป็น 'rejected'
     */
    public function rejectVendor($vendor_id = null)
    {
        $this->vendorModel->update($vendor_id, [
            'status' => 'rejected'
        ]);
        
        return redirect()->back()->with('success', 'Vendor (ID: '.$vendor_id.') has been REJECTED.');
    }
}