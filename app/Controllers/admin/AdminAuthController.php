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
     * แสดงหน้าฟอร์ม Login (สำหรับ Admin)
     */
    public function index()
    {
        // เราจะสร้าง View นี้ในขั้นตอนต่อไป
        return view('auth/login_admin'); 
    }

    /**
     * ⬇️ --- รับข้อมูลจากฟอร์ม Login (สำหรับ Admin) --- ⬇️
     */
    public function processLogin()
    {
        // 1. Validation
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        // 2. ⬇️ --- ค้นหาในตาราง ADMINS (ตารางเดียว) --- ⬇️
        $user = $this->adminModel->where('email', $email)->first();
        $role = 'admin'; // กำหนด Role ตายตัว

        // 3. ตรวจสอบ User และ รหัสผ่าน
        if (! $user || ! password_verify($password, $user['password_hash'])) {
            // ‼️ เด้งกลับไปหน้า Admin Login
            return redirect()->to('admin/login')->withInput()->with('errors', 'Invalid email or password for Admin.');
        }

        // 4. (Login สำเร็จ!) สร้าง Session
        $sessionData = [
            'user_id'      => $user['id'],
            'username'     => $user['username'],
            'email'        => $user['email'],
            'role'         => $role, // Role 'admin'
            'is_logged_in' => true
        ];
        
        session()->set($sessionData);

        // 5. ⬇️ --- Redirect ไปหน้า Admin Dashboard (ที่เดียว) --- ⬇️
        return redirect()->to('admin/dashboard');
    }
    
    /**
     * Admin Logout
     * (เราจะใช้ /logout (ตัวหลัก) หรือจะสร้าง /admin/logout ก็ได้)
     */
    public function logout()
    {
        session()->destroy();
        // เด้งกลับไปหน้า Admin Login
        return redirect()->to('admin/login')->with('success', 'You have been logged out.');
    }

}