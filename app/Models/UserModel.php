<?php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    // ชื่อตารางในฐานข้อมูล
    protected $table      = 'users';

    // ชื่อ Primary Key ของตาราง
    protected $primaryKey = 'id';

    // ฟิลด์ที่อนุญาต
    protected $allowedFields = [
        'username',
        'email',
        'password',
        'role'
    ];

    // เปิดใช้งานการบันทึกเวลา
    protected $useTimestamps = true;

    // กำหนดฟิลด์
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

}