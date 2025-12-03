<?php namespace App\Models;

use CodeIgniter\Model;

class FieldItemModel extends Model
{
    protected $table            = 'field_items'; // เชื่อมกับตารางหน้าร้าน
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields    = [
        'stadium_id',
        'field_id',      // NULL ได้ (สำหรับสนามเดี่ยว)
        'product_id',    // สินค้าตัวไหน
        'custom_price',  // ราคาขายจริง
        'status'
    ];

    protected $useTimestamps = true;

    // ฟังก์ชันพิเศษ: ดึงสินค้าที่ขายในสนามนี้ พร้อมรายละเอียดจากคลัง
    public function getItemsByField($fieldId = null, $stadiumId = null)
    {
        // เลือกข้อมูลจากตารางลูก (field_items) + ตารางแม่ (vendor_products) + หมวดหมู่
        $builder = $this->select('
            field_items.id as item_id, 
            field_items.custom_price, 
            field_items.status as item_status,
            p.name, 
            p.image, 
            p.unit, 
            p.base_price,
            types.name as type_name
        ');
        
        $builder->join('vendor_products p', 'p.id = field_items.product_id');
        $builder->join('facility_types types', 'types.id = p.facility_type_id', 'left');

        // กรองตามเงื่อนไข (Complex หรือ Single)
        if ($fieldId) {
            // กรณี Complex: ดึงเฉพาะของสนามย่อยนี้
            $builder->where('field_items.field_id', $fieldId);
        } else if ($stadiumId) {
            // กรณี Single: field_id ต้องเป็น NULL
            $builder->where('field_items.stadium_id', $stadiumId)
                    ->where('field_items.field_id', null);
        }

        return $builder->findAll();
    }
}