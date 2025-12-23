<?php
namespace App\Controllers\Owner;

use App\Controllers\BaseController;
use App\Models\OwnerModel;

class Login extends BaseController
{
    public function index()
    {
        return view('owner/login');
    }

    public function auth()
    {
        $ownerModel = new OwnerModel();

        // login รับได้ทั้ง email หรือ username
        $loginInput = $this->request->getPost('login');
        $password   = $this->request->getPost('password');

        // ค้นหาโดย email หรือ username
        $owner = $ownerModel
            ->where('email', $loginInput)
            ->orWhere('username', $loginInput)
            ->first();

        if (! $owner) {
            return redirect()->back()->with('error', 'ไม่พบผู้ใช้นี้');
        }

        // ตรวจรหัสผ่าน (ใช้ password_hash)
        if (! password_verify($password, $owner['password_hash'])) {
            return redirect()->back()->with('error', 'รหัสผ่านไม่ถูกต้อง');
        }

        // ตรวจสถานะการอนุมัติ
        if ($owner['status'] !== 'approved') {
            return redirect()->back()->with('error', 'บัญชีของคุณอยู่ระหว่างการตรวจสอบ (Pending Approval)');
        }

        // ตั้งค่า session
        session()->set([
            'owner_id'    => $owner['id'],
            'owner_name'  => $owner['vendor_name'],  // ใช้ vendor_name แทน name
            'owner_login' => true
        ]);

        return redirect()->to(base_url('owner/dashboard'));
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('owner/login'));
    }
}
