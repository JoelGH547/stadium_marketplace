<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
// ⬇️ --- 1. นำเข้า 3 Models ใหม่ --- ⬇️
use App\Models\AdminModel;
use App\Models\VendorModel;
use App\Models\CustomerModel;

class UserController extends BaseController
{
    // ⬇️ --- 2. ประกาศ 3 Models --- ⬇️
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
     * (แก้ไข: ดึงข้อมูลจาก 3 ตาราง)
     */
    public function index()
    {
        $data = [
            'title' => 'User Management',
            // ⬇️ --- 3. ดึงข้อมูล User จาก 3 ตาราง --- ⬇️
            'admins'    => $this->adminModel->findAll(),
            'vendors'   => $this->vendorModel->findAll(),
            'customers' => $this->customerModel->findAll(),
        ];
        
        return view('admin/users/index', $data); 
    }

    /**
     * Show the form for creating a new resource.
     * (เหมือนเดิม)
     */
    public function create()
    {
        $data = [
            'title' => 'Add New User',
        ];
        return view('admin/users/create', $data);
    }

    /**
     * Store a newly created resource in storage.
     * (แก้ไข: บันทึกตาม Role ที่เลือก)
     */
    public function store()
    {
        $role = $this->request->getVar('role');
        $model = null;
        $rules = [];

        // 4. ⬇️ --- เลือก Model และ Rules ตาม Role --- ⬇️
        switch ($role) {
            case 'admin':
                $model = $this->adminModel;
                $rules = [
                    'username' => 'required|min_length[3]|max_length[50]|is_unique[admins.username]',
                    'email'    => 'required|valid_email|is_unique[admins.email]',
                    'password' => 'required|min_length[6]',
                ];
                break;
            case 'vendor':
                $model = $this->vendorModel;
                $rules = [
                    'username' => 'required|min_length[3]|max_length[50]|is_unique[vendors.username]',
                    'email'    => 'required|valid_email|is_unique[vendors.email]',
                    'password' => 'required|min_length[6]',
                    'vendor_name' => 'required', // ฟิลด์พิเศษ
                ];
                break;
            case 'customer':
                $model = $this->customerModel;
                $rules = [
                    'username' => 'required|min_length[3]|max_length[50]|is_unique[customers.username]',
                    'email'    => 'required|valid_email|is_unique[customers.email]',
                    'password' => 'required|min_length[6]',
                ];
                break;
            default:
                return redirect()->back()->withInput()->with('errors', 'Invalid role selected.');
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // 5. ⬇️ --- เตรียม Data (รวมข้อมูลพื้นฐาน และ ข้อมูลพิเศษ) --- ⬇️
        $data = [
            'username' => $this->request->getVar('username'),
            'email'    => $this->request->getVar('email'),
            'password_hash' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
        ];

        // เพิ่มข้อมูลพิเศษ (ถ้ามี)
        if ($role == 'vendor') {
            $data['vendor_name'] = $this->request->getVar('vendor_name');
            $data['phone_number'] = $this->request->getVar('phone_number');
            $data['tax_id'] = $this->request->getVar('tax_id');
            $data['bank_account'] = $this->request->getVar('bank_account');
        } elseif ($role == 'customer') {
            $data['full_name'] = $this->request->getVar('full_name');
            $data['phone_number'] = $this->request->getVar('phone_number');
        }

        // 6. ⬇️ --- บันทึกลง Model ที่ถูกต้อง --- ⬇️
        $model->save($data);

        return redirect()->to('admin/users')->with('success', 'User created successfully.');
    }


    /**
     * Show the form for editing the specified resource.
     * (แก้ไข: รับ $role และ $id)
     */
    public function edit($role = null, $id = null)
    {
        $user = null;
        $model = null;

        // 7. ⬇️ --- เลือก Model ตาม Role --- ⬇️
        switch ($role) {
            case 'admin': $model = $this->adminModel; break;
            case 'vendor': $model = $this->vendorModel; break;
            case 'customer': $model = $this->customerModel; break;
            default:
                 throw new \CodeIgniter\Exceptions\PageNotFoundException('Invalid user role.');
        }
        
        $user = $model->find($id);
        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('User not found.');
        }

        $data = [
            'title' => 'Edit User: ' . $user['username'],
            'user'  => $user,
            'role'  => $role, // ส่ง Role ไปให้ View ด้วย
        ];
        
        // 8. ⬇️ --- เรียก View 'edit' (เดี๋ยวเราต้องแก้ View นี้) --- ⬇️
        return view('admin/users/edit', $data); 
    }

