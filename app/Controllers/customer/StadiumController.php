<?php

namespace App\Controllers\customer;

use App\Controllers\BaseController;
use App\Models\StadiumModel;
use App\Models\CategoryModel;
use App\Models\StadiumFieldModel;

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
        // ---------------- MOCK ข้อมูลสนามหลัก ----------------
        $stadium = [
            'id'             => 1,
            'name'           => 'Arena Sport Complex (Mock)',
            'price'          => 370,
            'category_name'  => 'แบดมินตัน',
            'category_emoji' => '🏸',
            'description'    => 'สนามแบดมินตันในร่ม พื้นยางมาตรฐาน แสงสว่างทั่วถึง มีที่จอดรถ และห้องน้ำสะอาด.',
            'lat'            => null,
            'lng'            => null,
            'district'       => 'เขตห้วยขวาง',
            'province'       => 'กรุงเทพฯ',
            'contact_phone'  => '02-123-4567',
            'contact_email'  => 'contact@arena-mock.test',
            'open_time'      => '10:00',
            'close_time'     => '23:00',

            // รูปภาพแบบ mock (ปล่อยค่าว่าง เพราะ show.php เดี๋ยวสร้าง fallback เอง)
            'cover_image'    => '',
            'outside_images' => json_encode([]),
            'inside_images'  => json_encode([]),

            // rating mock
            'rating'         => 4.8,
        ];

        // ---------------- MOCK สนามย่อย ----------------
        $fields = [
            [
                'id'          => 1,
                'name'        => 'คอร์ท 1 (พื้นยาง)',
                'description' => 'คอร์ทในร่ม พื้นยางมาตรฐาน เหมาะสำหรับซ้อมจริงจัง.',
                'status'      => 'active',
            ],
            [
                'id'          => 2,
                'name'        => 'คอร์ท 2 (พื้นยาง)',
                'description' => 'คอร์ทในร่ม บรรยากาศสงบ เหมาะสำหรับเล่นชิลๆ.',
                'status'      => 'active',
            ],
        ];

        // ---------------- MOCK อุปกรณ์/บริการเสริม ----------------
        $items = [
            [
                'id'    => 1,
                'name'  => 'ไม้แบด Yonex Pro',
                'price' => 50,
                'unit'  => 'ชม.'
            ],
            [
                'id'    => 2,
                'name'  => 'ลูกแบดฝึกซ้อม (1 กระป๋อง)',
                'price' => 120,
                'unit'  => 'ชุด'
            ]
        ];

        // ---------------- ตัวแปรที่ show.php ต้องใช้ ----------------

        // 1) coverUrl
        $coverUrl = base_url('assets/uploads/home/batminton.webp'); // mock

        // 2) galleryImages
        $galleryImages = [
            $coverUrl,
            $coverUrl,
            $coverUrl,
        ];

        // 3) addressFull
        $addressFull = trim($stadium['district'] . ' ' . $stadium['province']);

        // 4) timeLabel (ใช้ open_time/close_time)
        $timeLabel = $stadium['open_time'] . ' - ' . $stadium['close_time'];

        // ส่งให้ View
        return view('public/show', [
            'stadium'       => $stadium,
            'fields'        => $fields,
            'items'         => $items,
            'coverUrl'      => $coverUrl,
            'galleryImages' => $galleryImages,
            'addressFull'   => $addressFull,
            'timeLabel'     => $timeLabel,
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


        // ดึงรายการสนามย่อยจาก stadium_fields
        $fieldRows = $fieldModel
            ->where('stadium_id', $id)
            ->where('status', 'active')
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
