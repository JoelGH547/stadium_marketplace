<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class VendorFilter implements FilterInterface
{
    /**
     * ตรวจสอบก่อนเข้าถึง URL
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. ตรวจสอบว่ามีการล็อคอินหรือไม่
        if (!session()->get('is_logged_in')) {
            return redirect()->to(base_url('login'))->with('error', 'Please log in to access this page.');
        }

        // 2. ตรวจสอบ Role (ต้องเป็น vendor เท่านั้น)
        if (session()->get('role') !== 'vendor') {
            // ถ้าไม่ใช่ vendor ให้ redirect ไปหน้า Dashboard ของ Role ตัวเอง (admin หรือ customer)
            $role = session()->get('role') ?? 'customer'; 
            return redirect()->to(base_url($role . '/dashboard'))->with('error', 'Access denied. You are not a Vendor.');
        }
    }

    /**
     * ดำเนินการหลังประมวลผล URL (ว่างไว้)
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}