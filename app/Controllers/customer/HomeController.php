<?php

namespace App\Controllers\customer;

use App\Controllers\BaseController;
use App\Models\StadiumModel;
use App\Models\CategoryModel;
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
            ->findAll(20);

        foreach ($venueCards as &$v) {
            // ชื่อประเภท
            $catName  = (string)($v['category_name']  ?? '');
            $catEmoji = (string)($v['category_emoji'] ?? '');

            // ถ้าไม่มี emoji ใน DB ให้ fallback เป็นไอคอนสนามทั่วไป
            $v['type_icon']  = $catEmoji !== '' ? $catEmoji : '🏟️';
            $v['type_label'] = $catName  !== '' ? $catName  : 'สนามกีฬา';

            // รูปปกด้านนอกใบแรกจาก JSON
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

        $data = [
            'siteName'   => 'Stadium Marketplace',
            'heroUrl'    => 'assets/images/batminton.webp',
            'title'      => 'จองสนามกีฬาออนไลน์',
            'venueCards' => $venueCards,
        ];

        return view('public/home', $data);
    }

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
        'siteName'   => 'Stadium Marketplace',
        'venueCards' => $venueCards,
        'categories' => $categories,
    ];

    return view('public/view', $data);
    }
}
