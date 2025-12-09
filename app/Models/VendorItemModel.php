<?php
namespace App\Models;

use CodeIgniter\Model;

class VendorItemModel extends Model
{
    protected $table = 'vendor_items';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'vendor_id',
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
}
