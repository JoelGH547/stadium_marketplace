<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAdminsTable extends Migration
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
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'unique'     => true, // username ห้ามซ้ำ
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'unique'     => true, // email ห้ามซ้ำ
            ],
            'password_hash' => [ // ใช้ชื่อนี้แทน 'password' จะชัดเจนกว่า
                'type'       => 'VARCHAR',
                'constraint' => '255', // ต้องยาวพอสำหรับเก็บรหัสผ่านที่ hash แล้ว
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true); // กำหนด id เป็น Primary Key
        $this->forge->createTable('admins'); // สร้างตารางชื่อ 'admins'
    }

    public function down()
    {
        $this->forge->dropTable('admins'); // คำสั่งสำหรับเวลายกเลิก
    }
}