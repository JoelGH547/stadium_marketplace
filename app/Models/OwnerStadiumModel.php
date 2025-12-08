<?php

namespace App\Models;

use CodeIgniter\Model;

class OwnerStadiumModel extends Model
{
    protected $table = 'stadiums';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'vendor_id',
        'category_id',
        'name',
        'price', 
        'description',
        
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
        'inside_images',

        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;

    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
