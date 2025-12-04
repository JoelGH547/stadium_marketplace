<?php

namespace App\Models;

use CodeIgniter\Model;

class FacilityTypeModel extends Model
{
    protected $table = 'facility_types';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name']; // Assuming 'name' is the field, similar to categories
    protected $returnType = 'array';
}
