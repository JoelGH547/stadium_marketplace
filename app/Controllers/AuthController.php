<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

helper(['form']);

class AuthController extends BaseController
{
    public function index()
    {
        // หน้านี้จะไว้สำหรับหน้า login ภายหลัง
        return view('auth/login');
    }

    /**
     * แสดงหน้าฟอร์มสมัครสมาชิก
     */
    public function register()
    {
        return view('auth/register');
    }

    /**
     * รับข้อมูลจากฟอร์มสมัครสมาชิก
     * (โค้ดส่วนนี้ของคุณ... ทำงานได้ดีแล้ว!)
     */
    public function processRegister()
    {
        // 1️⃣ กำหนดกฎการตรวจสอบข้อมูล
        $rules = [
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'pass_confirm' => 'required|matches[password]'
        ];

        // 2️⃣ ตรวจสอบข้อมูล
        if (! $this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // 3️⃣ ถ้าข้อมูลผ่านหมด (Validation Passed)
        $model = new UserModel();

        $data = [
            'username' => $this->request->getVar('username'),
            'email'    => $this->request->getVar('email'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
            'role'     => 'staff', // ใส่ค่า default ไปด้วยกันพลาด
        ];

        // ✅ ใช้ save()
        if (! $model->save($data)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $model->errors());
        }

        // 4️⃣ เสร็จแล้ว redirect
        return redirect()
            ->to('/register')
            ->with('success', 'Account created successfully! Please login.');
    }


    /**
     * ⬇️ (โค้ดใหม่) ⬇️
     * รับข้อมูลจากฟอร์ม Login
     */
    public function processLogin()
    {
        // 1. Validation (กฎง่ายๆ: แค่ต้องกรอกมา)
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 2. ค้นหา User ด้วย Email
        $model = new UserModel();
        $user = $model->where('email', $this->request->getVar('email'))->first();

        // 3. ตรวจสอบ User และ รหัสผ่าน
        //    (ถ้า $user ไม่มีอยู่จริง "หรือ" รหัสผ่านไม่ตรง)
        if (! $user || ! password_verify($this->request->getVar('password'), $user['password'])) {
            
            // เด้งกลับไปหน้า login พร้อม Error
            return redirect()->back()->withInput()->with('errors', 'Invalid email or password.');
        }

        // 4. (Login สำเร็จ!) สร้าง Session
        $sessionData = [
            'user_id'      => $user['id'],
            'username'     => $user['username'],
            'role'         => $user['role'],
            'is_logged_in' => true
        ];
        
        // (เราใช้ $this->session ที่มาจาก BaseController)
        session()->set($sessionData);


        // 5. ส่งผู้ใช้ไปหน้า Dashboard
        // (ซึ่งเรายังไม่ได้สร้าง... นี่คือขั้นตอนต่อไป)
        return redirect()->to('/dashboard');
    }
    
    public function logout()
    {
        
        $session = session();
        $session->remove('user_id');
        $session->remove('username');
        $session->remove('role');
        $session->remove('is_logged_in');

        // เด้งกลับไปหน้า login
        return redirect()->to('/login')->with('success', 'You have been logged out.');
    }

}