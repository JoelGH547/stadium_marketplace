<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    // ชื่อตาราง
    protected $table            = 'admins';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    // คืนค่าเป็น array
    protected $returnType       = 'array';

    // ฟิลด์ที่อนุญาตให้ insert/update
    protected $allowedFields    = [
        'username',
        'email',
        'password_hash',
    ];

    // Timestamp fields (created_at / updated_at)
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
