<?php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CustomerFilter implements FilterInterface
{
    /**
     * This happens BEFORE the Controller runs
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. เช็กว่า session 'role' "ไม่ใช่" 'customer'
        if (session()->get('role') !== 'customer') {
            
            // 2. ถ้าคุณไม่ใช่ Customer (เช่น เป็น 'admin' หรือ 'vendor')
            // ให้ "เด้ง" (redirect) กลับไปหน้าเดิมที่เขาอยู่
            return redirect()->back()->with('errors', 'You do not have permission to access this page.');
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