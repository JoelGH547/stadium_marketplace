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

        $fullName = trim((string) $this->request->getPost('full_name'));
        $phone    = trim((string) $this->request->getPost('phone_number'));
        $gender   = trim((string) $this->request->getPost('gender'));
        $birthday = $this->request->getPost('birthday');

        $updateData = [
            'full_name'    => $fullName !== '' ? $fullName : $customer['full_name'],
            'phone_number' => $phone !== '' ? $phone : null,
            'gender'       => $gender !== '' ? $gender : null,
            'birthday'     => !empty($birthday) ? $birthday : null,
        ];

        // --- จัดการรูปจาก CropperJS (data URL) ---
        $dataUrl = $this->request->getPost('avatar_cropped');
        if ($dataUrl) {
            if (preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $type)) {
                $data = substr($dataUrl, strpos($dataUrl, ',') + 1);
                $data = base64_decode($data);

                if ($data !== false) {
                    $ext      = strtolower($type[1]); // png, jpeg, webp
                    $ext      = $ext === 'jpeg' ? 'jpg' : $ext;
                    $fileName = 'cust_' . $customerId . '_' . time() . '.' . $ext;

                    $relativePath = 'uploads/avatars/' . $fileName;
                    $fullPath     = FCPATH . $relativePath;

                    if (!is_dir(dirname($fullPath))) {
                        mkdir(dirname($fullPath), 0755, true);
                    }

                    file_put_contents($fullPath, $data);
                    $updateData['avatar'] = $relativePath;
                }
            }
        }

        $model->update($customerId, $updateData);

        return redirect()
            ->to('/sport/profile')
            ->with('success', 'อัปเดตโปรไฟล์เรียบร้อยแล้ว');
    }
}
