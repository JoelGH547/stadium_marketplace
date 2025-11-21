<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CustomerFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // ถ้ายังไม่ล็อกอินลูกค้า
        if (! session('customer_logged_in')) {
            // เก็บ URL ที่พยายามเข้าไว้ เผื่ออนาคตอยาก redirect กลับ
            session()->set('intended_url', current_url());

            return redirect()
                ->to('/sport')
                ->with('auth_error', 'กรุณาเข้าสู่ระบบก่อนเข้าหน้านี้');
        }

        // ถ้าล็อกอินแล้ว → ไม่ทำอะไร ปล่อยให้ไปต่อ
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // ไม่ทำอะไรหลังจบ request
    }
}
