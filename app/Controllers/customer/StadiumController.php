<?php

namespace App\Controllers\customer;

use App\Controllers\BaseController;
use App\Models\StadiumModel;
use App\Models\CategoryModel;
use App\Models\StadiumFieldModel;
use App\Models\StadiumReviewModel;
use App\Models\CustomerFavoriteModel;
// use App\Models\VendorItemModel; // Unused

class StadiumController extends BaseController
{
    public function view()
    {
        $stadiumModel  = new StadiumModel();
        $categoryModel = new CategoryModel();
        $db            = \Config\Database::connect();

        // -------------------------
        // Read filters (GET)
        // -------------------------
        $mode = (string) ($this->request->getGet('mode') ?? $this->request->getGet('rent') ?? '');
        if (! in_array($mode, ['hourly', 'daily'], true)) {
            $mode = '';
        }

        $q = trim((string) $this->request->getGet('q'));

        // Category filter (category_id is preferred; accept legacy numeric category too)
        $categoryId = (int) $this->request->getGet('category_id');
        if ($categoryId <= 0) {
            $legacyCategory = $this->request->getGet('category');
            if (is_numeric($legacyCategory)) {
                $categoryId = (int) $legacyCategory;
            }
        }

        $date       = (string) $this->request->getGet('date');
        $startTime  = (string) $this->request->getGet('start_time');
        $endTime    = (string) $this->request->getGet('end_time');

        $startDate  = (string) $this->request->getGet('start_date');
        $endDate    = (string) $this->request->getGet('end_date');

        $today = date('Y-m-d');

        // -------------------------
        // Normalize / validate times (for availability check)
        // -------------------------
        $reqStart = null; $reqEnd = null; $reqStartTime = null; $reqEndTime = null;
        $isValidDate = fn ($d) => is_string($d) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d);
        $isValidTime = fn ($t) => is_string($t) && (preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $t) || $t === '24:00');

        if ($mode === 'hourly' && $isValidDate($date) && $isValidTime($startTime) && $isValidTime($endTime)) {
            if ($date >= $today) {
                $startMin = ($startTime === '24:00') ? 1440 : (intval(substr($startTime, 0, 2)) * 60 + intval(substr($startTime, 3, 2)));
                $endMin   = ($endTime === '24:00') ? 1440 : (intval(substr($endTime, 0, 2)) * 60 + intval(substr($endTime, 3, 2)));
                if ($startMin < $endMin && $startMin < 1440) {
                    $reqStartTime = $startTime . ':00';
                    $reqEndTime   = ($endTime === '24:00') ? '24:00:00' : ($endTime . ':00');
                    $reqStart = $date . ' ' . $startTime . ':00';
                    $reqEnd   = ($endTime === '24:00') ? date('Y-m-d', strtotime($date . ' +1 day')) . ' 00:00:00' : $date . ' ' . $endTime . ':00';
                }
            }
        }

        if ($mode === 'daily' && $isValidDate($startDate) && $isValidDate($endDate)) {
            if ($startDate >= $today && $endDate >= $startDate) {
                $reqStart = $startDate . ' 00:00:00';
                $reqEnd   = date('Y-m-d', strtotime($endDate . ' +1 day')) . ' 00:00:00';
            }
        }

        // -------------------------
        // Base query
        // -------------------------
        $builder = $stadiumModel
            ->select('stadiums.*, categories.name AS category_name, categories.emoji AS category_emoji')
            ->join('categories', 'categories.id = stadiums.category_id', 'left')
            ->orderBy('stadiums.id', 'DESC');

        if ($q !== '') {
            $builder->like('stadiums.name', $q);
        }

        if (!empty($categoryId)) {
            $builder->where('stadiums.category_id', $categoryId);
        }

        // -------------------------
        // Availability filter
        // -------------------------
        // -------------------------
        // Availability filter
        // -------------------------
        $priceFilterSql = '';
        if ($mode === 'daily') {
            $priceFilterSql = 'sf.price_daily IS NOT NULL AND sf.price_daily > 0';
        } elseif ($mode === 'hourly') {
            $priceFilterSql = 'sf.price IS NOT NULL AND sf.price > 0';
        } else { // 'All' mode or empty
            $priceFilterSql = '((sf.price IS NOT NULL AND sf.price > 0) OR (sf.price_daily IS NOT NULL AND sf.price_daily > 0))';
        }

        // The time availability check only happens if a specific mode and time is chosen
        $bookingCheckSql = '';
        if ($mode !== '' && $reqStart && $reqEnd) {
            $reqStartEsc = $db->escape($reqStart);
            $reqEndEsc   = $db->escape($reqEnd);
            $bookingCheckSql = " AND NOT EXISTS (SELECT 1 FROM bookings b WHERE b.field_id = sf.id AND b.status IN ('pending','confirmed') AND b.booking_start_time < $reqEndEsc AND b.booking_end_time > $reqStartEsc)";
        }
        
        $finalSql = "EXISTS (SELECT 1 FROM stadium_fields sf WHERE sf.stadium_id = stadiums.id AND ($priceFilterSql) $bookingCheckSql)";
        $builder->where($finalSql, null, false);

        $venueCards = $builder->get()->getResultArray();

        // -------------------------
        // Extra: Time-based filtering
        // -------------------------
        $timeToMinutes = function (?string $time) {
            if (! $time) return null;
            if (preg_match('/^(\d{1,3}):(\d{2})(?::(\d{2}))?$/', $time, $m)) { return (int) $m[1] * 60 + (int) $m[2]; }
            return null;
        };

        if ($mode === 'hourly' && $reqStartTime && $reqEndTime) {
            $reqStartMin = $timeToMinutes($reqStartTime);
            $reqEndMin   = ($reqEndTime === '24:00:00') ? 1440 : $timeToMinutes($reqEndTime);
            if ($reqStartMin !== null && $reqEndMin !== null) {
                $venueCards = array_values(array_filter($venueCards, function ($v) use ($timeToMinutes, $reqStartMin, $reqEndMin) {
                    $openMin  = $timeToMinutes($v['open_time'] ?? null);
                    $closeMin = $timeToMinutes($v['close_time'] ?? null);
                    if ($openMin === null || $closeMin === null) return true;
                    if ($closeMin >= $openMin) return ($reqStartMin >= $openMin) && ($reqEndMin <= $closeMin);
                    return ($reqStartMin >= $openMin) || ($reqEndMin <= $closeMin);
                }));
            }
        }
        
        // -------------------------
        // Data Enrichment
        // -------------------------
        $stadiumIds = array_column($venueCards, 'id');

        // 1. Ratings
        $reviewModel = new StadiumReviewModel();
        $summaries = !empty($stadiumIds) ? $reviewModel->getSummariesForStadiumIds($stadiumIds) : [];

        // 2. Prices
        $fieldModel = new StadiumFieldModel();
        $allFields = !empty($stadiumIds) ? $fieldModel->select('stadium_id, price, price_daily')->whereIn('stadium_id', $stadiumIds)->findAll() : [];
        $stadiumPrices = [];
        foreach ($allFields as $f) {
            if (!isset($stadiumPrices[$f['stadium_id']])) $stadiumPrices[$f['stadium_id']] = ['hourly' => [], 'daily'  => []];
            if (!empty($f['price']) && $f['price'] > 0) $stadiumPrices[$f['stadium_id']]['hourly'][] = (float)$f['price'];
            if (!empty($f['price_daily']) && $f['price_daily'] > 0) $stadiumPrices[$f['stadium_id']]['daily'][] = (float)$f['price_daily'];
        }

        // 3. Facilities
        $stadiumFacilityMap = [];
        if (!empty($stadiumIds)) {
            $stadiumFacilities = $db->table('stadium_facilities sf')
                ->select('sfields.stadium_id, sf.facility_type_id')
                ->join('stadium_fields sfields', 'sfields.id = sf.field_id')
                ->whereIn('sfields.stadium_id', $stadiumIds)
                ->distinct()->get()->getResultArray();
            foreach ($stadiumFacilities as $sf) {
                if (!isset($stadiumFacilityMap[$sf['stadium_id']])) $stadiumFacilityMap[$sf['stadium_id']] = [];
                $stadiumFacilityMap[$sf['stadium_id']][] = $sf['facility_type_id'];
            }
        }

        // 4. Loop to apply enrichment
        foreach ($venueCards as &$v) {
            $sid = (int) ($v['id'] ?? 0);
            $v['type_icon'] = !empty($v['category_emoji']) ? $v['category_emoji'] : '🏟️';
            $v['type_label'] = $v['category_name'] ?? 'สนามกีฬา';

            $summary = $summaries[$sid] ?? ['avg' => 0.0, 'count' => 0];
            $v['avg_rating'] = round((float)$summary['avg'], 1);
            $v['review_count'] = (int)$summary['count'];
            
            $prices = $stadiumPrices[$sid] ?? ['hourly' => [], 'daily' => []];
            
            // --- Logic to find the absolute minimum price for sorting ---
            $allAvailablePrices = [];
            if (!empty($prices['hourly'])) {
                $allAvailablePrices = array_merge($allAvailablePrices, $prices['hourly']);
            }
            if (!empty($prices['daily'])) {
                $allAvailablePrices = array_merge($allAvailablePrices, $prices['daily']);
            }
            $v['min_price'] = !empty($allAvailablePrices) ? min($allAvailablePrices) : 0;
            unset($v['price']); // Unset original price from stadium table to avoid confusion

            // --- Logic to create HTML for display ---
            $priceHtmlParts = [];
            if (!empty($prices['hourly'])) {
                $minH = min($prices['hourly']); $maxH = max($prices['hourly']);
                $priceHtmlParts[] = '<div class="text-right"><div class="text-xs text-gray-500">รายชั่วโมง</div><div class="font-bold text-[var(--primary)]">' . ($minH !== $maxH ? number_format($minH) . ' ~ ' . number_format($maxH) : number_format($minH)) . ' ฿</div></div>';
            }
            if (!empty($prices['daily'])) {
                $minD = min($prices['daily']); $maxD = max($prices['daily']);
                $priceHtmlParts[] = '<div class="text-right"><div class="text-xs text-gray-500">รายวัน</div><div class="font-bold text-orange-600">' . ($minD !== $maxD ? number_format($minD) . ' ~ ' . number_format($maxD) : number_format($minD)) . ' ฿</div></div>';
            }
            $v['price_range_html'] = empty($priceHtmlParts) ? '<span class="text-xs text-gray-400">ยังไม่มีราคา</span>' : implode('<div class="h-2"></div>', $priceHtmlParts);
            
            $v['facility_ids'] = $stadiumFacilityMap[$sid] ?? [];
        }
        unset($v);

        // -------------------------
        // Prepare data for view
        // -------------------------
        $favoriteMap = [];
        if (session()->get('customer_logged_in')) {
            $favModel = new CustomerFavoriteModel();
            $favIds   = $favModel->getFavoriteStadiumIds((int) session('customer_id'));
            $favoriteMap = array_fill_keys($favIds, true);
        }

        $facilityTypeModel = new \App\Models\FacilityTypeModel();
        $facilityTypes = $facilityTypeModel->orderBy('name', 'ASC')->findAll();
        $categories = $categoryModel->orderBy('name', 'ASC')->findAll();

        $filters = [
            'mode' => $mode, 'category_id' => $categoryId, 'q' => $q, 'date' => $date, 'start_time' => $startTime, 
            'end_time' => $endTime, 'start_date' => $startDate, 'end_date' => $endDate,
        ];

        return view('public/view', [
            'venueCards'  => $venueCards,
            'categories'  => $categories,
            'facilityTypes' => $facilityTypes,
            'filters'     => $filters,
            'favoriteMap' => $favoriteMap,
        ]);
    }

