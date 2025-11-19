<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model
{
    protected $table            = 'bookings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    // ⬇️ (เพิ่ม) is_viewed_by_admin เข้าไปในนี้ครับ ⬇️
    protected $allowedFields    = [
        'customer_id',
        'stadium_id',
        'vendor_id',
        'booking_start_time', 
        'booking_end_time',
        'total_price',
        'status',
        
        'is_viewed_by_admin' // 👈 (เพิ่มบรรทัดนี้!) สำคัญมาก
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}