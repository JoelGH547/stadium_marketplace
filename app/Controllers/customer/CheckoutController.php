<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\StadiumFieldModel;
use App\Models\VendorProductModel;

class CheckoutController extends BaseController
{
    public function index()
    {
        // 1. ดึงข้อมูลจาก session cart
        $cart = cart_get();

        if (empty($cart)) {
            // ถ้าไม่มีข้อมูลในตะกร้า ให้ redirect กลับไปหน้าแรก หรือหน้าตะกร้า
            return redirect()->to(site_url('sport/cart'))->with('error', 'ไม่มีข้อมูลการจอง');
        }

        // 2. เตรียมข้อมูลสำหรับ View
        $bookingDetails = [
            'stadium_name' => $cart['stadium_name'] ?? '-',
            'field_name'   => $cart['field_name'] ?? '-',
            'date_label'   => '-',
            'time_label'   => '',
        ];

        $bookingType = $cart['booking_type'] ?? 'hourly';

        if ($bookingType === 'daily') {
            // สำหรับการจองรายวัน
            if (!empty($cart['start_date'])) {
                try {
                    $startDate = new \DateTime($cart['start_date']);
                    $bookingDetails['date_label'] = $startDate->format('d/m/Y');
                } catch (\Exception $e) {
                    $bookingDetails['date_label'] = $cart['start_date'];
                }
            }
            if (!empty($cart['end_date'])) {
                try {
                    $endDate = new \DateTime($cart['end_date']);
                    $bookingDetails['time_label'] = 'ถึง ' . $endDate->format('d/m/Y');
                } catch (\Exception $e) {
                    $bookingDetails['time_label'] = 'ถึง ' . $cart['end_date'];
                }
            }
        } else {
            // สำหรับการจองรายชั่วโมง (ของเดิม)
            if (!empty($cart['booking_date'])) {
                try {
                    $date = new \DateTime($cart['booking_date']);
                    $bookingDetails['date_label'] = 'วันที่ ' . $date->format('d/m/Y');
                } catch (\Exception $e) {
                    $bookingDetails['date_label'] = 'วันที่ ' . $cart['booking_date'];
                }
            }
            if (!empty($cart['time_start']) && !empty($cart['time_end'])) {
                $bookingDetails['time_label'] = "เวลา " . substr($cart['time_start'], 0, 5) . ' - ' . substr($cart['time_end'], 0, 5) . ' น.';
            }
        }

        // 3. เตรียมข้อมูล Items
        $items = [];
        $fieldBasePrice = (float) ($cart['field_base_price'] ?? 0.0);

        // เพิ่มค่าจองสนามเป็นรายการแรก (ถ้ามี)
        if ($fieldBasePrice > 0) {
            if ($bookingType === 'daily') {
                $items[] = [
                    'name'  => 'ค่าจองสนาม (รายวัน)',
                    'qty'   => $cart['days'] ?? 0,
                    'unit'  => 'วัน',
                    'price' => $cart['field_price_per_day'] ?? 0,
                ];
            } else {
                $items[] = [
                    'name'  => 'ค่าจองสนาม (รายชั่วโมง)',
                    'qty'   => $cart['hours'] ?? 0,
                    'unit'  => 'ชม.',
                    'price' => $cart['field_price_per_hour'] ?? 0,
                ];
            }
        }

        // เพิ่มไอเทมเสริม
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

                $items[] = [
                    'name'  => $row['name'] ?? '-',
                    'qty'   => $row['qty'] ?? 0,
                    'unit'  => $product['unit'] ?? 'ชิ้น',
                    'price' => $row['price'] ?? 0,
                ];
            }
        }

        // 4. ส่งข้อมูลไปที่ View
        return view('public/checkout', [
            'booking'    => $bookingDetails,
            'cartItems'  => $items,
            'subtotal'   => (float) ($cart['subtotal'] ?? 0.0),
            'serviceFee' => (float) ($cart['fee'] ?? 0.0),
            'total'      => (float) ($cart['total'] ?? 0.0),
        ]);
    }
}