<?php
namespace App\Controllers\admin; // ⬅️ อยู่ในโฟลเดอร์ admin

use App\Controllers\BaseController;
use App\Models\AdminModel; // ⬅️ ใช้ AdminModel

helper(['form']);

class AdminAuthController extends BaseController
{
    protected $adminModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
    }

    /**
     * 1. ⬇️ สร้างฟังก์ชัน "login()" ⬇️
     * (นี่คือฟังก์ชันที่ "หายไป" ใน Error 404 รูปแรก)
     * * แสดงหน้าฟอร์ม Login (สำหรับ Admin)
     */
    public function login()
    {
        // (เราจะสร้าง View นี้ในขั้นตอนต่อไป)
        return view('auth/login_admin'); 
    }

    /**
     * 2. ⬇️ สร้างฟังก์ชัน "processLogin()" ⬇️
     * (รับข้อมูลจากฟอร์ม Login)
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

        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        // 2. ค้นหาในตาราง ADMINS (ตารางเดียว)
        $user = $this->adminModel->where('email', $email)->first();

        // 3. ตรวจสอบ User และ รหัสผ่าน
        if (! $user || ! password_verify($password, $user['password_hash'])) {
            return redirect()->to('admin/login')->withInput()->with('errors', 'Invalid email or password for Admin.');
        }

        // 4. (Login สำเร็จ!) สร้าง Session
        $sessionData = [
            'user_id'      => $user['id'],
            'username'     => $user['username'],
            'email'        => $user['email'],
            'role'         => 'admin', // ⬅️ บังคับ Role
            'is_logged_in' => true
        ];
        
        session()->set($sessionData);

        // 5. เด้งไปหน้า Admin Dashboard
        return redirect()->to('admin/dashboard');
    }
    
    /**
     * Admin Logout
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('admin/login')->with('success', 'You have been logged out.');
    }

}