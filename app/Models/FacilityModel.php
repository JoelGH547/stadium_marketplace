<?php namespace App\Models;

use CodeIgniter\Model;

class FacilityModel extends Model
{
    protected $table = 'facilities';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'icon']; 

    public function getFacilitiesByCategory($categoryId = null)
    {
        $builder = $this->db->table('facilities');
        $builder->select('facilities.*, categories.name as category_name');
        $builder->join('facility_categories', 'facility_categories.facility_id = facilities.id', 'left');
        $builder->join('categories', 'categories.id = facility_categories.category_id', 'left');

        if ($categoryId != null) {
            $builder->groupStart();
                $builder->where('categories.id', $categoryId); 
                $builder->orWhere('categories.id', NULL);      
            $builder->groupEnd();
        }
        
        $builder->orderBy('categories.id', 'DESC');
        return $builder->get()->getResultArray();
    }
}