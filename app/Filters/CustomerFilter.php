<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CustomerFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. ตรวจสอบว่ามีการล็อคอินหรือไม่
        if (!session()->get('is_logged_in')) {
            return redirect()->to(base_url('login'))->with('error', 'Please log in to access this page.');
        }

        // 2. ตรวจสอบ Role (ต้องเป็น customer เท่านั้น)
        if (session()->get('role') !== 'customer') {
            // ถ้าไม่ใช่ customer ให้ redirect ไปหน้า Dashboard ของ Role ตัวเอง (admin หรือ vendor)
            $role = session()->get('role') ?? 'admin'; 
            return redirect()->to(base_url($role . '/dashboard'))->with('error', 'Access denied. You are not a Customer.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}