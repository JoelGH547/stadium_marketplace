<?php namespace App\Models;

use CodeIgniter\Model;

class FacilityCategoryModel extends Model
{
    protected $table = 'facility_categories';
    protected $primaryKey = 'id';
    protected $allowedFields = ['facility_id', 'category_id'];
}