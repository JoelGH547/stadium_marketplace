<?php
    namespace App\Controllers;

    class CustomerController extends BaseController
    {
        public function index()
        {
            $data = ['title' => 'Customer Dashboard'];
            // ⬇️ เราจะสร้าง View ใหม่ชื่อ 'customer/dashboard' ⬇️
            return view('customer/dashboard', $data);
        }
    }