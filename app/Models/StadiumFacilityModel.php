<?php

namespace App\Models;

use CodeIgniter\Model;

class StadiumFacilityModel extends Model
{
    protected $table = 'stadium_facilities';
    protected $primaryKey = 'id';
    protected $allowedFields = ['field_id', 'facility_type_id'];
    protected $useTimestamps = false;
}
