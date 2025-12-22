<?php

namespace App\Controllers\customer;

use App\Controllers\BaseController;
use App\Models\StadiumModel;
use App\Models\CategoryModel;
use App\Models\StadiumFieldModel;
use App\Models\StadiumReviewModel;
use App\Models\CustomerFavoriteModel;
use App\Models\BookingModel;
use App\Models\FacilityTypeModel;

class HomeController extends BaseController
{
    public function index()
    {
        $stadiumModel = new StadiumModel();
        $categoryModel = new CategoryModel();

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

        // Facilities (map per stadium_id) + facility type list for sidebar filter
        $facilityTypeModel = new FacilityTypeModel();
        $facilityTypes = $facilityTypeModel->orderBy('name', 'ASC')->findAll();

        $stadiumFacilityMap = [];
        if (!empty($stadiumIds)) {
            $db = db_connect();
            $rows = $db->table('stadium_facilities sf')
                ->select('sfields.stadium_id, sf.facility_type_id')
                ->join('stadium_fields sfields', 'sfields.id = sf.field_id')
                ->whereIn('sfields.stadium_id', $stadiumIds)
                ->distinct()
                ->get()
                ->getResultArray();

            foreach ($rows as $r) {
                $sid = (int) ($r['stadium_id'] ?? 0);
                $fid = (int) ($r['facility_type_id'] ?? 0);
                if ($sid <= 0 || $fid <= 0) continue;
                if (!isset($stadiumFacilityMap[$sid])) $stadiumFacilityMap[$sid] = [];
                $stadiumFacilityMap[$sid][] = $fid;
            }
            // unique per stadium
            foreach ($stadiumFacilityMap as $sid => $arr) {
                $arr = array_values(array_unique(array_map('intval', $arr)));
                sort($arr);
                $stadiumFacilityMap[$sid] = $arr;
            }
        }



        // Booking counts (for popularity sort)
        $bookingCountMap = [];
        if (!empty($stadiumIds)) {
            $bookingModel = new BookingModel();
            $bookingCountsRows = $bookingModel->select('stadium_id, COUNT(*) as booking_count')
                ->whereIn('stadium_id', $stadiumIds)
                ->whereIn('status', ['approved', 'paid', 'confirmed'])
                ->groupBy('stadium_id')
                ->findAll();

            foreach ($bookingCountsRows as $r) {
                $bookingCountMap[(int)$r['stadium_id']] = (int)($r['booking_count'] ?? 0);
            }
        }

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

        
        // Price bounds for dual-range slider on home filter
        $priceBounds = [
            'hourly_min' => 0,
            'hourly_max' => 0,
            'daily_min'  => 0,
            'daily_max'  => 0,
        ];
        $tmpHMin = INF; $tmpHMax = 0;
        $tmpDMin = INF; $tmpDMax = 0;

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

            // Store per-mode price range (for home filter)
            $hourlyMin = !empty($prices['hourly']) ? (float) min($prices['hourly']) : 0;
            $hourlyMax = !empty($prices['hourly']) ? (float) max($prices['hourly']) : 0;
            $dailyMin  = !empty($prices['daily'])  ? (float) min($prices['daily'])  : 0;
            $dailyMax  = !empty($prices['daily'])  ? (float) max($prices['daily'])  : 0;

            $v['hourly_min'] = (int) round($hourlyMin);
            $v['hourly_max'] = (int) round($hourlyMax);
            $v['daily_min']  = (int) round($dailyMin);
            $v['daily_max']  = (int) round($dailyMax);

            // Update global bounds (ignore 0 = no price)
            if ($hourlyMin > 0) $tmpHMin = min($tmpHMin, $hourlyMin);
            if ($hourlyMax > 0) $tmpHMax = max($tmpHMax, $hourlyMax);
            if ($dailyMin  > 0) $tmpDMin = min($tmpDMin, $dailyMin);
            if ($dailyMax  > 0) $tmpDMax = max($tmpDMax, $dailyMax);

            // Facilities (ids csv for dataset on venue card)
            $facIds = $stadiumFacilityMap[$sid] ?? [];
            $v['facility_ids'] = $facIds;
            $v['facility_ids_csv'] = !empty($facIds) ? implode(',', $facIds) : '';


            // ใช้ค่าน้อยสุด (รวม hourly + daily) สำหรับการ sort "ราคาถูกสุด/สุดหรู"
            $allAvailablePrices = [];
            if (!empty($prices['hourly'])) {
                $allAvailablePrices = array_merge($allAvailablePrices, $prices['hourly']);
            }
            if (!empty($prices['daily'])) {
                $allAvailablePrices = array_merge($allAvailablePrices, $prices['daily']);
            }
            $v['min_price'] = !empty($allAvailablePrices) ? min($allAvailablePrices) : 0;

            // ยอดนิยม: จำนวนการจองของสนามหลัก
            $v['booking_count'] = (int) ($bookingCountMap[$sid] ?? 0);


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

        
        
        // Finalize price bounds
        if (is_finite($tmpHMin) && $tmpHMax > 0) {
            $priceBounds['hourly_min'] = (int) floor($tmpHMin);
            $priceBounds['hourly_max'] = (int) ceil($tmpHMax);
        }
        if (is_finite($tmpDMin) && $tmpDMax > 0) {
            $priceBounds['daily_min'] = (int) floor($tmpDMin);
            $priceBounds['daily_max'] = (int) ceil($tmpDMax);
        }

// Favorites map (for heart icon state)
        $favoriteMap = [];
        if (session()->get('customer_logged_in')) {
            $favModel = new CustomerFavoriteModel();
            $favIds   = $favModel->getFavoriteStadiumIds((int) session('customer_id'));
            $favoriteMap = array_fill_keys($favIds, true);
        }

        // ดึงประเภทกีฬาไปใช้ใน filter (dynamic)
        $categories = $categoryModel->orderBy('name', 'ASC')->findAll();

        $data = [
            'heroUrl'    => 'assets/images/batminton.webp',
            'title'      => 'จองสนามกีฬาออนไลน์',
            'venueCards' => $venueCards,
            'favoriteMap' => $favoriteMap,
            'categories' => $categories,
            'facilityTypes' => $facilityTypes,
            'priceBounds' => $priceBounds,
        ];

        return view('public/home', $data);
    }
}
