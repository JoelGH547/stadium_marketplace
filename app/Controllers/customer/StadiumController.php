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
            $stadiumModel      = new StadiumModel();
            $stadiumFieldModel = new StadiumFieldModel();

            $stadium = $stadiumModel->getStadiumsWithCategory($id);

            if (!$stadium) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('ไม่พบสนามที่ต้องการ');
            }
            $contactPhone = $stadium['phone'] ?? ($stadium['contact_phone'] ?? '');
            $contactEmail = $stadium['email'] ?? ($stadium['contact_email'] ?? '');
            // สนามย่อยทั้งหมดของสนามนี้
            $fields = $stadiumFieldModel
                ->where('stadium_id', $id)
                ->orderBy('name', 'ASC')
                ->findAll();

            // เตรียมข้อมูลพื้นฐานให้ view ใช้งานง่าย
            $cover    = $stadium['cover_image'] ?? null;
            $coverUrl = $cover
                ? base_url('assets/uploads/stadiums/' . $cover)
                : base_url('assets/uploads/home/1.jpg');

            $addressParts = array_filter([
                $stadium['address_line'] ?? '',
                $stadium['district']     ?? '',
                $stadium['province']     ?? '',
                $stadium['postal_code']  ?? '',
            ]);

            $addressFull = implode(' ', $addressParts);

            $open  = $stadium['open_time']  ?? null;
            $close = $stadium['close_time'] ?? null;

            if ($open && strlen($open) >= 5)   $open  = substr($open, 0, 5);
            if ($close && strlen($close) >= 5) $close = substr($close, 0, 5);
            $timeLabel = ($open && $close) ? ($open . ' – ' . $close) : 'ยังไม่ระบุเวลาเปิด–ปิด';

            // MOCK: ไอเทมของสนาม (ชั่วคราว - รอเชื่อม DB ฝั่ง vendor)
            $items = [
                [
                    'id'       => 1,
                    'name'     => 'ไม้แบด Yonex Pro',
                    'price'    => 50,
                    'unit'     => 'ชม.',
                    'category' => 'อุปกรณ์กีฬา',
                    'desc'     => 'ให้เช่าไม้แบดคุณภาพสูง 1 ชั่วโมง',
                ],
                [
                    'id'       => 2,
                    'name'     => 'ลูกแบดฝึกซ้อม (1 กระป๋อง)',
                    'price'    => 80,
                    'unit'     => 'ชุด',
                    'category' => 'อุปกรณ์กีฬา',
                    'desc'     => 'ลูกแบดสำหรับการซ้อมทั่วไป 1 กระป๋อง',
                ],
                [
                    'id'       => 3,
                    'name'     => 'นวดนักกีฬา 60 นาที',
                    'price'    => 300,
                    'unit'     => 'ครั้ง',
                    'category' => 'บริการเสริม',
                    'desc'     => 'บริการนวดคลายกล้ามเนื้อหลังการเล่นกีฬา',
                ],
                [
                    'id'       => 4,
                    'name'     => 'ห้องพักนักกีฬา (2 ชั่วโมง)',
                    'price'    => 200,
                    'unit'     => 'ครั้ง',
                    'category' => 'ห้องพัก',
                    'desc'     => 'ห้องพักผ่อนพร้อมแอร์สำหรับนักกีฬา',
                ],
            ];


            $data = [
                'stadium'     => $stadium,
                'coverUrl'    => $coverUrl,
                'addressFull' => $addressFull,
                'timeLabel'   => $timeLabel,
                'fields'      => $fields,
                'contactPhone'  => $contactPhone,
                'contactEmail'  => $contactEmail,
                'items'        => $items,
            ];

            return view('public/show', $data);
        }
}
