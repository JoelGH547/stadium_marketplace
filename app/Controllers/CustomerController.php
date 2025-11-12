<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\StadiumModel; // ⬅️ 1. (เพิ่ม) นำเข้า StadiumModel
use App\Models\CategoryModel; // ⬅️ 1. (เพิ่ม) นำเข้า CategoryModel
use App\Models\BookingModel; // ⬅️ 1. (เพิ่ม) นำเข้า BookingModel

class CustomerController extends BaseController
{
    protected $customerModel;
    protected $stadiumModel; // ⬅️ 2. ประกาศ Models
    protected $categoryModel;
    protected $bookingModel; 
    protected $customerId; 

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->stadiumModel = new StadiumModel(); // ⬅️ 3. สร้าง Instance
        $this->categoryModel = new CategoryModel();
        $this->bookingModel = new BookingModel(); 
        
        $this->customerId = session()->get('user_id'); 
    }
    
    /**
     * (INDEX) หน้า Dashboard ของ Customer
     * (แก้ไข: ให้ "ดึง" สนาม "ทั้งหมด" มาโชว์)
     */
    public function index()
    {
        // 1. ดึงข้อมูล Customer ที่ล็อคอินอยู่
        $customer = $this->customerModel->find($this->customerId);

        // 2. ⬇️ (อัปเกรด) ดึง "สนามกีฬา" (Stadiums) "ทั้งหมด" ⬇️
        $allStadiums = $this->stadiumModel->getStadiumsWithCategory(); 

        // 3. เตรียมข้อมูลส่งไปให้ View
        $data = [
            'title' => 'Customer Dashboard (Book a Stadium)',
            'customer' => $customer,
            'stadiums' => $allStadiums, // ⬅️ 4. (เพิ่ม) ส่ง "ทุกสนาม" ไป
        ];

        // 5. โหลด View ของ Dashboard
        return view('customer/dashboard', $data);
    }

    // --- (ฟังก์ชัน 'book()' และ 'processBooking()' ...
    // ...ที่เราสร้างไว้ในขั้นตอนที่ 22...
    // ...ถ้าไฟล์ของคุณ "ย้อน" (Reverted) ...มันอาจจะ "หาย" (Lost) ...ไป
    // ...ผมจะ "เพิ่ม" (Add) ...มันกลับเข้ามาให้ (อีกครั้ง) ...
    // ...เพื่อ "ป้องกัน" (Prevent) ...Error 404 (Method not found) ...ใน "ขั้นตอนต่อไป" (Next step) ...ครับ) ---

    /**
     * "แสดง" หน้าฟอร์มยืนยันการจอง (สำหรับสนามที่เลือก)
     */
    public function book($stadium_id = null)
    {
        $stadium = $this->stadiumModel->getStadiumsWithCategory($stadium_id);

        if (!$stadium) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Cannot find the stadium item: ' . $stadium_id);
        }

        $data = [
            'title' => 'Book Stadium: ' . $stadium['name'],
            'stadium' => $stadium, 
        ];

        return view('customer/bookings/create', $data);
    }

    /**
     * "รับ" (POST) ข้อมูลจากฟอร์มยืนยันการจอง ...แล้ว "บันทึก" (Save)
     */
    public function processBooking()
    {
        $rules = [
            'stadium_id' => 'required|integer',
            'vendor_id'  => 'required|integer',
            'booking_date' => 'required|valid_date',
            'start_time' => 'required', 
            'end_time'   => 'required',
            'total_price' => 'required|numeric',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $booking_start = $this->request->getVar('booking_date') . ' ' . $this->request->getVar('start_time');
        $booking_end   = $this->request->getVar('booking_date') . ' ' . $this->request->getVar('end_time');

        $data = [
            'customer_id'   => $this->customerId, 
            'stadium_id'    => $this->request->getVar('stadium_id'),
            'vendor_id'     => $this->request->getVar('vendor_id'),
            'booking_start_time' => $booking_start,
            'booking_end_time'   => $booking_end,
            'total_price'   => $this->request->getVar('total_price'),
            'status'        => 'pending', 
        ];

        $this->bookingModel->save($data);

        return redirect()->to('customer/dashboard')->with('success', 'Booking successful! Waiting for confirmation.');
    }

}