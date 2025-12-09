<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStadiumFacilities extends Migration
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
            'stadium_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'field_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'type_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
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
        $this->forge->addForeignKey('stadium_id', 'stadiums', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('field_id', 'stadium_fields', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_id', 'facility_types', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('stadium_facilities');
    }

    public function down()
    {
        $this->forge->dropTable('stadium_facilities');
    }
}
