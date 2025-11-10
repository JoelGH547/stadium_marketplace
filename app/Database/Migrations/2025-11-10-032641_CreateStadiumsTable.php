<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStadiumsTable extends Migration
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
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'price' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2', // ราคา
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'category_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true, // ต้องตรงกับ 'id' ของ 'categories'
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
        $this->forge->addKey('id', true);
        // สร้าง Foreign Key เชื่อม 'category_id' ไปยัง 'id' ของตาราง 'categories'
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'CASCADE', 'NO ACTION');
        $this->forge->createTable('stadiums');
    }

    public function down()
    {
        $this->forge->dropTable('stadiums');
    }
}