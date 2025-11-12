<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model
{
    // 1. ⬇️ ชื่อตารางที่เชื่อมต่อ
    protected $table         = 'bookings';
    protected $primaryKey    = 'id';
    protected $useAutoIncrement = true;

    protected $returnType    = 'array';

    // 2. ⬇️ ฟิลด์ที่อนุญาตให้บันทึก (ตรงกับ Migration)
    protected $allowedFields = [
        'customer_id',
        'stadium_id',
        'vendor_id',
        'booking_start_time',
        'booking_end_time',
        'total_price',
        'status' 
    ];

    // 3. ⬇️ เปิดใช้งาน Timestamps (CI4 จะจัดการอัตโนมัติ)
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // (ในอนาคต เราจะมาเพิ่มฟังก์ชันดึง "การจอง" (Booking)
    // ...พร้อม "ชื่อลูกค้า" (Customer) และ "ชื่อสนาม" (Stadium) ที่นี่ครับ)
}