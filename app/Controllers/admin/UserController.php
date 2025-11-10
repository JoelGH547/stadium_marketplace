<?php

namespace App\Controllers\admin; // ⬅️ Namespace (ตัวเล็ก) ถูกต้องแล้ว

use App\Controllers\BaseController;
use App\Models\UserModel;

class UserController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'title' => 'User Management',
            'users' => $this->userModel->findAll(),
        ];
        
        return view('admin/users/index', $data); 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'title' => 'Add New User',
        ];
        return view('admin/users/create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        // 1. Validation Rules
        $rules = [
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            // ⬇️ --- 1. อัปเดต Validation Rule --- ⬇️
            'role'     => 'required|in_list[admin,vendor,customer]', 
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // 2. Hash Password
        $password = password_hash($this->request->getVar('password'), PASSWORD_DEFAULT);

        // 3. Save Data
        $this->userModel->save([
            'username' => $this->request->getVar('username'),
            'email'    => $this->request->getVar('email'),
            'role'     => $this->request->getVar('role'),
            'password' => $password,
        ]);

        return redirect()->to('admin/users')->with('success', 'User created successfully.');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id = null)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('User not found.');
        }

        $data = [
            'title' => 'Edit User: ' . $user['username'],
            'user'  => $user,
        ];
        
        return view('admin/users/edit', $data); 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id = null)
    {
        // 1. Validation Rules
        $rules = [
            'username' => "required|min_length[3]|max_length[50]|is_unique[users.username,id,{$id}]",
            'email'    => "required|valid_email|is_unique[users.email,id,{$id}]",
            // ⬇️ --- 2. อัปเดต Validation Rule --- ⬇️
            'role'     => 'required|in_list[admin,vendor,customer]',
        ];

        // (ตรวจสอบว่ามีการกรอกรหัสผ่านใหม่หรือไม่)
        if ($this->request->getVar('password')) {
            $rules['password'] = 'required|min_length[6]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // 2. Prepare data
        $data = [
            'username' => $this->request->getVar('username'),
            'email'    => $this->request->getVar('email'),
            'role'     => $this->request->getVar('role'),
        ];

        // 3. (ถ้ามีรหัสใหม่ ให้ Hash)
        if ($this->request->getVar('password')) {
            $data['password'] = password_hash($this->request->getVar('password'), PASSWORD_DEFAULT);
        }
        
        // 4. Update
        $this->userModel->update($id, $data);

        return redirect()->to('admin/users')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id = null)
    {
        // (ป้องกัน Admin ลบตัวเอง)
        $loggedInUserId = session()->get('user_id');
        if ($id == $loggedInUserId) {
            return redirect()->to('admin/users')->with('error', 'You cannot delete your own account.');
        }

        $this->userModel->delete($id);
        return redirect()->to('admin/users')->with('success', 'User deleted successfully.');
    }
}