<?php
namespace App\Models;

use CodeIgniter\Model;

class OwnerModel extends Model
{
    protected $table = 'vendors';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'username',
        'vendor_name',     // ชื่อเจ้าของ
        'lastname',
        'birthday',
        'province',

        'email',
        'phone_number',
        'tax_id',
        'bank_account',
        'password_hash'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
