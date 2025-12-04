<?php

namespace App\Models;

use CodeIgniter\Model;

class StadiumFacilityModel extends Model
{
    protected $table = 'stadium_facilities';
    protected $primaryKey = 'id';
    protected $allowedFields = ['stadium_id', 'field_id', 'type_id', 'name'];
    protected $useTimestamps = true;
}
