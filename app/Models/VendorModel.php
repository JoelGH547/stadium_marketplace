<?php

namespace App\Models;

use CodeIgniter\Model;

class VendorModel extends Model
{
    // 1. ชื่อตารางที่เชื่อมต่อ
    protected $table         = 'vendors';
    protected $primaryKey    = 'id';
    protected $useAutoIncrement = true;

    // 2. ฟิลด์ที่อนุญาตให้บันทึก (ตรงกับ Migration)
    protected $allowedFields = [
        // ข้อมูลล็อคอิน
        'username',
        'email',
        'password_hash', 
        
        // คอลัมน์พิเศษของ Vendor
        'vendor_name',
        'phone_number',
        'tax_id',
        'bank_account'
    ];

    // 3. เปิดใช้งาน Timestamps (CI4 จะจัดการอัตโนมัติ)
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}