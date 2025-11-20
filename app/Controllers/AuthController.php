<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel; // (ใช้ CustomerModel)

helper(['form']);

class AuthController extends BaseController
{
    /**
     * แสดงหน้าฟอร์ม "Customer" login
     */
    public function login()
    {
        // ⬇️ นี่คือไฟล์ View ที่เราจะสร้างในขั้นตอนต่อไป ⬇️
        return view('auth/login'); 
    }

    /**
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
            'role'         => 'customer', // (บังคับ Role เป็น 'customer')
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
        return view('auth/register'); // (ไฟล์นี้จะเรียกหน้า Register)
    }

    /**
     * รับข้อมูลจากฟอร์มสมัครสมาชิก (บันทึกลงตาราง Customers)
     */
    public function processRegister()
    {
        // 1. กำหนดกฎการตรวจสอบข้อมูล
        $rules = [
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

        // (ใช้ Hashing ที่เราตกลงกันไว้)
        $options = ['memory_cost' => 1 << 17, 'time_cost' => 4, 'threads' => 2];
        $data = [
            'username'      => $this->request->getVar('username'),
            'email'         => $this->request->getVar('email'),
            'password_hash' => password_hash($this->request->getVar('password'), PASSWORD_ARGON2ID, $options),
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