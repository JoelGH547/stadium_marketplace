<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel; 

helper(['form']);

class AuthController extends BaseController
{
     
     
    public function login()
    {
        
        return view('auth/login'); 
    }

    
     
     
    public function processLogin()
    {
        
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        
        $model = new CustomerModel();
        $user = $model->where('email', $this->request->getVar('email'))->first();

        
        if (! $user || ! password_verify($this->request->getVar('password'), $user['password_hash'])) {
            
            return redirect()->back()->withInput()->with('errors', 'Invalid email or password.');
        }

        
        $sessionData = [
            'user_id'      => $user['id'],
            'username'     => $user['username'],
            'email'        => $user['email'],
            'role'         => 'customer', 
            'is_logged_in' => true
        ];
        
        session()->set($sessionData);

        
        return redirect()->to('customer/dashboard');
    }

    
    
    public function register()
    {
        return view('auth/register'); 
    }

    
    public function processRegister()
    {
        
        $rules = [
             'username'     => 'required|min_length[3]|max_length[50]|is_unique[customers.username]',
             'email'        => 'required|valid_email|is_unique[customers.email]',
             'password'     => 'required|min_length[6]',
             'pass_confirm' => 'required|matches[password]'
        ];

        
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        
        $model = new CustomerModel();

        
        $options = ['memory_cost' => 1 << 17, 'time_cost' => 4, 'threads' => 2];
        $data = [
            'username'      => $this->request->getVar('username'),
            'email'         => $this->request->getVar('email'),
            'password_hash' => password_hash($this->request->getVar('password'), PASSWORD_ARGON2ID, $options),
        ];

        
        if (! $model->save($data)) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        
        return redirect()
            ->to('/login')
            ->with('success', 'Account created successfully! Please login.');
    }
    
    
    public function logout()
    {
        session()->destroy();
        
        return redirect()->to('/login')->with('success', 'You have been logged out.');
    }
}