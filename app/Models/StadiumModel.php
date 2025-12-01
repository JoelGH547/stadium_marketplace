<?php

namespace App\Models;

use CodeIgniter\Model;

class StadiumModel extends Model
{
    protected $table            = 'stadiums';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    // ==========================================================
    //  ฟิลด์ที่อนุญาตให้บันทึก (Allowed Fields)
    // ==========================================================
    protected $allowedFields    = [
        'name', 
        'description', 
        'booking_type',   // [เพิ่มใหม่] รองรับ ENUM('single', 'complex')
        // 'price',       // [ลบออก] เพราะย้ายราคาไปที่ตาราง stadium_fields แล้ว
        'category_id', 
        'vendor_id',
        'open_time',
        'close_time',
        'contact_email',
        'contact_phone',
        'province',
        'address',
        'lat',
        'lng',
        'map_link',
        'outside_images', 
        'inside_images'   
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * 1. (ฟังก์ชันที่ Error ถามหา)
     * สำหรับ Admin และ Customer: ดึงข้อมูลสนามพร้อมชื่อหมวดหมู่
     */
    public function getStadiumsWithCategory($id = null)
    {
        $builder = $this->select('stadiums.*, categories.name as category_name, categories.emoji as category_emoji')
                        ->join('categories', 'categories.id = stadiums.category_id', 'left');

        if ($id !== null) {
            return $builder->where('stadiums.id', $id)->first();
        }

        return $builder->findAll();
    }

    /**
     * 2. (ฟังก์ชันสำหรับ Vendor)
     * ดึงข้อมูลสนาม เฉพาะของ Vendor คนนั้น
     */
    public function getStadiumsByVendor($vendorId, $stadiumId = null)
    {
        $builder = $this->select('stadiums.*, categories.name as category_name')
                        ->join('categories', 'categories.id = stadiums.category_id', 'left')
                        ->where('stadiums.vendor_id', $vendorId);

        if ($stadiumId !== null) {
            return $builder->where('stadiums.id', $stadiumId)->first();
        }

        return $builder->findAll();
    }
}