<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;

class CartController extends BaseController
{
    public function index()
    {
        // ดึงข้อมูลจาก session cart (มาจาก cart_helper.php)
        $cart = cart_get();

        // ถ้ายังไม่มีข้อมูลการจอง ให้ส่งค่าเปล่าไปให้ view แสดง state ว่าง
        if (!is_array($cart) || empty($cart['items'])) {
            return view('public/cart', [
                'cartItems'  => [],
                'subtotal'   => 0.0,
                'serviceFee' => 0.0,
                'total'      => 0.0,
            ]);
        }

        $stadiumName       = (string) ($cart['stadium_name'] ?? '');
        $hours             = (float) ($cart['hours'] ?? 0);
        $fieldBasePrice    = (float) ($cart['field_base_price'] ?? 0.0);
        $fieldPricePerHour = (float) ($cart['field_price_per_hour'] ?? 0.0);

        $items = [];

        // แทรกแถว "ค่าจองสนาม" เป็น item ตัวแรก ถ้ามีข้อมูล
        if ($fieldBasePrice > 0 && $hours > 0 && $fieldPricePerHour > 0) {
            $items[] = [
                'stadium_name' => $stadiumName,
                'item_name'    => 'ค่าจองสนาม',
                'unit'         => 'ชม.',
                'qty'          => $hours,
                'price'        => $fieldPricePerHour,
            ];
        }

        // ตามด้วยไอเทมเสริม
        foreach ($cart['items'] as $row) {
            if (!is_array($row)) {
                continue;
            }

            $items[] = [
                'stadium_name' => $stadiumName,
                'item_name'    => (string) ($row['item_name'] ?? ''),
                'unit'         => (string) ($row['unit'] ?? ''),
                'qty'          => (int) ($row['qty'] ?? 0),
                'price'        => (float) ($row['price'] ?? 0),
            ];
        }

        $subtotal   = (float) ($cart['subtotal'] ?? 0.0);
        $serviceFee = (float) ($cart['fee'] ?? 0.0);
        $total      = (float) ($cart['total'] ?? 0.0);

        return view('public/cart', [
            'cartItems'  => $items,
            'subtotal'   => $subtotal,
            'serviceFee' => $serviceFee,
            'total'      => $total,
        ]);
    }
}
