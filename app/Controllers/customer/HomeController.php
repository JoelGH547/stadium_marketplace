<?php

namespace App\Controllers\customer;

use App\Controllers\BaseController;

class HomeController extends BaseController
{
    public function index()
    {
        $data = [
            'siteName' => 'Sports Arena',
            // สามารถเปลี่ยนภาพ hero หลักได้ที่นี่ (หรือใช้ค่าเริ่มต้นใน view)
            'heroUrl'  => 'assets/images/batminton.webp',
            'title'    => 'จองสนามกีฬาออนไลน์',
        ];

        return view('public/home', $data);
    }
}
