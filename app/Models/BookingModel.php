<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model
{
    protected $table            = 'bookings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';

    protected $allowedFields    = [
        'customer_id',
        'stadium_id',
        'vendor_id',
        'booking_start_time',
        'booking_end_time',
        'total_price',
        'status',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // ตรงนี้ในอนาคตเราสามารถเพิ่ม join กับ customers / stadiums ได้
    // เช่น method: getBookingsWithRelations() ฯลฯ
}
