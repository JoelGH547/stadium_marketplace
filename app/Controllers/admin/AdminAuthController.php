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
        return view('auth/admin'); 
    }

    /**
     * 2. ⬇️ สร้างฟังก์ชัน "processLogin()" ⬇️
     * (รับข้อมูลจากฟอร์ม Login)
     */
    public function processLogin()
{
    $email    = $this->request->getPost('email');
    $password = $this->request->getPost('password');

    $adminModel = new \App\Models\AdminModel(); // หรือ UserModel แล้วแต่ของจริง
    $user = $adminModel->where('email', $email)->first();

    if (! $user) {
        return redirect()->back()->with('errors', 'Invalid email or password.');
    }

    if (! password_verify($password, $user['password_hash'])) {
        return redirect()->back()->with('errors', 'Invalid email or password.');
    }

    // 🔴 จุดสำคัญ: ให้ผ่านเฉพาะ admin
    if (($user['role'] ?? 'admin') !== 'admin') {
        return redirect()->back()->with('errors', 'You do not have permission to access admin panel.');
    }

    // จากตรงนี้ไป = admin แน่ ๆ
    session()->set([
        'user_id'      => $user['id'],
        'username'     => $user['username'] ?? $user['email'],
        'email'        => $user['email'],
        'role'         => 'admin',
        'is_logged_in' => true,
    ]);

    return redirect()->to('/admin/dashboard');
}
    
    /**
     * Admin Logout
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/admin/login')->with('success', 'You have been logged out.');
    }

}