<?php

namespace App\Models;

use CodeIgniter\Model;

class StockMovementModel extends Model
{
    protected $table = 'stock_movements';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'product_id', 
        'type', 
        'quantity', 
        'reference', 
        'user_id'
    ];

    // **แก้ไข:** ปิดการจัดการ Timestamp ของ CI4
    // เราจะปล่อยให้ฐานข้อมูล (MySQL) จัดการ 'timestamp' column เอง
    protected $useTimestamps = false;
    protected $createdField  = '';
    protected $updatedField  = '';
}