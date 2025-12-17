<?php

namespace App\Controllers\customer;

use App\Controllers\BaseController;
use App\Models\StadiumModel;
use App\Models\CategoryModel;
use App\Models\StadiumFieldModel;
use App\Models\StadiumReviewModel;

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

        foreach ($venueCards as &$v) {
            // ชื่อประเภท
            $catName  = (string)($v['category_name']  ?? '');
            $catEmoji = (string)($v['category_emoji'] ?? '');

            // ถ้าไม่มี emoji ใน DB ให้ fallback เป็นไอคอนสนามทั่วไป
            $v['type_icon']  = $catEmoji !== '' ? $catEmoji : '🏟️';
            $v['type_label'] = $catName  !== '' ? $catName  : 'สนามกีฬา';

            // ดาวรีวิว (ถ้าไม่มีรีวิวให้เป็น 0)
            $sid = (int) ($v['id'] ?? 0);
            $avg = $summaries[$sid]['avg'] ?? 0.0;
            $v['rating'] = $avg > 0 ? round((float)$avg, 1) : 0.0;

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
            'heroUrl'    => 'assets/images/batminton.webp',
            'title'      => 'จองสนามกีฬาออนไลน์',
            'venueCards' => $venueCards,
        ];

        return view('public/home', $data);
    }
}
