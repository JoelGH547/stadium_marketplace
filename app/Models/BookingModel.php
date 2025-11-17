<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model
{
    protected $table            = 'bookings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    // ⬇️ (สำคัญ) ต้องอนุญาตฟิลด์ทั้งหมดที่เราจะ Save/Update ⬇️
    protected $allowedFields    = [
        'customer_id',
        'stadium_id',
        'vendor_id',
        'booking_date',      // (ถ้าคุณใช้ booking_start_time ก็ต้องแก้ตรงนี้ให้ตรงกับ DB)
        'booking_start_time',
        'booking_end_time',
        'start_time',        // (ถ้าคุณยังใช้ start_time อยู่)
        'total_price',
        'status',
        'is_viewed_by_admin'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}