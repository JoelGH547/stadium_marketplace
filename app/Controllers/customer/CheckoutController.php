<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;

class CheckoutController extends BaseController
{
    public function index()
    {
        // TODO: ภายหลังค่อยดึงจาก session / DB จริง
        // ตอนนี้ใช้ mock ให้ตรงกับ checkout.php
        $booking = [
            'stadium_name' => 'Sport Arena A',
            'date_label'   => 'วันเสาร์ที่ 10 พฤษภาคม 2568',
            'time_label'   => '18:00 - 20:00 น.',
        ];

        $cartItems = [
            [
                'item_name' => 'ไม้แบด Yonex Pro',
                'unit'      => 'ชม.',
                'qty'       => 1,
                'price'     => 50,
            ],
            [
                'item_name' => 'นวดนักกีฬา 60 นาที',
                'unit'      => 'ครั้ง',
                'qty'       => 1,
                'price'     => 300,
            ],
        ];

        $subtotal = array_reduce($cartItems, static function ($carry, $row) {
            return $carry + (float) $row['price'] * (int) $row['qty'];
        }, 0.0);

        $serviceFee = $subtotal * 0.05;
        $total      = $subtotal + $serviceFee;

        return view('public/checkout', [
            'booking'    => $booking,
            'cartItems'  => $cartItems,
            'subtotal'   => $subtotal,
            'serviceFee' => $serviceFee,
            'total'      => $total,
        ]);
    }
}
