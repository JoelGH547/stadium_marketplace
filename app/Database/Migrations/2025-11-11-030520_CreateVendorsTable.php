<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVendorsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            // --- ข้อมูลล็อคอินพื้นฐาน ---
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'unique'     => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'unique'     => true,
            ],
            'password_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            
            // --- ⬇️ คอลัมน์ "พิเศษ" สำหรับ Vendor ⬇️ ---
            'vendor_name' => [ // ชื่อเจ้าของ หรือ ชื่อบริษัท
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'phone_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
            'tax_id' => [ // เลขผู้เสียภาษี
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'bank_account' => [ // เลขบัญชี
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            // --- ⬆️ จบส่วนคอลัมน์พิเศษ ⬆️ ---

            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('vendors'); // สร้างตารางชื่อ 'vendors'
    }

    public function down()
    {
        $this->forge->dropTable('vendors');
    }
}
    