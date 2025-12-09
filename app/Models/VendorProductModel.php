<?php

namespace App\Models;

use CodeIgniter\Model;

class VendorProductModel extends Model
{
    protected $table      = 'vendor_products';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
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

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * ดึงสินค้า + ข้อมูลสัมพันธ์สนาม/สนามย่อย/หมวดหมู่
     */
    public function withRelations()
    {
        return $this
            ->select(
                'vendor_products.*,' .
                'stadium_fields.name   AS field_name,' .
                'stadiums.name         AS stadium_name,' .
                'facility_types.name   AS facility_type_name'
            )
            ->join('stadium_facilities', 'stadium_facilities.id = vendor_products.stadium_facility_id')
            ->join('stadium_fields',    'stadium_fields.id = stadium_facilities.field_id')
            ->join('stadiums',          'stadiums.id = stadium_fields.stadium_id')
            ->join('facility_types',    'facility_types.id = stadium_facilities.facility_type_id');
    }
}
