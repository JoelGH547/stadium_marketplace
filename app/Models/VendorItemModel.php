<?php

namespace App\Models;

use CodeIgniter\Model;

class VendorItemModel extends Model
{
    // 1. เปลี่ยนชื่อตารางเป็น vendor_items
    protected $table            = 'vendor_items';
    protected $primaryKey       = 'id';

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    // 2. Allowed Fields ครบถ้วนตามไฟล์เก่า
    protected $allowedFields    = [
        'stadium_facility_id',
        'name',
        'description',
        'price',
        'unit',
        'image',
        'status',
        'created_at',
        'updated_at',
    ];

    // 3. ตั้งค่า Timestamps
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * ดึงสินค้า + ข้อมูลสัมพันธ์สนาม/พื้นที่สนาม/หมวดหมู่
     */
    public function withRelations()
    {
        return $this
            ->select(
                'vendor_items.*,' . // ** แก้ตรงนี้เป็น vendor_items
                'stadium_fields.name   AS field_name,' .
                'stadiums.name         AS stadium_name,' .
                'facility_types.name   AS facility_type_name'
            )
            // ** แก้ตรงนี้เป็น vendor_items.stadium_facility_id
            ->join('stadium_facilities', 'stadium_facilities.id = vendor_items.stadium_facility_id', 'left')
            ->join('stadium_fields',    'stadium_fields.id = stadium_facilities.field_id', 'left')
            ->join('stadiums',          'stadiums.id = stadium_fields.stadium_id', 'left')
            ->join('facility_types',    'facility_types.id = stadium_facilities.facility_type_id', 'left');
    }
}