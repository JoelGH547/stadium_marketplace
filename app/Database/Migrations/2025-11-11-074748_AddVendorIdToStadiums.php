<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVendorIdToStadiums extends Migration
{
    /**
     * นี่คือคำสั่ง "เพิ่ม" คอลัมน์
     */
    public function up()
    {
        $fields = [
            'vendor_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true, // ⬅️ ตั้งเป็น 'true' (เผื่อมีข้อมูลเก่า)
                'after'      => 'category_id', // ⬅️ ให้คอลัมน์นี้อยู่ "หลัง" category_id
            ],
        ];
        
        // สั่ง "เพิ่มคอลัมน์" ลงในตาราง 'stadiums'
        $this->forge->addColumn('stadiums', $fields);
    }

    /**
     * นี่คือคำสั่ง "ลบ" คอลัมน์ (เวลายกเลิก)
     */
    public function down()
    {
        $this->forge->dropColumn('stadiums', 'vendor_id');
    }
}