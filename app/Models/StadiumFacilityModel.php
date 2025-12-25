<?php

namespace App\Models;

use CodeIgniter\Model;

class StadiumFacilityModel extends Model
{

    protected $table            = 'stadium_facilities';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    // NOTE:
    // ตาราง stadium_facilities ปัจจุบันเหลือแค่ field_id + facility_type_id
    // ไม่ได้ใช้ stadium_id / name / timestamps แล้ว
    protected $allowedFields    = [
        'stadium_id',
        'field_id',
        'facility_type_id',
    ];

    protected $useTimestamps = false;
}
