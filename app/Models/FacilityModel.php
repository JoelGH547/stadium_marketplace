<?php

namespace App\Models;

use CodeIgniter\Model;

class FacilityModel extends Model
{
    protected $table            = 'facilities';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['name', 'icon']; // ตัด category_id ออก เพราะย้ายไปตารางใหม่แล้ว

    // ฟังก์ชันดึงสิ่งอำนวยความสะดวก "ตามประเภทกีฬา"
    // (เผื่อ Admin อยากใช้ตอนสร้างสนาม หรือเผื่อเพื่อนฝั่ง Customer เอาไปใช้)
    public function getByCategoryId($categoryId)
    {
        return $this->select('facilities.*')
                    ->join('facility_categories', 'facility_categories.facility_id = facilities.id')
                    ->where('facility_categories.category_id', $categoryId)
                    ->findAll();
    }

    // ฟังก์ชันดึงสิ่งอำนวยความสะดวกที่เป็น "ส่วนกลาง" (ถ้ามี)
    // (เผื่อในอนาคตมีการเพิ่มกลับเข้ามา)
    public function getGeneralFacilities()
    {
        return $this->select('facilities.*')
                    ->join('facility_categories', 'facility_categories.facility_id = facilities.id')
                    ->where('facility_categories.category_id', NULL)
                    ->findAll();
    }
}