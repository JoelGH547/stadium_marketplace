<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    // กำหนดชื่อตารางฐานข้อมูลที่ Model นี้จะใช้งาน
    protected $table         = 'categories';
    
    // กำหนด Primary Key ของตาราง
    protected $primaryKey    = 'id';

    protected $useAutoIncrement = true;
    protected $returnType    = 'array';

    // ฟิลด์ที่อนุญาตสำหรับ 'ประเภทสนาม'
    protected $allowedFields = ['name'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}