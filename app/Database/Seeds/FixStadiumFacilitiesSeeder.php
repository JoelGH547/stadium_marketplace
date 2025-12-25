<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FixStadiumFacilitiesSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();

        echo "Checking stadium_facilities table...\n";

        // 1. Add stadium_id if it doesn't exist
        $fields = $db->getFieldNames('stadium_facilities');
        if (!in_array('stadium_id', $fields)) {
            echo "Adding stadium_id column...\n";
            $forge->addColumn('stadium_facilities', [
                'stadium_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'id',
                ],
            ]);
        } else {
            echo "stadium_id already exists.\n";
        }

        // 2. Make field_id nullable
        echo "Making field_id nullable...\n";
        $forge->modifyColumn('stadium_facilities', [
            'field_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);

        // 3. Migrate data from _SYSTEM_CATALOG_
        echo "Migrating _SYSTEM_CATALOG_ items...\n";
        $db->query("
            UPDATE stadium_facilities sf
            JOIN stadium_fields f ON sf.field_id = f.id
            SET sf.stadium_id = f.stadium_id, sf.field_id = NULL
            WHERE f.name = '_SYSTEM_CATALOG_'
        ");

        // 4. Delete the _SYSTEM_CATALOG_ fields
        echo "Deleting _SYSTEM_CATALOG_ fields...\n";
        $db->query("DELETE FROM stadium_fields WHERE name = '_SYSTEM_CATALOG_'");

        echo "Done!\n";
    }
}
