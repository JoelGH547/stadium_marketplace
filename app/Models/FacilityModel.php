<?php namespace App\Models;

use CodeIgniter\Model;

class FacilityModel extends Model
{
    protected $table = 'facilities';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'icon']; 

    // ฟังก์ชันดึงสิ่งอำนวยความสะดวก โดยแยกตาม ID ของ Category
    // ถ้า $categoryId = null คือดึงทั้งหมด (เอาไว้ใช้หน้า Create)
    public function getFacilitiesByCategory($categoryId = null)
    {
        $builder = $this->db->table('facilities');
        $builder->select('facilities.*, categories.name as category_name, categories.id as cat_id');
        
        // Join เพื่อดูว่า Facility นี้เป็นของหมวดไหน
        $builder->join('facility_categories', 'facility_categories.facility_id = facilities.id', 'left');
        $builder->join('categories', 'categories.id = facility_categories.category_id', 'left');

        // Logic กรองข้อมูล
        if ($categoryId != null) {
            $builder->groupStart();
                $builder->where('categories.id', $categoryId); // ของหมวดที่เลือก
                $builder->orWhere('categories.id', NULL);      // หรือ ของส่วนกลาง (ไม่มีหมวด)
            $builder->groupEnd();
        }
        
        // เรียงลำดับให้สวยงาม (เอาหมวดขึ้นก่อน)
        $builder->orderBy('categories.id', 'DESC');
        
        return $builder->get()->getResultArray();
    }
}