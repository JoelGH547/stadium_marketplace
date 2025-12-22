<?php
namespace App\Controllers\Owner;

use App\Controllers\BaseController;
use App\Models\VendorModel;

class Register extends BaseController
{
    public function index()
    {
        helper('thai_provinces');
        $data['provinces'] = getThaiProvinces();
        
        return view('owner/register', $data);
    }

    public function store()
    {
        $model = new VendorModel();

        $rules = [
            'username' => 'required|min_length[3]|is_unique[vendors.username]',
            'email'    => 'required|valid_email|is_unique[vendors.email]',
            'password' => 'required|min_length[6]',
            'password_confirm' => 'matches[password]',
            'vendor_name' => 'required',
            'phone_number' => 'required|min_length[8]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // --------------- Upload Files ---------------
        $profileImg   = $this->uploadImage('profile_image', 'profile');
        $idCardImg    = $this->uploadImage('id_card_image', 'idcards');
        $bankBookImg  = $this->uploadImage('bank_book_image', 'bankbooks');

        // --------------- Insert Data ---------------
        $model->insert([
            'username'      => $this->request->getPost('username'),
            'email'         => $this->request->getPost('email'),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),

            'vendor_name'   => $this->request->getPost('vendor_name'),
            'lastname'      => $this->request->getPost('lastname'),
            'gender'        => $this->request->getPost('gender'),
            'birthday'      => $this->request->getPost('birthday'),

            'address'       => $this->request->getPost('address'),
            'district'      => $this->request->getPost('district'),
            'subdistrict'   => $this->request->getPost('subdistrict'),
            'zipcode'       => $this->request->getPost('zipcode'),
            'province'      => $this->request->getPost('province'),

            'phone_number'  => $this->request->getPost('phone_number'),
            'line_id'       => $this->request->getPost('line_id'),
            'facebook_url'  => $this->request->getPost('facebook_url'),

            'tax_id'        => $this->request->getPost('tax_id'),
            'citizen_id'    => $this->request->getPost('citizen_id'),
            'bank_account'  => $this->request->getPost('bank_account'),

            'profile_image'  => $profileImg,
            'id_card_image'  => $idCardImg,
            'bank_book_image'=> $bankBookImg,

            'role'          => 'owner',
            'status'        => 'pending',
        ]);

        return redirect()->to(base_url('owner/login'))
            ->with('success', 'สมัครสมาชิกสำเร็จ! กรุณารอการอนุมัติ');
    }


    private function uploadImage($field, $folder)
    {
        $file = $this->request->getFile($field);
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('uploads/'.$folder, $newName);
            return $newName;
        }
        return null;
    }
}
