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
        // ขั้นนี้เรายังไม่ยุ่ง DB ใช้ field.php ที่มีข้อมูลจำลองในตัว view ไปก่อน
        return view('public/field');
    }
}