    /**
     * Update the specified resource in storage.
     * (แก้ไข: รับ $role และ $id)
     */
    public function update($role = null, $id = null)
    {
        $model = null;
        $rules = [];

        // 9. ⬇️ --- เลือก Model และ Rules ตาม Role (สำหรับ is_unique) --- ⬇️
        switch ($role) {
            case 'admin':
                $model = $this->adminModel;
                $rules = [
                    'username' => "required|min_length[3]|max_length[50]|is_unique[admins.username,id,{$id}]",
                    'email'    => "required|valid_email|is_unique[admins.email,id,{$id}]",
                ];
                break;
            case 'vendor':
                $model = $this->vendorModel;
                $rules = [
                    'username' => "required|min_length[3]|max_length[50]|is_unique[vendors.username,id,{$id}]",
                    'email'    => "required|valid_email|is_unique[vendors.email,id,{$id}]",
                    'vendor_name' => 'required',
                ];
                break;
            case 'customer':
                $model = $this->customerModel;
                $rules = [
                    'username' => "required|min_length[3]|max_length[50]|is_unique[customers.username,id,{$id}]",
                    'email'    => "required|valid_email|is_unique[customers.email,id,{$id}]",
                ];
                break;
            default:
                 return redirect()->back()->withInput()->with('errors', 'Invalid role.');
        }

        // (ตรวจสอบว่ามีการกรอกรหัสผ่านใหม่หรือไม่)
        if ($this->request->getVar('password')) {
            $rules['password'] = 'required|min_length[6]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // 10. ⬇️ --- เตรียม Data (เหมือนตอน store) --- ⬇️
        $data = [
            'username' => $this->request->getVar('username'),
            'email'    => $this->request->getVar('email'),
        ];

        // (ถ้ามีรหัสใหม่ ให้ Hash)
        if ($this->request->getVar('password')) {
            $data['password_hash'] = password_hash($this->request->getVar('password'), PASSWORD_DEFAULT);
        }
        
        // เพิ่มข้อมูลพิเศษ (ถ้ามี)
        if ($role == 'vendor') {
            $data['vendor_name'] = $this->request->getVar('vendor_name');
            $data['phone_number'] = $this->request->getVar('phone_number');
            $data['tax_id'] = $this->request->getVar('tax_id');
            $data['bank_account'] = $this->request->getVar('bank_account');
        } elseif ($role == 'customer') {
            $data['full_name'] = $this->request->getVar('full_name');
            $data['phone_number'] = $this->request->getVar('phone_number');
        }
        
        // 11. ⬇️ --- อัปเดต Model ที่ถูกต้อง --- ⬇️
        $model->update($id, $data);

        return redirect()->to('admin/users')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * (แก้ไข: รับ $role และ $id)
     */
    public function delete($role = null, $id = null)
    {
        $model = null;
        
        // 12. ⬇️ --- เลือก Model ตาม Role --- ⬇️
        switch ($role) {
            case 'admin': 
                $model = $this->adminModel; 
                // (ป้องกัน Admin ลบตัวเอง)
                $loggedInUserId = session()->get('user_id');
                if ($id == $loggedInUserId) {
                    return redirect()->to('admin/users')->with('error', 'You cannot delete your own admin account.');
                }
                break;
            case 'vendor': $model = $this->vendorModel; break;
            case 'customer': $model = $this->customerModel; break;
            default:
                 return redirect()->to('admin/users')->with('error', 'Invalid user role.');
        }

        $model->delete($id);
        return redirect()->to('admin/users')->with('success', 'User deleted successfully.');
    }
}