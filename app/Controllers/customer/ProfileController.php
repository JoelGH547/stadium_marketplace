<?php

namespace App\Controllers\customer;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class ProfileController extends BaseController
{
    public function show()
    {
        $customerId = session('customer_id'); // เปลี่ยนตาม key ที่คุณใช้
        if (!$customerId) {
            return redirect()->to(route_to('login'));
        }

        $model    = new CustomerModel();
        $customer = $model->find($customerId);
        if (!$customer) {
            throw PageNotFoundException::forPageNotFound('ไม่พบบัญชีผู้ใช้');
        }

        $data = [
            'customer' => $customer,
            'age'      => null,
        ];

        if (!empty($customer['birthday'])) {
            try {
                $birthDate = new \DateTime($customer['birthday']);
                $today     = new \DateTime();
                $data['age'] = $today->diff($birthDate)->y;
            } catch (\Exception $e) {
                // Handle exception if date is invalid, though validation should prevent this
                $data['age'] = null;
            }
        }

        return view('public/profile', $data);
    }

    public function edit()
    {
        $customerId = session('customer_id');
        if (!$customerId) {
            return redirect()->to(route_to('login'));
        }

        $model    = new CustomerModel();
        $customer = $model->find($customerId);
        if (!$customer) {
            throw PageNotFoundException::forPageNotFound('ไม่พบบัญชีผู้ใช้');
        }

        return view('public/profile_edit', ['customer' => $customer]);
    }

    public function update()
    {
        $customerId = session('customer_id');
        if (!$customerId) {
            return redirect()->to(route_to('login'));
        }

        $model    = new CustomerModel();
        $customer = $model->find($customerId);
        if (!$customer) {
            throw PageNotFoundException::forPageNotFound('ไม่พบบัญชีผู้ใช้');
        }

        // 1. Validation Rules
        $validationRules = [
            'full_name'    => 'permit_empty|string|max_length[255]',
            'username'     => [
                'label' => 'Username',
                'rules' => "required|alpha_numeric|max_length[50]|is_unique[customers.username,id,{$customerId}]",
                'errors' => [
                    'required'      => 'กรุณากรอกชื่อผู้ใช้',
                    'alpha_numeric' => 'ชื่อผู้ใช้ต้องเป็นตัวอักษรภาษาอังกฤษหรือตัวเลขเท่านั้น',
                    'max_length'    => 'ชื่อผู้ใช้ยาวเกินไป (สูงสุด 50 ตัวอักษร)',
                    'is_unique'     => 'ขออภัย, ชื่อผู้ใช้นี้มีคนอื่นใช้แล้ว',
                ]
            ],
            'phone_number' => 'permit_empty|alpha_numeric_punct|max_length[20]',
            'gender'       => 'permit_empty|in_list[male,female,other]',
            'birthday'     => 'permit_empty|valid_date',
            'avatar'       => 'permit_empty'
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 2. Get Validated Data
        $validatedData = $this->validator->getValidated();

        $updateData = [
            'full_name'    => trim($validatedData['full_name']) !== '' ? trim($validatedData['full_name']) : $customer['full_name'],
            'username'     => $validatedData['username'],
            'phone_number' => trim($validatedData['phone_number']) ?: null,
            'gender'       => $validatedData['gender'] ?: null,
            'birthday'     => !empty($validatedData['birthday']) ? $validatedData['birthday'] : null,
        ];


        // --- จัดการรูปจาก CropperJS (data URL) ---
        $dataUrl = $this->request->getPost('avatar_cropped');
        if ($dataUrl && preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $type)) {
            $data = substr($dataUrl, strpos($dataUrl, ',') + 1);
            $data = base64_decode($data);

            if ($data !== false) {
                $ext      = strtolower($type[1]); // png, jpeg, webp
                $ext      = $ext === 'jpeg' ? 'jpg' : $ext;
                $fileName = 'cust_' . $customerId . '_' . time() . '.' . $ext;

                $relativePath = 'assets/uploads/avatars/' . $fileName;
                $fullPath     = FCPATH . $relativePath;

                if (!is_dir(dirname($fullPath))) {
                    mkdir(dirname($fullPath), 0755, true);
                }

                // Delete old avatar if it exists
                if (!empty($customer['avatar']) && file_exists(FCPATH . $customer['avatar'])) {
                    unlink(FCPATH . $customer['avatar']);
                }

                file_put_contents($fullPath, $data);
                $updateData['avatar'] = $relativePath;
            }
        }

        if ($model->update($customerId, $updateData)) {
             return redirect()
                ->to('/sport/profile')
                ->with('success', 'อัปเดตโปรไฟล์เรียบร้อยแล้ว');
        }

        return redirect()->back()->withInput()->with('error', 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล');
    }
}
