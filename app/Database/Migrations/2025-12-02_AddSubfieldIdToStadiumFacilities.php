<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSubfieldIdToStadiumFacilities extends Migration
{
    public function up()
    {
        $this->forge->addColumn('stadium_facilities', [
            'subfield_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'facility_id',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('stadium_facilities', 'subfield_id');
    }
}
