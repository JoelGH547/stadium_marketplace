<?php

namespace App\Controllers\customer;

use App\Controllers\BaseController;
use App\Models\StadiumModel;
use App\Models\CategoryModel;
use App\Models\StadiumFieldModel;
// use App\Models\VendorItemModel; // Unused

class StadiumController extends BaseController
{
    public function view()
    {
        $stadiumModel  = new StadiumModel();
        $categoryModel = new CategoryModel();

        // ดึงสนามทั้งหมด + join category (ชื่อ + emoji)
        $venueCards = $stadiumModel
            ->select('stadiums.*, categories.name AS category_name, categories.emoji AS category_emoji')
            ->join('categories', 'categories.id = stadiums.category_id', 'left')
            ->orderBy('stadiums.id', 'DESC')
            ->findAll(); // ✅ เอามาทั้งหมด (ภายหลังค่อยทำ pagination ได้)

        // เตรียมข้อมูลให้เหมือนหน้า home (type_icon, type_label, cover_image)
        foreach ($venueCards as &$v) {
            $catName  = (string)($v['category_name']  ?? '');
            $catEmoji = (string)($v['category_emoji'] ?? '');

            $v['type_icon']  = $catEmoji !== '' ? $catEmoji : '🏟️';
            $v['type_label'] = $catName  !== '' ? $catName  : 'สนามกีฬา';

            // รูปปกด้านนอกใบแรกจาก JSON outside_images
            $cover = null;
            if (!empty($v['outside_images'])) {
                $decoded = json_decode($v['outside_images'], true);
                if (is_array($decoded) && !empty($decoded)) {
                    $cover = reset($decoded);
                }
            }
            $v['cover_image'] = $cover;
        }
        unset($v);

        // ดึงประเภทกีฬาไปใช้ใน filter (dynamic จากตาราง categories)
        $categories = $categoryModel
            ->orderBy('name', 'ASC')
            ->findAll();

        $data = [
            'venueCards' => $venueCards,
            'categories' => $categories,
        ];

        return view('public/view', $data);
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
            ->where('stadiums.id', $stadium['id'])
            ->where('vendor_products.status', 'active')
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

        // ส่งตัวแปรให้ field.php (dummy ใน view จะไม่ถูกใช้เพราะเราส่งค่ามาแล้ว)
        return view('public/field', [
            'stadium'   => $stadium,
            'stadiumId' => (int) $id,
            'fields'    => $fields,
            'stadiumImages'  => $stadiumImages,
        ]);
    }
}
