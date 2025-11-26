<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;

class CheckoutController extends BaseController
{
    public function index()
    {
        // ดึงข้อมูลจาก session cart
        $cart = cart_get();

        // ถ้าไม่มีตะกร้า / ยังไม่ได้จองอะไร ให้เด้งกลับไปหน้าตะกร้า
        if (!is_array($cart) || empty($cart['items'])) {
            return redirect()->to(route_to('customer.cart'));
        }

        $stadiumName       = (string) ($cart['stadium_name'] ?? '');
        $bookingDate       = (string) ($cart['booking_date'] ?? '');
        $timeStart         = (string) ($cart['time_start'] ?? '');
        $timeEnd           = (string) ($cart['time_end'] ?? '');
        $hours             = (float) ($cart['hours'] ?? 0);
        $fieldBasePrice    = (float) ($cart['field_base_price'] ?? 0.0);
        $fieldPricePerHour = (float) ($cart['field_price_per_hour'] ?? 0.0);

        // กล่องฝั่งซ้าย: สรุปการจองสนาม
        $booking = [
            'stadium_name' => $stadiumName,
            'date_label'   => $bookingDate
                ? ('วันที่ ' . $bookingDate)
                : 'ยังไม่ได้เลือกวัน',
            'time_label'   => ($timeStart && $timeEnd)
                ? ($timeStart . ' - ' . $timeEnd . ' น.')
                : 'ยังไม่ได้เลือกเวลา',
        ];

        // เตรียมรายการฝั่งขวา (เหมือน cart)
        $items = [];

        if ($fieldBasePrice > 0 && $hours > 0 && $fieldPricePerHour > 0) {
            $items[] = [
                'item_name' => 'ค่าจองสนาม',
                'unit'      => 'ชม.',
                'qty'       => $hours,
                'price'     => $fieldPricePerHour,
            ];
        }

        foreach ($cart['items'] as $row) {
            if (!is_array($row)) {
                continue;
            }

            $items[] = [
                'item_name' => (string) ($row['item_name'] ?? ''),
                'unit'      => (string) ($row['unit'] ?? ''),
                'qty'       => (int) ($row['qty'] ?? 0),
                'price'     => (float) ($row['price'] ?? 0),
            ];
        }

        $subtotal   = (float) ($cart['subtotal'] ?? 0.0);
        $serviceFee = (float) ($cart['fee'] ?? 0.0);
        $total      = (float) ($cart['total'] ?? 0.0);

        return view('public/checkout', [
            'booking'    => $booking,
            'cartItems'  => $items,
            'subtotal'   => $subtotal,
            'serviceFee' => $serviceFee,
            'total'      => $total,
        ]);
    }
}
