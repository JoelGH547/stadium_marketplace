<?php

namespace App\Models;

use CodeIgniter\Model;

class SubFieldModel extends Model
{
    protected $table = 'stadium_fields';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'stadium_id',
        'name',
        'price',
        'price_daily',

        'description',
        'outside_images',
        'inside_images',
        'status'
    ];
}
