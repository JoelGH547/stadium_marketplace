<?php

namespace App\Models;

use CodeIgniter\Model;

class VendorItemModel extends Model
{
    // =========================================================================
    // ⚙️ การตั้งค่าตาราง (Table Configuration)
    // =========================================================================
    protected $table            = 'vendor_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false; // ถ้าใน DB ไม่มีคอลัมน์ deleted_at ให้ตั้งเป็น false

    // =========================================================================
    // 🛡️ ฟิลด์ที่อนุญาตให้แก้ไข (Allowed Fields)
    // =========================================================================
    protected $allowedFields    = [
        'vendor_id', 
        'facility_type_id', 
        'name', 
        'description', 
        'price', 
        'unit', 
        'image', 
        'status'
    ];

    // =========================================================================
    // 🕒 การจัดการเวลา (Timestamps)
    // =========================================================================
    // ตั้งเป็น true เพราะใน Database มีคอลัมน์ created_at, updated_at
    protected $useTimestamps = true; 
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // =========================================================================
    // 🔧 ฟังก์ชันเสริม (Custom Methods)
    // =========================================================================

    /**
     * ดึงข้อมูลสินค้า พร้อมชื่อร้าน (Vendor) และ หมวดหมู่ (Type)
     * ใช้สำหรับแสดงในตารางหน้า Admin หรือหน้าบ้าน
     */
    public function getItemsWithDetails()
    {
        return $this->select('vendor_items.*, vendors.vendor_name, facility_types.name as type_name')
                    ->join('vendors', 'vendors.id = vendor_items.vendor_id')
                    ->join('facility_types', 'facility_types.id = vendor_items.facility_type_id', 'left')
                    ->orderBy('vendors.vendor_name', 'ASC') // เรียงตามชื่อร้าน
                    ->findAll();
    }

    /**
     * ดึงสินค้าเฉพาะของ Vendor เจ้าใดเจ้าหนึ่ง (ใช้ตอนแสดงรายละเอียดสนาม หรือหน้า Profile ร้าน)
     * @param int $vendor_id
     */
    public function getItemsByVendor($vendor_id)
    {
        return $this->select('vendor_items.*, facility_types.name as type_name')
                    ->join('facility_types', 'facility_types.id = vendor_items.facility_type_id', 'left')
                    ->where('vendor_items.vendor_id', $vendor_id)
                    ->where('vendor_items.status', 'active') // เอาเฉพาะที่สถานะ Active
                    ->findAll();
    }
}