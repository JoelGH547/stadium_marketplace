<?php namespace App\Models;

use CodeIgniter\Model;

class VendorProductModel extends Model
{
    protected $table            = 'vendor_products'; // เชื่อมกับตารางคลังสินค้า
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    protected $allowedFields    = [
        'vendor_id', 
        'facility_type_id', // หมวดหมู่สินค้า
        'name', 
        'description', 
        'base_price',       // ราคาตั้งต้น
        'unit', 
        'image', 
        'status'
    ];

    protected $useTimestamps = true; 
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}