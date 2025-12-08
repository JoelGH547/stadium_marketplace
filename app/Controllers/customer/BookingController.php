<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;

class BookingController extends BaseController
{
    public function add()
    {
        // รับค่าจากฟอร์ม (หน้า show)
        $stadiumId   = $this->request->getPost('stadium_id');
        $stadiumName = $this->request->getPost('stadium_name');

        $bookingDate = $this->request->getPost('booking_date');
        $timeStart   = $this->request->getPost('time_start');
        $timeEnd     = $this->request->getPost('time_end');
        $hours       = (float) $this->request->getPost('hours');

        // ค่าจองสนาม (จากฝั่งหน้าเว็บคำนวณ hours * pricePerHour ส่งมา)
        $fieldBasePrice   = (float) $this->request->getPost('field_base_price');
        $fieldPricePerHour = (float) $this->request->getPost('field_price_per_hour');

        // ไอเทมเสริม (JSON)
        $itemsJson = $this->request->getPost('items');
        $itemsArr  = json_decode((string) $itemsJson, true) ?? [];

        // ยอดรวมเฉพาะไอเทม
        $itemsSubtotal = array_reduce($itemsArr, static function ($carry, $row) {
            $price = isset($row['price']) ? (float) $row['price'] : 0.0;
            $qty   = isset($row['qty'])   ? (int) $row['qty']   : 0;
            return $carry + ($price * $qty);
        }, 0.0);

        // subtotal = ค่าจองสนาม + ไอเทม
        $subtotal = $fieldBasePrice + $itemsSubtotal;
        $fee      = $subtotal * 0.05;
        $total    = $subtotal + $fee;

        cart_set_booking([
            'stadium_id'          => $stadiumId,
            'stadium_name'        => $stadiumName,

            'booking_date'        => $bookingDate,
            'time_start'          => $timeStart,
            'time_end'            => $timeEnd,
            'hours'               => $hours,

            'field_price_per_hour' => $fieldPricePerHour,
            'field_base_price'     => $fieldBasePrice,
            'items_subtotal'       => $itemsSubtotal,

            'items'    => $itemsArr,
            'subtotal' => $subtotal,
            'fee'      => $fee,
            'total'    => $total,
        ]);

        return redirect()->to(site_url('sport/cart'));
    }
}
