<?php

namespace App\Models;

use CodeIgniter\Model;

class StadiumModel extends Model
{
    // กำหนดชื่อตารางฐานข้อมูลที่ Model นี้จะใช้งาน
    protected $table         = 'stadiums'; 
    
    // กำหนด Primary Key ของตาราง
    protected $primaryKey    = 'id'; 

    protected $useAutoIncrement = true; 
    protected $returnType    = 'array'; 
    protected $useSoftDelete = false; 

    // --- ⬇️ 1. อัปเดตบรรทัดนี้ ⬇️ ---
    // (เพิ่ม 'vendor_id' เข้าไปในฟิลด์ที่อนุญาต)
    protected $allowedFields = ['name', 'price', 'description', 'category_id', 'vendor_id'];

    protected $useTimestamps = true; 
    protected $dateFormat    = 'datetime'; 
    protected $createdField  = 'created_at'; 
    protected $updatedField  = 'updated_at'; 
    protected $deletedField  = 'deleted_at'; 

    /**
     * (ฟังก์ชันเก่า: สำหรับ Admin)
     * ดึง 'สนามกีฬา' พร้อมชื่อ Category (เช่น ประเภท: สนามฟุตซอล, สนามแบด)
     */
    public function getStadiumsWithCategory($id = null)
    {
        $builder = $this->select('stadiums.*, categories.name as category_name')
                         ->join('categories', 'categories.id = stadiums.category_id', 'left');

        if ($id !== null) {
            return $builder->where('stadiums.id', $id)->first();
        }

        return $builder->findAll();
    }
    
    // --- ⬇️ 2. เพิ่มฟังก์ชันใหม่ (สำหรับ Vendor) ⬇️ ---
    /**
     * (ฟังก์ชันใหม่: สำหรับ Vendor)
     * ดึง 'สนามกีฬา' (พร้อม Category) ...เฉพาะที่ 'vendor_id' ตรงกัน
     */
    public function getStadiumsByVendor($vendorId, $stadiumId = null)
    {
        $builder = $this->select('stadiums.*, categories.name as category_name')
                         ->join('categories', 'categories.id = stadiums.category_id', 'left')
                         ->where('stadiums.vendor_id', $vendorId); // ⬅️ กรองเฉพาะของ Vendor คนนี้

        if ($stadiumId !== null) {
            // (สำหรับหน้า Edit)
            return $builder->where('stadiums.id', $stadiumId)->first();
        }

        return $builder->findAll();
    }
}