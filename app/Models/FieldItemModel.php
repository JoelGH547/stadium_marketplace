<?php namespace App\Models;

use CodeIgniter\Model;

class FieldItemModel extends Model
{
    protected $table            = 'field_items'; 
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields    = [
        'stadium_id',
        'field_id',      
        'product_id',    
        'custom_price',  
    ];

    protected $useTimestamps = true;

    
    public function getItemsByField($fieldId = null, $stadiumId = null)
    {
        
        $builder = $this->select('
            field_items.id as item_id, 
            field_items.custom_price, 
            field_items.status as item_status,
            p.name, 
            p.image, 
            p.unit, 
            p.price,
            types.name as type_name
        ');
        
        $builder->join('vendor_products p', 'p.id = field_items.product_id');
        $builder->join('facility_types types', 'types.id = p.facility_type_id', 'left');

        if ($fieldId) {
            $builder->where('field_items.field_id', $fieldId);
        } else if ($stadiumId) {
            $builder->where('field_items.stadium_id', $stadiumId)
                    ->where('field_items.field_id', null);
        }

        return $builder->findAll();
    }
}