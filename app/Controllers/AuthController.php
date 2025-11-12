<?php

namespace App\Controllers;

use App\Controllers\BaseController;
// 1. ⬇️ เปลี่ยนจาก UserModel เป็น CustomerModel ⬇️
use App\Models\CustomerModel; 

helper(['form']);

class AuthController extends BaseController
{
    // นี่คือ Controller สำหรับ "Customer (ลูกค้า)" (หน้าเว็บสาธารณะ)

    /**
     * 2. ⬇️ เปลี่ยนชื่อจาก index() เป็น login() ⬇️
     * (เพื่อ "ซ่อม" Error 404 "method not found")
     * * แสดงหน้าฟอร์ม "Customer" login
     */
    public function login()
    {
        return view('auth/login');
    }

    /**
     * 3. ⬇️ "ผ่าตัด" processLogin() ⬇️
     * รับข้อมูลจากฟอร์ม Login (เฉพาะ Customer)
     */
    public function processLogin()
    {
        // 1. Validation
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // 2. ค้นหา "เฉพาะ" ในตาราง Customers
        $model = new CustomerModel();
        $user = $model->where('email', $this->request->getVar('email'))->first();

        // 3. ตรวจสอบ User และ รหัสผ่าน (เช็กคอลัมน์ 'password_hash')
        if (! $user || ! password_verify($this->request->getVar('password'), $user['password_hash'])) {
            
            return redirect()->back()->withInput()->with('errors', 'Invalid email or password.');
        }

        // 4. (Login สำเร็จ!) สร้าง Session (สำหรับ Customer)
        $sessionData = [
            'user_id'      => $user['id'],
            'username'     => $user['username'],
            'email'        => $user['email'],
            'role'         => 'customer', // ⬅️ (บังคับ Role เป็น 'customer')
            'is_logged_in' => true
        ];
        
        session()->set($sessionData);

        // 5. ส่ง Customer ไปหน้า 'customer/dashboard'
        return redirect()->to('customer/dashboard');
    }

    /**
     * แสดงหน้าฟอร์มสมัครสมาชิก (สำหรับ Customer)
     */
    public function register()
    {
        return view('auth/register');
    }

    /**
     * 4. ⬇️ "ผ่าตัด" processRegister() ⬇️
     * รับข้อมูลจากฟอร์มสมัครสมาชิก (บันทึกลงตาราง Customers)
     */
    public function processRegister()
    {
        // 1. กำหนดกฎการตรวจสอบข้อมูล
        $rules = [
             // (เช็ก is_unique "เฉพาะ" ในตาราง customers... ง่ายกว่าเดิม)
            'username'     => 'required|min_length[3]|max_length[50]|is_unique[customers.username]',
            'email'        => 'required|valid_email|is_unique[customers.email]',
            'password'     => 'required|min_length[6]',
            'pass_confirm' => 'required|matches[password]'
        ];

        // 2. ตรวจสอบข้อมูล
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 3. ถ้าข้อมูลผ่านหมด (Validation Passed)
        $model = new CustomerModel();

        $data = [
            'username'      => $this->request->getVar('username'),
            'email'         => $this->request->getVar('email'),
            'password_hash' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
            // (full_name และ phone_number จะเป็น NULL ในตอนแรก)
        ];

        // 4. ✅ บันทึกลงตาราง CUSTOMERS
        if (! $model->save($data)) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        // 5. เสร็จแล้ว redirect ไปหน้า Customer Login
        return redirect()
            ->to('/login')
            ->with('success', 'Account created successfully! Please login.');
    }
    
    /**
     * Logout (สำหรับ Customer)
     */
    public function logout()
    {
        session()->destroy();
        // เด้งกลับไปหน้า Customer login
        return redirect()->to('/login')->with('success', 'You have been logged out.');
    }
}