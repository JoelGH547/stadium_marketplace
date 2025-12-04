<?php

namespace App\Models;

use CodeIgniter\Model;

class VendorProductModel extends Model
{
    // ชื่อตารางใน Database (ตรงกับรูป image_91fde1.jpg)
    protected $table            = 'vendor_products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    
    protected $allowedFields    = [
        'stadium_id',       
        'facility_type_id', 
        'name', 
        'description', 
        'price',           
        'unit', 
        'image', 
        'status'
    ];

    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}