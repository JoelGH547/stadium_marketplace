<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTrackingToBookings extends Migration
{
    public function up()
    {
        // 1. กำหนดคอลัมน์ใหม่ 2 คอลัมน์
        $fields = [
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'confirmed', 'cancelled', 'completed'],
                'default' => 'pending',
                'null' => false,
                // (เราจะเพิ่ม 'after' => ... (คอลัมน์สุดท้าย) ที่นี่, แต่ถ้าไม่ชัวร์ ก็ไม่ต้องใส่ครับ)
            ],
            'is_viewed_by_admin' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0, // 0 = ยังไม่เห็น
                'null' => false,
            ]
        ];

        // 2. สั่งเพิ่มคอลัมน์นี้ลงในตาราง 'bookings'
        $this->forge->addColumn('bookings', $fields);
    }

    public function down()
    {
        // 3. (สำหรับยกเลิก) สั่งลบคอลัมน์
        $this->forge->dropColumn('bookings', 'status');
        $this->forge->dropColumn('bookings', 'is_viewed_by_admin');
    }
}