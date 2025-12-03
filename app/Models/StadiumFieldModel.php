<?php

namespace App\Models;

use CodeIgniter\Model;

class StadiumFieldModel extends Model
{
    protected $table            = 'stadium_fields';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'stadium_id',
        'name',
        'short_description',
        'description',
        'price',
        'price_daily',
        'status',
        'outside_images', // [ใหม่]
        'inside_images'   // [ใหม่]
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
