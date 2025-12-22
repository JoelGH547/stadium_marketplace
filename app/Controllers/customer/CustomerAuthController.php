<?php

namespace App\Controllers\customer;

use App\Controllers\BaseController;
use App\Models\CustomerModel;

helper(['form']);

class CustomerAuthController extends BaseController
{
    protected CustomerModel $customerModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
    }

    /**
     * แสดงหน้า Login (GET /login)
     */
    public function login()
    {
        // ถ้าล็อคอินแล้วไม่ต้องกลับมาหน้านี้
        if (session()->get('customer_logged_in')) {
            return redirect()->to('/sport');
        }

        return view('auth/login_customer', [
            'title' => 'เข้าสู่ระบบลูกค้า',
        ]);
    }

    /**
     * ประมวลผล Login (POST /login)
     */
    public function processLogin()
    {
        $email    = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');

        if ($email === '' || $password === '') {
            return redirect()->back()->withInput()
                ->with('auth_error', 'กรุณากรอกอีเมลและรหัสผ่าน');
        }

        $user = $this->customerModel
            ->where('email', $email)
            ->first();

        if (! $user || ! password_verify($password, $user['password_hash'] ?? '')) {
            return redirect()->back()->withInput()
                ->with('auth_error', 'อีเมลหรือรหัสผ่านไม่ถูกต้อง');
        }

        // เซต session ฝั่ง customer
        session()->set([
            'customer_id'        => $user['id'],
            'customer_email'     => $user['email'],
            'customer_name'      => $user['full_name'] ?? $user['username'] ?? $user['email'],
            'customer_username'  => $user['username'],
            'customer_logged_in' => true,
        ]);

        return redirect()->to('/sport');
    }

    /**
     * ประมวลผล Login ผ่าน AJAX สำหรับ Popup
     */
    public function ajaxLogin()
    {
        // ตรวจสอบว่าเป็น AJAX request หรือไม่
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Forbidden');
        }

        $email    = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');

        $responsePayload = ['csrf_hash' => csrf_hash()];

        if ($email === '' || $password === '') {
            $responsePayload['success'] = false;
            $responsePayload['message'] = 'กรุณากรอกอีเมลและรหัสผ่าน';

            return $this->response->setJSON($responsePayload)->setStatusCode(401);
        }

        $user = $this->customerModel
            ->where('email', $email)
            ->first();

        if (! $user || ! password_verify($password, $user['password_hash'] ?? '')) {
            $responsePayload['success'] = false;
            $responsePayload['message'] = 'อีเมลหรือรหัสผ่านไม่ถูกต้อง';

            return $this->response->setJSON($responsePayload)->setStatusCode(401);
        }

        // เซต session ฝั่ง customer
        session()->set([
            'customer_id'        => $user['id'],
            'customer_email'     => $user['email'],
            'customer_name'      => $user['full_name'] ?? $user['username'] ?? $user['email'],
            'customer_username'  => $user['username'],
            'customer_logged_in' => true,
        ]);

        $responsePayload['success'] = true;

        return $this->response->setJSON($responsePayload);
    }

    /**
     * แสดงหน้า Register (GET /register)
     */
    public function register()
    {
        if (session()->get('customer_logged_in')) {
            return redirect()->to('/sport');
        }

        return view('auth/register_customer', [
            'title' => 'สมัครสมาชิกลูกค้า',
        ]);
    }

    /**
     * ประมวลผล Register (POST /register)
     */
    public function processRegister()
    {
        $username  = trim((string) $this->request->getPost('username'));
        $email     = trim((string) $this->request->getPost('email'));
        $password  = (string) $this->request->getPost('password');
        $firstname = trim((string) $this->request->getPost('firstname'));
        $lastname  = trim((string) $this->request->getPost('lastname'));
        $phone     = trim((string) $this->request->getPost('phone'));

        if ($username === '' || $email === '' || $password === '') {
            return redirect()->back()->withInput()
                ->with('auth_error', 'กรุณากรอกข้อมูลที่จำเป็นให้ครบ');
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()
                ->with('auth_error', 'รูปแบบอีเมลไม่ถูกต้อง');
        }

        // ตรวจ email ซ้ำ
        if ($this->customerModel->where('email', $email)->first()) {
            return redirect()->back()->withInput()
                ->with('auth_error', 'อีเมลนี้มีบัญชีอยู่แล้ว');
        }

        $fullName = trim($firstname . ' ' . $lastname);

        $insertData = [
            'username'      => $username,
            'email'         => $email,
            'password_hash' => password_hash($password, PASSWORD_ARGON2ID),
            'full_name'     => $fullName !== '' ? $fullName : null,
            'phone_number'  => $phone !== '' ? $phone : null,
        ];

        $id = $this->customerModel->insert($insertData);

        session()->set([
            'customer_id'        => $id,
            'customer_email'     => $email,
            'customer_name'      => $fullName !== '' ? $fullName : $username,
            'customer_username'  => $username,
            'customer_logged_in' => true,
        ]);

        return redirect()->to('/sport');
    }

    /**
     * Logout ลูกค้า (GET /logout)
     */
    public function logout()
    {
        session()->remove([
            'customer_id',
            'customer_email',
            'customer_name',
            'customer_username',
            'customer_logged_in',
        ]);

        return redirect()->to('/sport');
    }
}
