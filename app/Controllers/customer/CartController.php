<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;

class CartController extends BaseController
{
    public function index()
    {
        // TODO: ภายหลังค่อยดึงจาก session / DB จริง
        // ตอนนี้ใช้ mock ให้ตรงกับ cart.php ที่เราเตรียมไว้
        $cartItems = [
            [
                'stadium_name' => 'Sport Arena A',
                'item_name'    => 'ไม้แบด Yonex Pro',
                'unit'         => 'ชม.',
                'qty'          => 1,
                'price'        => 50,
            ],
            [
                'stadium_name' => 'Sport Arena A',
                'item_name'    => 'นวดนักกีฬา 60 นาที',
                'unit'         => 'ครั้ง',
                'qty'          => 1,
                'price'        => 300,
            ],
        ];

        $subtotal = array_reduce($cartItems, static function ($carry, $row) {
            return $carry + (float) $row['price'] * (int) $row['qty'];
        }, 0.0);

        $serviceFee = $subtotal * 0.05;
        $total      = $subtotal + $serviceFee;

        return view('public/cart', [
            'cartItems'  => $cartItems,
            'subtotal'   => $subtotal,
            'serviceFee' => $serviceFee,
            'total'      => $total,
        ]);
    }
}
