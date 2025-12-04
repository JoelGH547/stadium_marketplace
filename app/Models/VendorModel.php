<?php

namespace App\Models;

use CodeIgniter\Model;

class VendorModel extends Model
{
    protected $table            = 'vendors';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';

    protected $allowedFields    = [
        'username',
        'email',
        'password_hash',

        // Personal info
        'vendor_name',
        'lastname',
        'profile_image',
        'gender',
        'birthday',
        'province',
        'address',
        'district',
        'subdistrict',
        'zipcode',

        // Contact
        'phone_number',
        'line_id',
        'facebook_url',

        // Verification
        'tax_id',
        'citizen_id',
        'id_card_image',
        'bank_book_image',
        'verified_at',

        // Bank
        'bank_account',

        // Role + status
        'role',
        'status',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
