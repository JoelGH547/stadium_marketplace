<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model
{
    protected $table            = 'bookings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields    = [
        'customer_id',
        'stadium_id',
        'vendor_id',
        'booking_start_time', 
        'booking_end_time',
        'total_price',
        'status',
        'is_viewed_by_admin',
        'slip_image' 
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // [แก้ไขแล้ว] เปลี่ยนจาก users เป็น customers ให้ตรงกับ Database
    public function getAllBookings()
    {
        return $this->select('bookings.*, 
                              customers.full_name as customer_name, 
                              customers.phone_number as customer_phone, 
                              stadiums.name as stadium_name, 
                              vendors.vendor_name')
                    // แก้บรรทัดนี้: เปลี่ยน users -> customers
                    ->join('customers', 'customers.id = bookings.customer_id', 'left') 
                    ->join('stadiums', 'stadiums.id = bookings.stadium_id', 'left')
                    ->join('vendors', 'vendors.id = bookings.vendor_id', 'left')
                    ->orderBy('bookings.created_at', 'DESC')
                    ->findAll();
    }
}