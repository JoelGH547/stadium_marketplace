<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCustomersTable extends Migration
{
    public function up()
    {
        // ⬇️ --- แก้ไข $this.forge เป็น $this->forge --- ⬇️
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
            
            // --- ⬇️ คอลัมน์ "พิเศษ" สำหรับ Customer ⬇️ ---
            'full_name' => [ // ชื่อนามสกุลจริง
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'phone_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
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

        // ⬇️ --- แก้ไข $this.forge เป็น $this->forge --- ⬇️
        $this->forge->addKey('id', true);
        $this->forge->createTable('customers'); // สร้างตารางชื่อ 'customers'
    }

    public function down()
    {
        // ⬇️ --- แก้ไข $this.forge เป็น $this->forge --- ⬇️
        $this->forge->dropTable('customers');
    }
}