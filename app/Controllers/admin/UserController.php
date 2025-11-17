<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;
use App\Models\VendorModel;
use App\Models\CustomerModel;

class UserController extends BaseController
{
    protected $adminModel;
    protected $vendorModel;
    protected $customerModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
        $this->vendorModel = new VendorModel();
        $this->customerModel = new CustomerModel();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'title' => 'User Management',
            'admins'    => $this->adminModel->findAll(),
            'vendors'   => $this->vendorModel->findAll(),
            'customers' => $this->customerModel->findAll(),
        ];
        
        return view('admin/users/index', $data); 
    }

    public function create()
    {
        $data = ['title' => 'Add New User'];
        return view('admin/users/create', $data);
    }

    public function store()
    {
        $role = $this->request->getVar('role');
        $model = null;
        $rules = [];

        switch ($role) {
            case 'admin':
                $model = $this->adminModel;
                $rules = [
                    'username' => 'required|min_length[3]|max_length[50]|is_unique[admins.username]',
                    'email'    => 'required|valid_email|is_unique[admins.email]',
                    'password' => 'required|min_length[6]',
                ];
                break;
            case 'vendor':
                $model = $this->vendorModel;
                $rules = [
                    'username' => 'required|min_length[3]|max_length[50]|is_unique[vendors.username]',
                    'email'    => 'required|valid_email|is_unique[vendors.email]',
                    'password' => 'required|min_length[6]',
                    'vendor_name' => 'required',
                ];
                break;
            case 'customer':
                $model = $this->customerModel;
                $rules = [
                    'username' => 'required|min_length[3]|max_length[50]|is_unique[customers.username]',
                    'email'    => 'required|valid_email|is_unique[customers.email]',
                    'password' => 'required|min_length[6]',
                ];
                break;
            default:
                return redirect()->back()->withInput()->with('errors', 'Invalid role selected.');
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // Hashing options
        $options = ['memory_cost' => 1 << 17, 'time_cost' => 4, 'threads' => 2];
        
        $data = [
            'username' => $this->request->getVar('username'),
            'email'    => $this->request->getVar('email'),
            'password_hash' => password_hash($this->request->getVar('password'), PASSWORD_ARGON2ID, $options),
        ];

        if ($role == 'vendor') {
            $data['vendor_name'] = $this->request->getVar('vendor_name');
            $data['phone_number'] = $this->request->getVar('phone_number');
            $data['tax_id'] = $this->request->getVar('tax_id');
            $data['bank_account'] = $this->request->getVar('bank_account');
            $data['status'] = 'approved'; // ถ้า Admin สร้างเอง ให้ Approve เลย
        } elseif ($role == 'customer') {
            $data['full_name'] = $this->request->getVar('full_name');
            $data['phone_number'] = $this->request->getVar('phone_number');
        }

        $model->save($data);

        return redirect()->to('admin/users')->with('success', 'User created successfully.');
    }

    public function edit($role = null, $id = null)
    {
        $user = null;
        $model = null;

        switch ($role) {
            case 'admin': $model = $this->adminModel; break;
            case 'vendor': $model = $this->vendorModel; break;
            case 'customer': $model = $this->customerModel; break;
            default:
                  throw new \CodeIgniter\Exceptions\PageNotFoundException('Invalid user role.');
        }
        
        $user = $model->find($id);
        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('User not found.');
        }

        $data = [
            'title' => 'Edit User: ' . $user['username'],
            'user'  => $user,
            'role'  => $role,
        ];
        
        return view('admin/users/edit', $data); 
    }

    public function update($role = null, $id = null)
    {
        $model = null;
        $rules = [];

        switch ($role) {
            case 'admin':
                $model = $this->adminModel;
                $rules = [
                    'username' => "required|min_length[3]|max_length[50]|is_unique[admins.username,id,{$id}]",
                    'email'    => "required|valid_email|is_unique[admins.email,id,{$id}]",
                ];
                break;
            case 'vendor':
                $model = $this->vendorModel;
                $rules = [
                    'username' => "required|min_length[3]|max_length[50]|is_unique[vendors.username,id,{$id}]",
                    'email'    => "required|valid_email|is_unique[vendors.email,id,{$id}]",
                    'vendor_name' => 'required',
                ];
                break;
            case 'customer':
                $model = $this->customerModel;
                $rules = [
                    'username' => "required|min_length[3]|max_length[50]|is_unique[customers.username,id,{$id}]",
                    'email'    => "required|valid_email|is_unique[customers.email,id,{$id}]",
                ];
                break;
            default:
                 return redirect()->back()->withInput()->with('errors', 'Invalid role.');
        }

        if ($this->request->getVar('password')) {
            $rules['password'] = 'required|min_length[6]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'username' => $this->request->getVar('username'),
            'email'    => $this->request->getVar('email'),
        ];

        if ($this->request->getVar('password')) {
            $options = ['memory_cost' => 1 << 17, 'time_cost' => 4, 'threads' => 2];
            $data['password_hash'] = password_hash($this->request->getVar('password'), PASSWORD_ARGON2ID, $options);
        }
        
        if ($role == 'vendor') {
            $data['vendor_name'] = $this->request->getVar('vendor_name');
            $data['phone_number'] = $this->request->getVar('phone_number');
            $data['tax_id'] = $this->request->getVar('tax_id');
            $data['bank_account'] = $this->request->getVar('bank_account');
        } elseif ($role == 'customer') {
            $data['full_name'] = $this->request->getVar('full_name');
            $data['phone_number'] = $this->request->getVar('phone_number');
        }
        
        $model->update($id, $data);

        return redirect()->to('admin/users')->with('success', 'User updated successfully.');
    }

    public function delete($role = null, $id = null)
    {
        $model = null;
        
        switch ($role) {
            case 'admin': 
                $model = $this->adminModel; 
                $loggedInUserId = session()->get('user_id');
                if ($id == $loggedInUserId) {
                    return redirect()->to('admin/users')->with('error', 'You cannot delete your own admin account.');
                }
                break;
            case 'vendor': $model = $this->vendorModel; break;
            case 'customer': $model = $this->customerModel; break;
            default:
                 return redirect()->to('admin/users')->with('error', 'Invalid user role.');
        }

        $model->delete($id);
        return redirect()->to('admin/users')->with('success', 'User deleted successfully.');
    }


    // --- ⬇️ (ส่วนที่เคยหายไป) ฟังก์ชันสำหรับอนุมัติ Vendor ⬇️ ---

    /**
     * 1. แสดงรายชื่อ Vendor ที่รออนุมัติ (status = 'pending')
     */
    public function pendingList()
    {
        $data = [
            'title' => 'Pending Vendor Approvals',
            'vendors' => $this->vendorModel
                ->where('status', 'pending')
                ->orderBy('created_at', 'ASC')
                ->findAll(),
        ];

        return view('admin/users/pending_vendors', $data);
    }

    /**
     * 2. อนุมัติ Vendor (เปลี่ยน status เป็น approved)
     */
    public function approveVendor($vendor_id = null)
    {
        $this->vendorModel->update($vendor_id, [
            'status' => 'approved'
        ]);

        return redirect()->back()->with('success', 'Vendor (ID: '.$vendor_id.') has been APPROVED.');
    }

    /**
     * 3. ปฏิเสธ Vendor (เปลี่ยน status เป็น rejected)
     */
    public function rejectVendor($vendor_id = null)
    {
        $this->vendorModel->update($vendor_id, [
            'status' => 'rejected'
        ]);
        
        return redirect()->back()->with('success', 'Vendor (ID: '.$vendor_id.') has been REJECTED.');
    }
}