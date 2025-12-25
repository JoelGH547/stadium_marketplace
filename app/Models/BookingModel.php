<?php namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model
{
    protected $table = 'bookings';
    protected $primaryKey = 'id';
    
    
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


    public function getAllBookings($vendorId = null)
    {
        $query = $this->select('bookings.*, 
                              customers.full_name as customer_name, 
                              customers.phone_number as customer_phone, 
                              stadiums.name as stadium_name, 
                              stadium_fields.name as field_name, 
                              vendors.vendor_name')
                    ->join('customers', 'customers.id = bookings.customer_id', 'left') 
                    ->join('stadiums', 'stadiums.id = bookings.stadium_id', 'left')
                    ->join('stadium_fields', 'stadium_fields.id = bookings.field_id', 'left')
                    ->join('vendors', 'vendors.id = bookings.vendor_id', 'left');

        if ($vendorId !== null) {
            $query->where('bookings.vendor_id', $vendorId);
        }

        return $query->orderBy('bookings.created_at', 'DESC')
                     ->findAll();
    }

    public function getBookingsByCustomerId($customerId)
{
    return $this->select('bookings.*, 
                          stadiums.name as stadium_name, 
                          stadium_fields.name as field_name, 
                          vendors.vendor_name')
                ->join('stadiums', 'stadiums.id = bookings.stadium_id', 'left')
                ->join('stadium_fields', 'stadium_fields.id = bookings.field_id', 'left')
                ->join('vendors', 'vendors.id = bookings.vendor_id', 'left')
                ->where('bookings.customer_id', $customerId)
                ->orderBy('bookings.created_at', 'DESC')
                ->findAll();
}
/**
 * ดึงรายการจองของสนามย่อยในช่วงเวลา (สำหรับปฏิทิน)
 * เงื่อนไข overlap:
 *   booking_start_time < $end AND booking_end_time > $start
 */
public function getScheduleForField(int $fieldId, string $start, string $end): array
{
    return $this->select('id, booking_start_time, booking_end_time, status')
                ->where('field_id', $fieldId)
                ->whereIn('status', ['pending', 'confirmed'])
                ->where('booking_start_time <', $end)
                ->where('booking_end_time >', $start)
                ->orderBy('booking_start_time', 'ASC')
                ->findAll();
}
}
