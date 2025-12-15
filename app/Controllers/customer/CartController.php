<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\StadiumFieldModel;
use App\Models\VendorProductModel;

class CartController extends BaseController
{
    public function index()
    {
        // ดึงข้อมูลจาก session cart (มาจาก cart_helper.php)
        $cart = cart_get();

        $stadiumName       = (string) ($cart['stadium_name'] ?? '');
        $stadiumImage      = (string) ($cart['stadium_image'] ?? ''); 
        $stadiumId         = (int) ($cart['stadium_id'] ?? 0);
        $fieldId           = (int) ($cart['field_id'] ?? 0); // New
        $fieldBasePrice    = (float) ($cart['field_base_price'] ?? 0.0);
        
        // ถ้าไม่มีทั้งค่าเช่าสนามและไม่มีไอเทม แสดงว่าไม่มีสินค้า
        if ($fieldBasePrice <= 0 && empty($cart['items'])) {
             return view('public/cart', [
                'cartItems'  => [],
                'subtotal'   => 0.0,
                'serviceFee' => 0.0,
                'total'      => 0.0,
                'stadiumId'  => 0,
            ]);
        }

        $items = [];
        $bookingType = $cart['booking_type'] ?? 'hourly';

        // แทรกแถว "ค่าจองสนาม" เป็น item ตัวแรก
        if ($fieldBasePrice > 0) {
            // Field image is outside_image of stadium
            $fieldItem = [
                'stadium_name' => $stadiumName,
                'item_name'    => 'ค่าจองสนาม',
                'unit'         => '',
                'qty'          => 0,
                'price'        => 0.0,
                'image'        => $stadiumImage ? base_url('assets/uploads/stadiums/' . $stadiumImage) : null,
            ];

            if ($bookingType === 'daily') {
                $days = (int) ($cart['days'] ?? 1);
                $pricePerDay = (float) ($cart['field_price_per_day'] ?? 0.0);
                
                $fieldItem['item_name'] = 'ค่าจองสนาม (รายวัน)';
                $fieldItem['unit'] = 'วัน';
                $fieldItem['qty'] = $days;
                $fieldItem['price'] = $pricePerDay;
            } else {
                 $hours = (float) ($cart['hours'] ?? 0);
                 $pricePerHour = (float) ($cart['field_price_per_hour'] ?? 0.0);

                 $fieldItem['item_name'] = 'ค่าจองสนาม (รายชั่วโมง)';
                 $fieldItem['unit'] = 'ชม.';
                 $fieldItem['qty'] = $hours;
                 $fieldItem['price'] = $pricePerHour;
            }
            $items[] = $fieldItem;
        }

        // ตามด้วยไอเทมเสริม
        if (!empty($cart['items']) && is_array($cart['items'])) {
            $productModel = new VendorProductModel();
            $itemIds = array_filter(array_map(static function ($item) {
                return $item['id'] ?? null;
            }, $cart['items']));

            $productsById = [];
            if (!empty($itemIds)) {
                $products = $productModel->whereIn('id', $itemIds)->findAll();
                foreach ($products as $product) {
                    $productsById[$product['id']] = $product;
                }
            }

            foreach ($cart['items'] as $row) {
                if (!is_array($row)) {
                    continue;
                }

                $productId = $row['id'] ?? null;
                $product = $productId ? ($productsById[$productId] ?? null) : null;
                
                // Item image
                $itemImgPath = $row['image'] ?? $row['item_image'] ?? '';
                $itemImgUrl = $itemImgPath ? base_url('assets/uploads/items/' . $itemImgPath) : null;

                $items[] = [
                    'stadium_name' => $stadiumName,
                    'item_name'    => (string) ($row['name'] ?? $row['item_name'] ?? ''), 
                    'unit'         => (string) ($product['unit'] ?? 'ชิ้น'),
                    'qty'          => (int) ($row['qty'] ?? 0),
                    'price'        => (float) ($row['price'] ?? 0),
                    'image'        => $itemImgUrl,
                ];
            }
        }

        $subtotal   = (float) ($cart['subtotal'] ?? 0.0);
        $serviceFee = (float) ($cart['fee'] ?? 0.0);
        $total      = (float) ($cart['total'] ?? 0.0);

        return view('public/cart', [
            'cartItems'  => $items,
            'subtotal'   => $subtotal,
            'serviceFee' => $serviceFee,
            'total'      => $total,
            'subtotal'   => $subtotal,
            'serviceFee' => $serviceFee,
            'total'      => $total,
            'stadiumId'  => $stadiumId,
            'fieldId'    => $fieldId, // Pass fieldId
        ]);
    }
    public function add()
    {
        // รับค่าจากฟอร์ม (หน้า show)
        $stadiumId   = $this->request->getPost('stadium_id');
        $fieldId     = $this->request->getPost('field_id'); // New
        $stadiumName = $this->request->getPost('stadium_name');
        $stadiumImage = $this->request->getPost('stadium_image');

        $fieldName = '';
        if ($fieldId) {
            $fieldModel = new StadiumFieldModel();
            $field = $fieldModel->find($fieldId);
            if ($field) {
                $fieldName = $field['name'];
            }
        }

        $bookingType = $this->request->getPost('booking_type') ?: 'hourly';

        // Hourly
        $bookingDate = $this->request->getPost('booking_date');
        $timeStart   = $this->request->getPost('time_start');
        $timeEnd     = $this->request->getPost('time_end');
        $hours       = (float) $this->request->getPost('hours');
        $fieldPricePerHour = (float) $this->request->getPost('field_price_per_hour');
        
        // Daily
        $startDate = $this->request->getPost('start_date');
        $endDate   = $this->request->getPost('end_date');
        $days      = (int) $this->request->getPost('days');
        $fieldPricePerDay = (float) $this->request->getPost('field_price_per_day');

        // ค่าจองสนาม (จากฝั่งหน้าเว็บคำนวณ hours * pricePerHour OR days * pricePerDay ส่งมา)
        $fieldBasePrice   = (float) $this->request->getPost('field_base_price');

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
            'field_id'            => $fieldId, // New
            'field_name'          => $fieldName,
            'stadium_name'        => $stadiumName,
            'stadium_image'       => $stadiumImage,
            'booking_type'        => $bookingType,

            'booking_date'        => $bookingDate,
            'time_start'          => $timeStart,
            'time_end'            => $timeEnd,
            'hours'               => $hours,
            'field_price_per_hour' => $fieldPricePerHour,
            
            'start_date'          => $startDate,
            'end_date'            => $endDate,
            'days'                => $days,
            'field_price_per_day' => $fieldPricePerDay,

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
