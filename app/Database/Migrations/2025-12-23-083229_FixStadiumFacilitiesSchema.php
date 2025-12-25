<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixStadiumFacilitiesSchema extends Migration
{
    public function up()
    {
        // 1. Add stadium_id after id
        $this->forge->addColumn('stadium_facilities', [
            'stadium_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id',
            ],
        ]);

        // 2. Make field_id nullable
        $this->forge->modifyColumn('stadium_facilities', [
            'field_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);

        // 3. Migrate data from _SYSTEM_CATALOG_
        $db = \Config\Database::connect();
        $db->query("
            UPDATE stadium_facilities sf
            JOIN stadium_fields f ON sf.field_id = f.id
            SET sf.stadium_id = f.stadium_id, sf.field_id = NULL
            WHERE f.name = '_SYSTEM_CATALOG_'
        ");
        
        // 4. Optionally delete the _SYSTEM_CATALOG_ fields
        $db->query("DELETE FROM stadium_fields WHERE name = '_SYSTEM_CATALOG_'");
    }

    public function down()
    {
        $this->forge->dropColumn('stadium_facilities', 'stadium_id');
        $this->forge->modifyColumn('stadium_facilities', [
            'field_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
        ]);
    }
}
