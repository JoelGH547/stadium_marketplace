<?php

namespace App\Controllers\Customer;

use App\Models\BookingModel;
use App\Models\StadiumModel;
use CodeIgniter\I18n\Time;

use App\Controllers\BaseController;
use App\Models\StadiumFieldModel;
use App\Models\VendorProductModel;
use App\Libraries\SlipGenerator;

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

    public function confirm()
    {
        helper(['url', 'cart']);

        $customerId = session()->get('customer_id');
        if (! $customerId) {
            return redirect()->to(site_url('sport/login'));
        }

        $cart = cart_get();
        if (! is_array($cart) || empty($cart['stadium_id']) || empty($cart['field_id'])) {
            return redirect()->to(site_url('sport/checkout'))->with('error', 'ไม่พบข้อมูลการจอง กรุณาลองใหม่อีกครั้ง');
        }

        $stadiumId = (int) $cart['stadium_id'];
        $fieldId   = (int) $cart['field_id'];

        // หา vendor_id จาก stadiums
        $stadiumModel = new StadiumModel();
        $stadium = $stadiumModel->find($stadiumId);
        if (! $stadium) {
            return redirect()->to(site_url('sport/checkout'))->with('error', 'ไม่พบข้อมูลสนาม กรุณาลองใหม่อีกครั้ง');
        }
        $vendorId = (int) ($stadium['vendor_id'] ?? 0);

        // รองรับทั้งรายชั่วโมง / รายวัน ตาม booking_type ที่เก็บไว้ใน cart
        $bookingType = (string) ($cart['booking_type'] ?? 'hourly');

        $startAt = null;
        $endAt   = null;

        if ($bookingType === 'daily') {
            $startDate = (string) ($cart['start_date'] ?? '');
            $endDate   = (string) ($cart['end_date'] ?? '');

            if ($startDate === '' || $endDate === '') {
                return redirect()->to(site_url('sport/checkout'))->with('error', 'ข้อมูลวันจองไม่ครบ กรุณาลองใหม่อีกครั้ง');
            }

            $startAt = Time::parse($startDate . ' 00:00:00');
            $endAt   = Time::parse($endDate . ' 23:59:59');
        } else {
            $bookingDate = (string) ($cart['booking_date'] ?? '');
            $timeStart   = (string) ($cart['time_start'] ?? '');
            $timeEnd     = (string) ($cart['time_end'] ?? '');

            if ($bookingDate === '' || $timeStart === '' || $timeEnd === '') {
                return redirect()->to(site_url('sport/checkout'))->with('error', 'ข้อมูลเวลาจองไม่ครบ กรุณาลองใหม่อีกครั้ง');
            }

            $startAt = Time::parse($bookingDate . ' ' . $timeStart . ':00');
            $endAt   = Time::parse($bookingDate . ' ' . $timeEnd . ':00');
        }

        $totalPrice = (float) ($cart['total'] ?? 0);

        // -------------------------
        // 1) ข้อมูลผู้จอง (จากฟอร์ม checkout)
        // -------------------------
        $customerName  = trim((string) $this->request->getPost('customer_name'));
        $customerPhone = trim((string) $this->request->getPost('customer_phone'));
        $customerEmail = trim((string) $this->request->getPost('customer_email'));
        $customerNote  = trim((string) $this->request->getPost('customer_note'));

        if ($customerName === '' || $customerPhone === '') {
            return redirect()->to(site_url('sport/checkout'))->with('error', 'กรุณากรอกชื่อ-นามสกุล และเบอร์โทรศัพท์');
        }
        if ($customerEmail === '') $customerEmail = 'ระบุไม่มี';
        if ($customerNote === '')  $customerNote  = 'ระบุไม่มี';

        // -------------------------
        // 2) สร้างสลิปเป็นรูป และบันทึกชื่อไฟล์ลง slip_image
        // -------------------------
        $slipFilename = null;

        $lines = [];

        // ส่วนข้อมูลผู้จอง
        $lines[] = ['type' => 'section', 'text' => 'ข้อมูลผู้จอง'];
        $lines[] = ['type' => 'kv', 'k' => 'ชื่อ-นามสกุลผู้จอง', 'v' => $customerName];
        $lines[] = ['type' => 'kv', 'k' => 'เบอร์โทรศัพท์', 'v' => $customerPhone];
        $lines[] = ['type' => 'kv', 'k' => 'อีเมล', 'v' => $customerEmail];
        $lines[] = ['type' => 'kv', 'k' => 'หมายเหตุเพิ่มเติม', 'v' => $customerNote];
        $lines[] = ['type' => 'hr'];

        // ส่วนรายละเอียดการจอง
        $lines[] = ['type' => 'section', 'text' => 'รายละเอียดการจอง'];
        $lines[] = ['type' => 'kv', 'k' => 'สนาม', 'v' => (string)($cart['stadium_name'] ?? '-')];
        $lines[] = ['type' => 'kv', 'k' => 'สนามย่อย', 'v' => (string)($cart['field_name'] ?? '-')];

        if ($bookingType === 'daily') {
            $startDate = (string) ($cart['start_date'] ?? '');
            $endDate   = (string) ($cart['end_date'] ?? '');
            $dateLabel = $startDate !== '' ? date('d/m/Y', strtotime($startDate)) : '-';
            $endLabel  = $endDate   !== '' ? date('d/m/Y', strtotime($endDate))   : '-';
            $lines[] = ['type' => 'kv', 'k' => 'วันเวลา', 'v' => 'วันที่ ' . $dateLabel . ' ถึง ' . $endLabel];
        } else {
            $bookingDate = (string) ($cart['booking_date'] ?? '');
            $timeStart   = (string) ($cart['time_start'] ?? '');
            $timeEnd     = (string) ($cart['time_end'] ?? '');
            $dateLabel   = $bookingDate !== '' ? date('d/m/Y', strtotime($bookingDate)) : '-';
            $timeLabel   = ($timeStart && $timeEnd) ? ('เวลา ' . $timeStart . ' - ' . $timeEnd . ' น.') : '-';
            $lines[] = ['type' => 'kv', 'k' => 'วันเวลา', 'v' => 'วันที่ ' . $dateLabel . '  ' . $timeLabel];
        }

        $lines[] = ['type' => 'hr'];

        // รายการบริการและไอเทม
        $lines[] = ['type' => 'section', 'text' => 'รายการบริการและไอเทม'];

        // ค่าจองสนาม (เป็นบรรทัดแรก)
        $fieldBasePrice = (float) ($cart['field_base_price'] ?? 0.0);
        if ($bookingType === 'daily') {
            $days = (int) ($cart['days'] ?? 1);
            $left = 'ค่าจองสนาม (รายวัน) x' . $days . ' วัน';
        } else {
            $hours = (float) ($cart['hours'] ?? 0);
            // แสดงชั่วโมงเป็นจำนวนเต็มถ้าเป็น .0
            $hoursLabel = (floor($hours) == $hours) ? (string) ((int)$hours) : (string) $hours;
            $left = 'ค่าจองสนาม (รายชั่วโมง) x' . $hoursLabel . ' ชม.';
        }
        $lines[] = ['type' => 'row', 'left' => $left, 'right' => number_format($fieldBasePrice, 2) . '฿'];

        // ไอเทมเสริม
        $items = $cart['items'] ?? [];
        if (is_array($items)) {
            foreach ($items as $it) {
                $name = (string)($it['name'] ?? $it['item_name'] ?? 'ไอเทม');
                $qty  = (int)($it['qty'] ?? 0);
                $price = (float)($it['price'] ?? 0.0);
                if ($qty <= 0) continue;

                $unit = (string)($it['unit'] ?? '');
                $unitLabel = $unit !== '' ? (' ' . $unit) : '';
                $leftLine  = $name . ' x' . $qty . $unitLabel;
                $lines[] = ['type' => 'row', 'left' => $leftLine, 'right' => number_format($price * $qty, 2) . '฿'];
            }
        }

        $lines[] = ['type' => 'hr'];

        $subtotal = (float) ($cart['subtotal'] ?? 0.0);
        $fee      = (float) ($cart['fee'] ?? 0.0);
        $total    = (float) ($cart['total'] ?? 0.0);

        $lines[] = ['type' => 'row', 'left' => 'ยอดรวมบริการและไอเทม', 'right' => number_format($subtotal, 2) . '฿', 'bold' => true];
        $lines[] = ['type' => 'row', 'left' => 'ค่าบริการแพลตฟอร์ม (5%)', 'right' => number_format($fee, 2) . '฿'];
        $lines[] = ['type' => 'row', 'left' => 'ยอดชำระทั้งหมด', 'right' => number_format($total, 2) . '฿', 'bold' => true];

        // สร้างสลิป
        $dbSlipPath = null;
        try {
            $gen = new SlipGenerator();
            $slipFilename = $gen->generate([
                'title' => 'สลิปการจองสนาม',
                'meta'  => 'สร้างเมื่อ ' . date('d/m/Y H:i') . ' น.',
                'lines' => $lines,
            ]);

            if ($slipFilename) {
                // ถ้าสร้างไฟล์สำเร็จ ให้เตรียม path สำหรับเก็บลง DB
                $dbSlipPath = 'assets/uploads/slips/' . $slipFilename;
            } else {
                // This case should ideally not be reached if SlipGenerator throws exceptions
                log_message('error', 'SlipGenerator returned null without throwing an exception.');
                return redirect()->to(site_url('sport/checkout'))->with('error', 'ไม่สามารถสร้างสลิปได้เนื่องจากปัญหาที่ไม่ทราบสาเหตุ');
            }
        } catch (\Exception $e) {
            log_message('error', '[SlipGenerator] ' . $e->getMessage());
            // ส่ง error กลับไปให้ user เห็นเลย
            return redirect()->to(site_url('sport/checkout'))->with('error', 'ไม่สามารถสร้างสลิปการจองได้: ' . $e->getMessage());
        }

        $bookingModel = new BookingModel();
        $bookingModel->insert([
            'customer_id'        => (int) $customerId,
            'stadium_id'         => $stadiumId,
            'field_id'           => $fieldId,
            'vendor_id'          => $vendorId ?: null,
            'booking_start_time' => $startAt ? $startAt->toDateTimeString() : null,
            'booking_end_time'   => $endAt ? $endAt->toDateTimeString() : null,
            'total_price'        => $totalPrice,
            'status'             => 'pending',
            'slip_image'         => $dbSlipPath,
        ]);

        cart_reset();

        return view('public/booking_success', [
            'redirectUrl' => site_url('sport'),
        ]);
    }
}
