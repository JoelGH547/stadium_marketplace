<?php
namespace App\Controllers\admin; 

use App\Controllers\BaseController;
use App\Models\AdminModel; 

helper(['form']);

class AdminAuthController extends BaseController
{
    protected $adminModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
    }

    
    public function login()
    {
        
        return view('auth/admin'); 
    }

    
    public function processLogin()
{
    $email    = $this->request->getPost('email');
    $password = $this->request->getPost('password');

    $adminModel = new \App\Models\AdminModel(); 
    $user = $adminModel->where('email', $email)->first();

    if (! $user) {
        return redirect()->back()->with('errors', 'Invalid email or password.');
    }

    if (! password_verify($password, $user['password_hash'])) {
        return redirect()->back()->with('errors', 'Invalid email or password.');
    }

    
    if (($user['role'] ?? 'admin') !== 'admin') {
        return redirect()->back()->with('errors', 'You do not have permission to access admin panel.');
    }

    
    session()->set([
        'user_id'      => $user['id'],
        'username'     => $user['username'] ?? $user['email'],
        'email'        => $user['email'],
        'role'         => 'admin',
        'is_logged_in' => true,
    ]);

    return redirect()->to('/admin/dashboard');
}
    
    
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/admin/login')->with('success', 'You have been logged out.');
    }

}