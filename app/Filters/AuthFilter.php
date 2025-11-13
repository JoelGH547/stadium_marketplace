<?php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * This happens BEFORE the Controller runs
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. เช็กว่า session 'is_logged_in' ถูกตั้งค่าไว้หรือไม่
        if (! session()->get('is_logged_in')) {
            
            // 2. ถ้ายังไม่ Login
            // ให้ "เด้ง" (redirect) กลับไปที่หน้า /login
            return redirect()->to('/admin/login')->with('errors', 'You must be logged in to access that page.');
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