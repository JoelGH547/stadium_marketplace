<?php
namespace App\Controllers\Owner;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        // ป้องกันไม่ให้เข้าโดยไม่ login
        if (!session()->get('owner_login')) {
            return redirect()->to(base_url('owner/login'));
        }

        return view('owner/dashboard');
    }
}
