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
        $this->adminModel    = new AdminModel();
        $this->vendorModel   = new VendorModel();
        $this->customerModel = new CustomerModel();
        helper(['form', 'url']);
    }

    private function getModel($role) {
        switch($role) {
            case 'admins':    return $this->adminModel;
            case 'vendors':   return $this->vendorModel;
            case 'customers': return $this->customerModel;
            default:          return null;
        }
    }

    // 1. READ
    public function admins() {
        return view('admin/users/index_admins', ['title' => 'จัดการผู้ดูแลระบบ (Admins)', 'users' => $this->adminModel->findAll()]);
    }
    public function vendors() {
        return view('admin/users/index_vendors', ['title' => 'จัดการเจ้าของสนาม (Vendors)', 'users' => $this->vendorModel->findAll()]);
    }
    public function customers() {
        return view('admin/users/index_customers', ['title' => 'จัดการลูกค้า (Customers)', 'users' => $this->customerModel->findAll()]);
    }

    // 2. CREATE
    public function create($role)
    {
        if (!in_array($role, ['admins', 'vendors', 'customers'])) {
            return redirect()->back()->with('error', 'Role ไม่ถูกต้อง');
        }
        return view('admin/users/create', ['title' => 'เพิ่มข้อมูล ' . ucfirst($role), 'role' => $role]);
    }

    public function store($role)
    {
        $model = $this->getModel($role);
        if (!$model) return redirect()->back();

        $argon2Options = ['memory_cost' => 1 << 17, 'time_cost' => 4, 'threads' => 2];

        $data = [
            'email'         => $this->request->getPost('email'),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_ARGON2ID, $argon2Options),
            'role'          => rtrim($role, 's')
        ];

        if ($role == 'admins') {
            $data['username'] = $this->request->getPost('username');
        } 
        elseif ($role == 'vendors') {
            $data['vendor_name']  = $this->request->getPost('vendor_name');
            $data['tax_id']       = $this->request->getPost('tax_id');
            $data['bank_account'] = $this->request->getPost('bank_account');
            $data['status']       = 'approved';
            
            // [แก้ไข] เปลี่ยน key เป็น 'phone_number' ให้ตรงกับ Model
            $data['phone_number'] = $this->request->getPost('phone'); 
        } 
        elseif ($role == 'customers') {
            $data['full_name'] = $this->request->getPost('full_name');
            
            // [แก้ไข] เปลี่ยน key เป็น 'phone_number' ให้ตรงกับ Model
            $data['phone_number'] = $this->request->getPost('phone');
        }

        if ($model->save($data)) {
            return redirect()->to(base_url("admin/users/$role"))->with('success', 'เพิ่มข้อมูลเรียบร้อย');
        } else {
            return redirect()->back()->withInput()->with('validation', $model->errors());
        }
    }

    // 3. EDIT
    public function edit($role, $id)
    {
        $model = $this->getModel($role);
        $user  = $model->find($id);
        if (!$user) return redirect()->back()->with('error', 'ไม่พบข้อมูล');

        return view('admin/users/edit', ['title' => 'แก้ไขข้อมูล ' . ucfirst($role), 'role' => $role, 'user' => $user]);
    }

    public function update($role, $id)
    {
        $model = $this->getModel($role);
        if (!$model) return redirect()->back();

        $data = ['email' => $this->request->getPost('email')];

        $newPass = $this->request->getPost('password');
        if (!empty($newPass)) {
            $argon2Options = ['memory_cost' => 1 << 17, 'time_cost' => 4, 'threads' => 2];
            $data['password_hash'] = password_hash($newPass, PASSWORD_ARGON2ID, $argon2Options);
        }

        if ($role == 'admins') {
            $data['username'] = $this->request->getPost('username');
        } 
        elseif ($role == 'vendors') {
            $data['vendor_name']  = $this->request->getPost('vendor_name');
            $data['tax_id']       = $this->request->getPost('tax_id');
            $data['bank_account'] = $this->request->getPost('bank_account');
            
            // [แก้ไข] เปลี่ยน key เป็น 'phone_number'
            $data['phone_number'] = $this->request->getPost('phone');
        } 
        elseif ($role == 'customers') {
            $data['full_name'] = $this->request->getPost('full_name');
            
            // [แก้ไข] เปลี่ยน key เป็น 'phone_number'
            $data['phone_number'] = $this->request->getPost('phone');
        }

        if ($model->update($id, $data)) {
            return redirect()->to(base_url("admin/users/$role"))->with('success', 'อัปเดตข้อมูลเรียบร้อย');
        } else {
            return redirect()->back()->withInput()->with('validation', $model->errors());
        }
    }

    // 4. DELETE & APPROVAL
    public function delete($role, $id)
    {
        $model = $this->getModel($role);
        if ($model && $model->delete($id)) {
            return redirect()->to(base_url("admin/users/$role"))->with('success', 'ลบข้อมูลเรียบร้อย');
        }
        return redirect()->back()->with('error', 'เกิดข้อผิดพลาด');
    }

    public function pendingList()
    {
        $pendingVendors = $this->vendorModel->where('status', 'pending')->findAll();
        return view('admin/users/pending_vendors', ['title' => 'อนุมัติ Vendor', 'vendors' => $pendingVendors]);
    }

    public function approveVendor($id)
    {
        $this->vendorModel->update($id, ['status' => 'approved']);
        return redirect()->back()->with('success', 'อนุมัติเรียบร้อย');
    }

    public function rejectVendor($id)
    {
        $this->vendorModel->delete($id);
        return redirect()->back()->with('success', 'ปฏิเสธเรียบร้อย');
    }
}