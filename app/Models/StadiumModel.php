<?php

namespace App\Models;

use CodeIgniter\Model;

class StadiumModel extends Model
{
    // กำหนดชื่อตารางฐานข้อมูลที่ Model นี้จะใช้งาน
    protected $table         = 'stadiums'; // เปลี่ยนจาก 'products'
    
    // กำหนด Primary Key ของตาราง
    protected $primaryKey    = 'id'; 

    protected $useAutoIncrement = true; 
    protected $returnType    = 'array'; 
    protected $useSoftDelete = false; 

    // ฟิลด์ที่อนุญาตสำหรับ 'สนามกีฬา' (ตัด 'stock' ออก)
    protected $allowedFields = ['name', 'price', 'description', 'category_id'];

    protected $useTimestamps = true; 
    protected $dateFormat    = 'datetime'; 
    protected $createdField  = 'created_at'; 
    protected $updatedField  = 'updated_at'; 
    protected $deletedField  = 'deleted_at'; 

    // เมธอดสำหรับดึง 'สนามกีฬา' พร้อมชื่อ Category (เช่น ประเภท: สนามฟุตซอล, สนามแบด)
    public function getStadiumsWithCategory($id = null)
    {
        $builder = $this->select('stadiums.*, categories.name as category_name') // เปลี่ยน 'products'
                         ->join('categories', 'categories.id = stadiums.category_id', 'left'); // เปลี่ยน 'products'

        if ($id !== null) {
            return $builder->where('stadiums.id', $id)->first(); // เปลี่ยน 'products'
        }

        return $builder->findAll();
    }
}