<?php

namespace App\Models;

use CodeIgniter\Model;

class StadiumFieldModel extends Model
{
    protected $table            = 'stadium_fields'; // ชื่อตารางใน DB
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // ฟิลด์ที่อนุญาตให้บันทึกได้
    protected $allowedFields    = ['stadium_id', 'name', 'description', 'status',];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}