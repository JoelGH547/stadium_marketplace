<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    // ชื่อตาราง
    protected $table         = 'categories';

    // Primary Key
    protected $primaryKey    = 'id';

    protected $useAutoIncrement = true;
    protected $returnType    = 'array';

    /**
     * ฟิลด์ที่อนุญาตให้ insert/update
     * ถ้าเพื่อนมีฟิลด์อื่น เช่น icon, description ก็เพิ่มในลิสต์นี้ได้
     */
    protected $allowedFields = [
        'name', 'emoji'
    ];

    // ถ้าตารางมี created_at, updated_at ให้เปิด
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