public function show($id = null)
    {
        // ตอนนี้ $id = stadium_fields.id (สนามย่อย)
        if ($id === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('ไม่พบสนามย่อยที่ต้องการ');
        }

        $fieldModel    = new StadiumFieldModel();
        $stadiumModel  = new StadiumModel();
        // $itemModel     = new VendorItemModel(); // Removed

        // 1) ดึงข้อมูลสนามย่อย
        $field = $fieldModel->find($id);
        if (!$field) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('ไม่พบสนามย่อยที่ต้องการ');
        }

        $stadiumId = (int) ($field['stadium_id'] ?? 0);
        if ($stadiumId <= 0) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('สนามย่อยนี้ไม่ผูกกับสนามหลัก');
        }

        // 2) ดึงข้อมูลสนามหลัก + category + emoji
        $row = $stadiumModel->getStadiumsWithCategory($stadiumId);
        if (!$row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('ไม่พบสนามหลักที่เกี่ยวข้อง');
        }

        // 3) เลือก description ให้เหมาะกับสนามย่อยก่อน แล้วค่อย fallback ไปสนามหลัก
        $description = trim((string) ($field['description'] ?? ''));
        if ($description === '') {
            $description = trim((string) ($field['short_description'] ?? ''));
        }
        if ($description === '') {
            $description = trim((string) ($row['description'] ?? ''));
        }

        // 4) สร้าง array $stadium สำหรับให้ show.php ใช้ (บล็อคแรก)
        $stadium = [
            'id'             => (int) $row['id'],
            'name'           => $row['name'] ?? '',
            // ใช้ราคา/ชม. ของสนามย่อยเป็น price หลัก
            'price'          => isset($field['price']) ? (float) $field['price'] : 0,
            'category_name'  => $row['category_name']  ?? '',
            'category_emoji' => $row['category_emoji'] ?? '🏟️',
            'description'    => $description,

            'lat'            => $row['lat'] ?? null,
            'lng'            => $row['lng'] ?? null,
            'district'       => $row['district'] ?? '',
            'province'       => $row['province'] ?? '',

            'contact_phone'  => $row['contact_phone'] ?? '',
            'contact_email'  => $row['contact_email'] ?? '',

            'open_time'      => $row['open_time'] ?? null,
            'close_time'     => $row['close_time'] ?? null,

            // รูปภาพ: ใช้รูปของสนามย่อยก่อน ถ้าไม่มีค่อย fallback เป็นของสนามหลัก
            'cover_image'    => $row['cover_image'] ?? null,
            'outside_images' => $field['outside_images'] ?: ($row['outside_images'] ?? null),
            'inside_images'  => $field['inside_images']  ?: ($row['inside_images'] ?? null),

            // rating ตอนนี้ยังไม่มีใน DB → ใส่ค่า default ไว้ก่อน
            'rating'         => 5.0,

            // ✅ เพิ่ม status ของสนามย่อยเข้ามาใน array หลัก เพื่อให้ view นำไปใช้ได้สะดวก
            'status'         => $field['status'] ?? 'active',
        ];

        // 5) สร้าง $fields สำหรับ show.php (ตอนนี้มีแค่ "สนามย่อยที่เลือก" ตัวเดียว)
        $priceHour  = $field['price']        ?? null;
        $priceDaily = $field['price_daily']  ?? null;

        $fields = [
            [
                'id'         => (int) $field['id'],
                'name'       => $field['name'] ?? '',
                'status'     => $field['status'] ?? 'active',
                // show.php ใช้ key "price_hour" และ "price_day"
                'price_hour' => $priceHour  !== null ? (float) $priceHour  : null,
                'price_day'  => $priceDaily !== null ? (float) $priceDaily : null,
            ],
        ];

        // 6) ดึง items (products) ที่ผูกกับสนามนี้
        // join stadium_facilities -> stadium_fields -> stadiums
        $productModel = new \App\Models\VendorItemModel();
        
        $rawProducts = $productModel->withRelations()
            ->where('stadium_fields.id', $id)
            ->orderBy('facility_types.id', 'ASC')
            ->orderBy('vendor_items.id', 'DESC')
            ->findAll();

        // Group by Category (Facility Type)
        $groupedItems = [];
        foreach ($rawProducts as $p) {
            $catName = $p['facility_type_name'] ?? 'อื่นๆ';
            if (!isset($groupedItems[$catName])) {
                $groupedItems[$catName] = [];
            }
            $groupedItems[$catName][] = $p;
        }

        // 7) ตัวช่วยเสริม (ไม่บังคับ แต่ให้ค่าไว้เหมือน mock เดิม)
        $district = trim((string) ($stadium['district'] ?? ''));
        $province = trim((string) ($stadium['province'] ?? ''));
        $addressFull = trim($district . ($district && $province ? ', ' : '') . $province);

        $openTimeRaw  = isset($stadium['open_time'])  ? substr($stadium['open_time'], 0, 5)  : '';
        $closeTimeRaw = isset($stadium['close_time']) ? substr($stadium['close_time'], 0, 5) : '';
        $timeLabel    = ($openTimeRaw && $closeTimeRaw)
            ? ($openTimeRaw . ' – ' . $closeTimeRaw)
            : 'ยังไม่ระบุเวลา';

        // coverUrl / galleryImages จริงๆ show.php คำนวนเองแล้ว ใช้ค่า default ไว้เฉยๆ
        $coverUrl      = null;
        $galleryImages = [];

        // 8) Retrieve Cart Session for Restore State
        $cart = cart_get(); // Helper function
        $cartData = null;
        // Only restore if 'restore=1' is passed in URL AND stadium_id matches
        if ($this->request->getGet('restore') && $cart && isset($cart['stadium_id']) && (int) $cart['stadium_id'] === (int) $stadium['id']) {
            $cartData = $cart;
        }

        // 9) ส่งให้ View
        return view('public/show', [
            'stadium'       => $stadium,
            'fields'        => $fields,
            'groupedItems'  => $groupedItems, // ส่งแบบ Group แล้ว
            'coverUrl'      => $coverUrl,
            'galleryImages' => $galleryImages,
            'addressFull'   => $addressFull,
            'timeLabel'     => $timeLabel,
            'cartData'      => $cartData, // Pass cart data to frontend
            'isLoggedIn'    => session()->get('customer_logged_in') ?? false, // Pass login status
        ]);
    }



    public function fields($id = null)
    {
        if ($id === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('ไม่พบสนามที่ต้องการ');
        }

        $stadiumModel = new StadiumModel();
        $fieldModel   = new StadiumFieldModel();

        // ดึงข้อมูลสนามหลัก + category (ใช้ฟังก์ชันที่มีอยู่แล้ว)
        $row = $stadiumModel->getStadiumsWithCategory($id);

        if (!$row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('ไม่พบสนามที่ต้องการ');
        }

        // เตรียมข้อมูลหัวการ์ดสนามหลักให้ตรงกับที่ field.php ใช้
        helper('url'); // ให้ใช้ base_url ได้ชัวร์

        // emoji + ชื่อประเภทกีฬา
        $sportEmoji = $row['category_emoji'] ?? '🏟️';
        $sportName  = $row['category_name']  ?? 'สนามกีฬา';

        // location ง่าย ๆ จาก address + province
        $locationParts = [];
        if (!empty($row['address'])) {
            $locationParts[] = trim($row['address']);
        }
        if (!empty($row['province'])) {
            $locationParts[] = trim($row['province']);
        }
        $location = !empty($locationParts) ? implode(', ', $locationParts) : 'ประเทศไทย';

        // ✅ รวมรูป outside + inside จากตาราง stadiums
        $imageBasePath = 'assets/uploads/stadiums/';

        $outsideFiles = [];
        if (!empty($row['outside_images'])) {
            $decoded = json_decode($row['outside_images'], true);
            if (is_array($decoded)) {
                $outsideFiles = array_filter($decoded, fn($v) => is_string($v) && $v !== '');
            }
        }

        $insideFiles = [];
        if (!empty($row['inside_images'])) {
            $decoded = json_decode($row['inside_images'], true);
            if (is_array($decoded)) {
                $insideFiles = array_filter($decoded, fn($v) => is_string($v) && $v !== '');
            }
        }

        $stadiumImages = [];
        foreach (array_merge($outsideFiles, $insideFiles) as $file) {
            $stadiumImages[] = base_url($imageBasePath . $file);
        }

        // ใช้ภาพแรกเป็น hero ถ้ามี ไม่มีก็ใช้ default เดิม
        $heroImageUrl = $stadiumImages[0] ?? base_url('assets/uploads/home/batminton.webp');


        $stadium = [
            'name'        => $row['name'],
            'sport_emoji' => $sportEmoji,
            'sport_name'  => $sportName,
            'location'    => $location,
            'hero_image'  => $heroImageUrl,
            'lat'         => $row['lat'] ?? null,
            'lng'         => $row['lng'] ?? null,
        ];

        // label เวลาเปิดแต่ละสนามย่อย (ใช้เวลาเปิด/ปิดจาก stadium หลัก)
        $open  = $row['open_time']  ?? null;
        $close = $row['close_time'] ?? null;

        if ($open && strlen($open) >= 5) {
            $open = substr($open, 0, 5);
        }
        if ($close && strlen($close) >= 5) {
            $close = substr($close, 0, 5);
        }

        $openLabel = ($open && $close)
            ? ($open . ' - ' . $close . ' น.')
            : 'ยังไม่ระบุเวลาเปิด-ปิด';

        $stadium['open_label'] = $openLabel;


        // ดึงรายการสนามย่อยจาก stadium_fields (ทั้งหมด, ไม่กรอง status)
        $fieldRows = $fieldModel
            ->where('stadium_id', $id)
            ->orderBy('id', 'ASC')
            ->findAll();

        $fields = [];

        foreach ($fieldRows as $f) {
            // รูปของสนามย่อย (fallback เป็น hero ของสนามหลัก)
            $thumb = null;
            if (!empty($f['outside_images'])) {
                $decoded = json_decode($f['outside_images'], true);
                if (is_array($decoded) && !empty($decoded)) {
                    $thumb = reset($decoded);
                }
            }

            $imageUrl = $thumb
                ? base_url('assets/uploads/stadiums/' . $thumb)
                : $heroImageUrl;

            $priceHour  = $f['price'] ?? null;
            $priceDaily = $f['price_daily'] ?? null;

            $fields[] = [
                'id'         => $f['id'],
                'name'       => $f['name'],
                'status'     => $f['status'] ?? 'active', // ส่ง status ไปให้ view
                'price_hour'   => ($priceHour  !== null ? (float) $priceHour  : null),
                'price_daily'  => ($priceDaily !== null ? (float) $priceDaily : null),
                'image'      => $imageUrl,
                'short_desc' => $f['short_description'] ?? '',
            ];
        }

        // รีวิว + คะแนนดาว (รวมของสนามย่อยทั้งหมดเข้าที่สนามหลัก)
        $reviewModel   = new StadiumReviewModel();
        $ratingSummary = $reviewModel->getSummaryForStadium((int) $id);
        $latestReviews = $reviewModel->getLatestForStadium((int) $id, 8);

        
        // Favorite state for this stadium
        $isFavorite = false;
        if (session()->get('customer_logged_in')) {
            $favModel    = new CustomerFavoriteModel();
            $isFavorite  = $favModel->isFavorited((int) session('customer_id'), (int) $id);
        }

// ส่งตัวแปรให้ field.php (dummy ใน view จะไม่ถูกใช้เพราะเราส่งค่ามาแล้ว)
        return view('public/field', [
            'stadium'   => $stadium,
            'stadiumId' => (int) $id,
            'fields'    => $fields,
            'stadiumImages'  => $stadiumImages,
            'ratingSummary' => $ratingSummary,
            'latestReviews' => $latestReviews,
            'isFavorite'    => $isFavorite,
        ]);
    }
}
