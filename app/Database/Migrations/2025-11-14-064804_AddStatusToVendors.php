<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusToVendors extends Migration
{
    public function up()
    {
        // 1. กำหนดคอลัมน์ใหม่
        $field = [
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default' => 'pending',
                'after' => 'bank_account', // (ต่อจากคอลัมน์สุดท้ายของ Vendor)
                'null' => false,
            ],
        ];

        // 2. สั่งเพิ่มคอลัมน์นี้ลงในตาราง 'vendors'
        $this->forge->addColumn('vendors', $field);
    }

    public function down()
    {
        // 3. (สำหรับยกเลิก) สั่งลบคอลัมน์ 'status'
        $this->forge->dropColumn('vendors', 'status');
    }
}