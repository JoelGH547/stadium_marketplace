<?php namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model
{
    protected $table = 'bookings';
    protected $primaryKey = 'id';
    
    // [สำคัญ] ต้องใส่ชื่อฟิลด์ให้ครบ ไม่งั้นบันทึกไม่ได้
    protected $allowedFields = [
        'customer_id', 
        'stadium_id', 
        'field_id', 
        'vendor_id', 
        'booking_start_time', 
        'booking_end_time', 
        'total_price', 
        'status', 
        'slip_image'
    ];
    
    protected $useTimestamps = true;

    // ฟังก์ชันดึงข้อมูลแบบ Join ครบทุกตาราง
    public function getAllBookings()
    {
        return $this->select('bookings.*, 
                              customers.full_name as customer_name, 
                              customers.phone_number as customer_phone, 
                              stadiums.name as stadium_name, 
                              stadium_fields.name as field_name, 
                              vendors.vendor_name')
                    // Join กับตารางลูกค้า (customers)
                    ->join('customers', 'customers.id = bookings.customer_id', 'left') 
                    // Join กับตารางสนาม (stadiums)
                    ->join('stadiums', 'stadiums.id = bookings.stadium_id', 'left')
                    // Join กับตารางสนามย่อย (stadium_fields)
                    ->join('stadium_fields', 'stadium_fields.id = bookings.field_id', 'left')
                    // Join กับตารางเจ้าของสนาม (vendors)
                    ->join('vendors', 'vendors.id = bookings.vendor_id', 'left')
                    
                    ->orderBy('bookings.created_at', 'DESC')
                    ->findAll();
    }
}