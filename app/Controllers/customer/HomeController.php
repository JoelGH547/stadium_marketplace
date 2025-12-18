<?php

namespace App\Controllers\customer;

use App\Controllers\BaseController;
use App\Models\StadiumModel;
use App\Models\CategoryModel;
use App\Models\StadiumFieldModel;
use App\Models\StadiumReviewModel;
use App\Models\CustomerFavoriteModel;

class HomeController extends BaseController
{
    public function index()
    {
        $stadiumModel = new StadiumModel();

        // ดึงสนามมาใช้ในการ์ด section ด้านล่าง + ดึงชื่อประเภท + emoji จาก categories
        $venueCards = $stadiumModel
            ->select('stadiums.*, categories.name AS category_name, categories.emoji AS category_emoji')
            ->join('categories', 'categories.id = stadiums.category_id', 'left')
            ->orderBy('stadiums.id', 'DESC')
            ->findAll();

        // Rating summary (รวมคะแนนของสนามย่อยทั้งหมดเป็นของสนามหลัก)
        $reviewModel = new StadiumReviewModel();
        $stadiumIds  = array_map(static fn($x) => (int)($x['id'] ?? 0), $venueCards);
        $summaries   = $reviewModel->getSummariesForStadiumIds($stadiumIds);

        // ดึงราคาจาก StadiumFieldModel
        $fieldModel = new StadiumFieldModel();
        // หา field ทั้งหมดที่อยู่ใน stadiumIds นี้
        $allFields = [];
        if (!empty($stadiumIds)) {
            $allFields = $fieldModel
                ->select('stadium_id, price, price_daily')
                ->whereIn('stadium_id', $stadiumIds)
                ->findAll();
        }

        // Group fields by stadium_id
        $stadiumPrices = [];
        foreach ($allFields as $f) {
            $sid = $f['stadium_id'];
            if (!isset($stadiumPrices[$sid])) {
                $stadiumPrices[$sid] = [
                    'hourly' => [],
                    'daily'  => []
                ];
            }
            // เก็บราคา hourly
            if (!empty($f['price']) && $f['price'] > 0) {
                $stadiumPrices[$sid]['hourly'][] = (float)$f['price'];
            }
            // เก็บราคา daily
            if (!empty($f['price_daily']) && $f['price_daily'] > 0) {
                $stadiumPrices[$sid]['daily'][] = (float)$f['price_daily'];
            }
        }

        foreach ($venueCards as &$v) {
            // ชื่อประเภท
            $catName  = (string)($v['category_name']  ?? '');
            $catEmoji = (string)($v['category_emoji'] ?? '');

            // ถ้าไม่มี emoji ใน DB ให้ fallback เป็นไอคอนสนามทั่วไป
            $v['type_icon']  = $catEmoji !== '' ? $catEmoji : '🏟️';
            $v['type_label'] = $catName  !== '' ? $catName  : 'สนามกีฬา';

            // ดาวรีวิว (ถ้าไม่มีรีวิวให้เป็น 0)
            $sid = (int) ($v['id'] ?? 0);
            $summary = $summaries[$sid] ?? ['avg' => 0.0, 'count' => 0];
            $v['avg_rating'] = round((float)$summary['avg'], 1);
            $v['review_count'] = (int)$summary['count'];
            $v['rating'] = $v['avg_rating']; // Keep for nearby section

            // รูปปกด้านนอกใบแรกจาก JSON
            $cover = null;
            if (!empty($v['outside_images'])) {
                $decoded = json_decode($v['outside_images'], true);
                if (is_array($decoded) && !empty($decoded)) {
                    $cover = reset($decoded);
                }
            }
            $v['cover_image'] = $cover;

            // Logic คำนวณราคา (Display Range)
            $prices = $stadiumPrices[$sid] ?? ['hourly' => [], 'daily' => []];

            $priceHtmlParts = [];

            // 1. Hourly
            if (!empty($prices['hourly'])) {
                $minH = min($prices['hourly']);
                $maxH = max($prices['hourly']);
                if (count($prices['hourly']) > 1 && $minH !== $maxH) {
                    // range
                    $priceHtmlParts[] = '<div class="text-right"><div class="text-xs text-gray-500">รายชั่วโมง</div><div class="font-bold text-[var(--primary)]">' . number_format($minH) . ' ~ ' . number_format($maxH) . ' ฿</div></div>';
                } else {
                    // single
                    $priceHtmlParts[] = '<div class="text-right"><div class="text-xs text-gray-500">รายชั่วโมง</div><div class="font-bold text-[var(--primary)]">' . number_format($minH) . ' ฿</div></div>';
                }
            }

            // 2. Daily
            if (!empty($prices['daily'])) {
                $minD = min($prices['daily']);
                $maxD = max($prices['daily']);
                if (count($prices['daily']) > 1 && $minD !== $maxD) {
                    $priceHtmlParts[] = '<div class="text-right"><div class="text-xs text-gray-500">รายวัน</div><div class="font-bold text-orange-600">' . number_format($minD) . ' ~ ' . number_format($maxD) . ' ฿</div></div>';
                } else {
                    $priceHtmlParts[] = '<div class="text-right"><div class="text-xs text-gray-500">รายวัน</div><div class="font-bold text-orange-600">' . number_format($minD) . ' ฿</div></div>';
                }
            }

            if (empty($priceHtmlParts)) {
                $v['price_range_html'] = '<span class="text-xs text-gray-400">ยังไม่มีราคา</span>';
            } else {
                // เชื่อมด้วย gap เล็กน้อย
                $v['price_range_html'] = implode('<div class="h-2"></div>', $priceHtmlParts);
            }
        }
        unset($v);

        
        // Favorites map (for heart icon state)
        $favoriteMap = [];
        if (session()->get('customer_logged_in')) {
            $favModel = new CustomerFavoriteModel();
            $favIds   = $favModel->getFavoriteStadiumIds((int) session('customer_id'));
            $favoriteMap = array_fill_keys($favIds, true);
        }

$data = [
            'heroUrl'    => 'assets/images/batminton.webp',
            'title'      => 'จองสนามกีฬาออนไลน์',
            'venueCards' => $venueCards,
            'favoriteMap' => $favoriteMap,
        ];

        return view('public/home', $data);
    }
}
