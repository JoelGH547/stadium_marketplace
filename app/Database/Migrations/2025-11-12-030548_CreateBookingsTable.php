<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBookingsTable extends Migration
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
            // 1. ⬇️ "ใคร" (Customer) จอง ⬇️
            'customer_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            // 2. ⬇️ "สนามไหน" (Stadium) ที่ถูกจอง ⬇️
            'stadium_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            // 3. ⬇️ "ของใคร" (Vendor) (เพื่อความง่ายในการค้นหาของ Vendor) ⬇️
            'vendor_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            // 4. ⬇️ "เวลาไหน" ⬇️
            'booking_start_time' => [ // วันที่และเวลาที่เริ่มจอง
                'type' => 'DATETIME',
            ],
            'booking_end_time' => [ // วันที่และเวลาที่สิ้นสุด
                'type' => 'DATETIME',
            ],
            'total_price' => [ // ราคารวม (คำนวณจาก ชม. x ราคา)
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'status' => [ // สถานะการจอง
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'pending', // (เช่น: 'pending', 'confirmed', 'cancelled')
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
        
        // 5. ⬇️ (แนะนำ) สร้าง "Foreign Keys" (ตัวเชื่อมโยง) ⬇️
        // (ถ้าลบ Customer... การจองนี้จะถูกลบไปด้วย)
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'CASCADE');
        // (ถ้าลบ Stadium... การจองนี้จะถูกลบไปด้วย)
        $this->forge->addForeignKey('stadium_id', 'stadiums', 'id', 'CASCADE', 'CASCADE');
        // (ถ้าลบ Vendor... การจองนี้จะถูกลบไปด้วย)
        $this->forge->addForeignKey('vendor_id', 'vendors', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('bookings'); // สร้างตารางชื่อ 'bookings'
    }

    public function down()
    {
        $this->forge->dropTable('bookings');
    }
}