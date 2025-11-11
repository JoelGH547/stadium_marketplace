<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    // 1. ชื่อตารางที่เชื่อมต่อ
    protected $table         = 'admins';
    protected $primaryKey    = 'id';
    protected $useAutoIncrement = true;

    // 2. ฟิลด์ที่อนุญาตให้บันทึก (ตรงกับ Migration)
    protected $allowedFields = [
        'username',
        'email',
        'password_hash' 
        // ไม่ต้องใส่ created_at, updated_at ตรงนี้
    ];

    // 3. เปิดใช้งาน Timestamps (CI4 จะจัดการอัตโนมัติ)
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // (เราจะเพิ่มฟังก์ชันค้นหา User, Hash Password ฯลฯ ที่นี่ในอนาคต)
}