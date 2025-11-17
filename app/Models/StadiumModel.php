<?php

namespace App\Models;

use CodeIgniter\Model;

class StadiumModel extends Model
{
    protected $table            = 'stadiums';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';

    protected $allowedFields    = [
        'name',
        'price',
        'description',
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
        'outside_images', // JSON string
        'inside_images',  // JSON string
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * ดึงสนามของ vendor รายใดรายหนึ่ง
     * - ถ้าไม่ส่ง $stadiumId => คืนทั้งหมดของ vendor นั้น
     * - ถ้าส่ง $stadiumId => คืนเฉพาะสนามที่ต้องการ (ใช้กับหน้าแก้ไข)
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
