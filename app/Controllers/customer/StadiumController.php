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

        $date       = (string) $this->request->getGet('date');
        $startTime  = (string) $this->request->getGet('start_time');
        $endTime    = (string) $this->request->getGet('end_time');

        $startDate  = (string) $this->request->getGet('start_date');
        $endDate    = (string) $this->request->getGet('end_date');

        $today = date('Y-m-d');

        // -------------------------
        // Normalize / validate times (for availability check)
        // -------------------------
        $reqStart = null; // DATETIME string
        $reqEnd   = null; // DATETIME string (exclusive end)
        $reqStartTime = null; // TIME string (for open/close check)
        $reqEndTime   = null; // TIME string (for open/close check)

        $isValidDate = function ($d) {
            return is_string($d) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d);
        };

        $isValidTime = function ($t) {
            // allow 00:00 .. 23:59 + special 24:00
            return is_string($t) && preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $t) || $t === '24:00';
        };

        if ($mode === 'hourly' && $isValidDate($date) && $isValidTime($startTime) && $isValidTime($endTime)) {
            // cannot choose past date
            if ($date >= $today) {
                // end must be after start; allow end=24:00
                $startMin = ($startTime === '24:00') ? 1440 : (intval(substr($startTime, 0, 2)) * 60 + intval(substr($startTime, 3, 2)));
                $endMin   = ($endTime === '24:00') ? 1440 : (intval(substr($endTime, 0, 2)) * 60 + intval(substr($endTime, 3, 2)));

                if ($startMin < $endMin && $startMin < 1440) {
                    $reqStartTime = $startTime . ':00';
                    $reqEndTime   = ($endTime === '24:00') ? '24:00:00' : ($endTime . ':00');

                    $reqStart = $date . ' ' . $startTime . ':00';

                    if ($endTime === '24:00') {
                        $reqEnd = date('Y-m-d', strtotime($date . ' +1 day')) . ' 00:00:00';
                    } else {
                        $reqEnd = $date . ' ' . $endTime . ':00';
                    }
                }
            }
        }

        if ($mode === 'daily' && $isValidDate($startDate) && $isValidDate($endDate)) {
            if ($startDate >= $today && $endDate >= $startDate) {
                // Daily: treat as [startDate 00:00, endDate+1 00:00)
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

        // -------------------------
        // Availability filter (bookings overlap) + rent support (price column)
        // - Apply only when user selects mode (hourly/daily)
        // - If time/date not provided, still filter by "supports rent" (price > 0)
        // -------------------------
        if ($mode !== '') {
            $priceCol = ($mode === 'daily') ? 'price_daily' : 'price';

            $sql = "EXISTS (
                SELECT 1
                FROM stadium_fields sf
                WHERE sf.stadium_id = stadiums.id
                  AND sf.$priceCol IS NOT NULL
                  AND sf.$priceCol > 0";

            if ($reqStart && $reqEnd) {
                $reqStartEsc = $db->escape($reqStart);
                $reqEndEsc   = $db->escape($reqEnd);

                $sql .= " AND NOT EXISTS (
                    SELECT 1
                    FROM bookings b
                    WHERE b.field_id = sf.id
                      AND b.status IN ('pending','confirmed')
                      AND b.booking_start_time < $reqEndEsc
                      AND b.booking_end_time   > $reqStartEsc
                )";
            }

            $sql .= ")";

            $builder->where($sql, null, false);
        }

        $venueCards = $builder->get()->getResultArray();

        // -------------------------
        // Extra real-world constraint: stadium open/close hours must cover requested time
        // (Only for hourly + when time filter is present)
        // -------------------------
        $timeToMinutes = function (?string $time) {
            if (! $time) return null;
            // TIME from DB may be "HH:MM:SS" or "HH:MM"
            if (preg_match('/^(\d{1,3}):(\d{2})(?::(\d{2}))?$/', $time, $m)) {
                $h = (int) $m[1];
                $mi = (int) $m[2];
                // ignore seconds
                return $h * 60 + $mi;
            }
            return null;
        };

        if ($mode === 'hourly' && $reqStartTime && $reqEndTime) {
            $reqStartMin = $timeToMinutes($reqStartTime);
            $reqEndMin   = ($reqEndTime === '24:00:00') ? 1440 : $timeToMinutes($reqEndTime);

            if ($reqStartMin !== null && $reqEndMin !== null) {
                $venueCards = array_values(array_filter($venueCards, function ($v) use ($timeToMinutes, $reqStartMin, $reqEndMin) {
                    $open  = $v['open_time']  ?? null;
                    $close = $v['close_time'] ?? null;

                    $openMin  = $timeToMinutes($open);
                    $closeMin = $timeToMinutes($close);

                    // If missing hours info, do not exclude
                    if ($openMin === null || $closeMin === null) {
                        return true;
                    }

                    // Normal same-day window
                    if ($closeMin >= $openMin) {
                        return ($reqStartMin >= $openMin) && ($reqEndMin <= $closeMin);
                    }

                    // Overnight window (e.g., 18:00 -> 02:00)
                    // Accept requests that fall in [open, 24:00] OR [00:00, close]
                    if ($reqStartMin >= $openMin) {
                        return true;
                    }
                    return ($reqEndMin <= $closeMin);
                }));
            }
        }

        // -------------------------
        // Category presentation helpers (existing behavior)
        // -------------------------
        foreach ($venueCards as &$venue) {
            $cat = strtolower(trim((string) ($venue['category_name'] ?? '')));
            $venue['type_icon'] = match ($cat) {
                'football'  => '⚽',
                'basketball'=> '🏀',
                'badminton' => '🏸',
                'tennis'    => '🎾',
                'futsal'    => '🥅',
                'volleyball'=> '🏐',
                default     => '🏟️',
            };

            $venue['type_bg'] = match ($cat) {
                'football'   => 'bg-green-50 text-green-700',
                'basketball' => 'bg-orange-50 text-orange-700',
                'badminton'  => 'bg-purple-50 text-purple-700',
                'tennis'     => 'bg-lime-50 text-lime-700',
                'futsal'     => 'bg-blue-50 text-blue-700',
                'volleyball' => 'bg-pink-50 text-pink-700',
                default      => 'bg-gray-50 text-gray-700',
            };
        }
        unset($venue);

        $categories = $categoryModel->orderBy('name', 'ASC')->findAll();

        $filters = [
            'mode'       => $mode,
            'q'          => $q,
            'date'       => $date,
            'start_time' => $startTime,
            'end_time'   => $endTime,
            'start_date' => $startDate,
            'end_date'   => $endDate,
        ];

        return view('public/view', [
            'venueCards' => $venueCards,
            'categories' => $categories,
            'filters'    => $filters,
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
        $productModel = new \App\Models\VendorProductModel();
        
        $rawProducts = $productModel->withRelations()
            ->where('stadium_fields.id', $id)
            ->orderBy('facility_types.id', 'ASC')
            ->orderBy('vendor_products.id', 'DESC')
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
