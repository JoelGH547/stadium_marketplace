<?php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    /**
     * This happens BEFORE the Controller runs
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. เช็กว่า session 'role' "ไม่ใช่" 'admin'
        if (session()->get('role') !== 'admin') {
            
            // 2. ถ้าคุณไม่ใช่แอดมิน (เช่น เป็น 'staff')
            // ให้ "เด้ง" (redirect) กลับไปที่หน้า Dashboard หลัก
            // (เราไม่เตะไปหน้า login เพราะคุณ "login แล้ว" แต่แค่ "ไม่มีสิทธิ์")
            return redirect()->to('/admin/login')->with('errors', 'You do not have permission to access this page.');
        }
    }

    /**
     * This happens AFTER the Controller runs
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // (เราไม่จำเป็นต้องทำอะไรตรงนี้)
    }
}