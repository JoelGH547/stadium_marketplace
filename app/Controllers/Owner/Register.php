<?php
namespace App\Controllers\Owner;

use App\Controllers\BaseController;
use App\Models\OwnerModel;

class Register extends BaseController
{
    public function index()
    {
        helper('thai_provinces'); // โหลด helper
        $data['provinces'] = getThaiProvinces();
        
        return view('owner/register', $data); // ← ต้องใส่ $data ตรงนี้
    }

    public function store()
    {
        $model = new OwnerModel();

        $rules = [
            'username' => 'required|min_length[3]|is_unique[vendors.username]',
            'email'    => 'required|valid_email|is_unique[vendors.email]',
            'password' => 'required|min_length[6]',
            'password_confirm' => 'matches[password]'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $model->insert([
            'username'      => $this->request->getPost('username'),
            'vendor_name'   => $this->request->getPost('vendor_name'),
            'lastname'      => $this->request->getPost('lastname'),
            'birthday'      => $this->request->getPost('birthday'),
            'province'      => $this->request->getPost('province'),

            'email'         => $this->request->getPost('email'),
            'phone_number'  => $this->request->getPost('phone_number'),
            'tax_id'        => $this->request->getPost('tax_id'),
            'bank_account'  => $this->request->getPost('bank_account'),

            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        ]);

        return redirect()->to(base_url('owner/login'))
            ->with('success', 'สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ');
    }
}
