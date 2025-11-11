<?php
namespace App\Controllers;

use App\Controllers\BaseController;
// ⬇️ --- 1. นำเข้า CustomerModel (อันเดียว) --- ⬇️
use App\Models\CustomerModel;

helper(['form']);

class AuthController extends BaseController
{
    // ⬇️ --- 2. ใช้แค่ CustomerModel --- ⬇️
    protected $customerModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
    }

    /**
     * แสดงหน้าฟอร์ม Login (สำหรับ Customer)
     */
    public function index()
    {
        return view('auth/login');
    }

    /**
     * แสดงหน้าฟอร์ม Register (สำหรับ Customer)
     */
    public function register()
    {
        return view('auth/register');
    }

    /**
     * ⬇️ --- 3. แก้ไข Register ให้บันทึกลง Customers เท่านั้น --- ⬇️
     * รับข้อมูลจากฟอร์มสมัครสมาชิก (บันทึกลงตาราง Customers)
     */
    public function processRegister()
    {
        // 1. กำหนดกฎการตรวจสอบข้อมูล (เช็กแค่ตาราง customers)
        $rules = [
            'username' => 'required|min_length[3]|max_length[50]|is_unique[customers.username]',
            'email'    => 'required|valid_email|is_unique[customers.email]',
            'password' => 'required|min_length[6]',
            'pass_confirm' => 'required|matches[password]'
        ];

        // 2. ตรวจสอบข้อมูล
        if (! $this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // 3. ถ้าข้อมูลผ่านหมด (Validation Passed)
        $data = [
            'username' => $this->request->getVar('username'),
            'email'    => $this->request->getVar('email'),
            'password_hash' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
            // (เราสามารถเพิ่ม full_name, phone_number จากฟอร์มได้ในอนาคต)
        ];

        // ✅ บันทึกลงตาราง CUSTOMERS
        if (! $this->customerModel->save($data)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->customerModel->errors());
        }

        // 4. เสร็จแล้ว redirect
        return redirect()
            ->to('/login')
            ->with('success', 'Customer account created successfully! Please login.');
    }


    /**
     * ⬇️ --- 4. แก้ไข Login ให้เช็กแค่ Customers เท่านั้น --- ⬇️
     * รับข้อมูลจากฟอร์ม Login (สำหรับ Customer)
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

        // 2. ⬇️ --- ค้นหาในตาราง CUSTOMERS (ตารางเดียว) --- ⬇️
        $user = $this->customerModel->where('email', $email)->first();
        $role = 'customer'; // กำหนด Role ตายตัว

        // 3. ตรวจสอบ User และ รหัสผ่าน
        if (! $user || ! password_verify($password, $user['password_hash'])) {
            return redirect()->back()->withInput()->with('errors', 'Invalid email or password for Customer.');
        }

        // 4. (Login สำเร็จ!) สร้าง Session
        $sessionData = [
            'user_id'      => $user['id'],
            'username'     => $user['username'],
            'email'        => $user['email'],
            'role'         => $role, // Role 'customer'
            'is_logged_in' => true
        ];
        
        session()->set($sessionData);

        // 5. ⬇️ --- Redirect ไปหน้า Customer Dashboard (ที่เดียว) --- ⬇️
        return redirect()->to('customer/dashboard');
    }
    
    /**
     * ทำลาย Session (Logout)
     */
    public function logout()
    {
        session()->destroy();
        // เด้งกลับไปหน้า login (ของ Customer)
        return redirect()->to('/login')->with('success', 'You have been logged out.');
    }

}