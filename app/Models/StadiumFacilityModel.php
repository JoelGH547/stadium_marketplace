<?php namespace App\Models;

use CodeIgniter\Model;

class StadiumFacilityModel extends Model
{
    protected $table            = 'stadium_facilities';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // ⭐⭐⭐ ต้องอนุญาตให้ครบทุกคอลัมน์ที่มีใน Database ⭐⭐⭐
    protected $allowedFields    = [
        'stadium_id', 
        'field_id',      // สำคัญมาก! เพื่อผูกกับสนามย่อย
        'type_id',       // ประเภทสิ่งอำนวยความสะดวก
        'name'           // ชื่อรายละเอียด (เช่น "แอร์ 2 ตัว")
    ];

    // เปิดใช้ Timestamp เพื่อบันทึกเวลา created_at, updated_at
    protected $useTimestamps = true; 
}
