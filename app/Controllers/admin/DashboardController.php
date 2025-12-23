<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\VendorModel;
use App\Models\CustomerModel;
use App\Models\StadiumModel;
use App\Models\BookingModel; 

class DashboardController extends BaseController
{
    protected $vendorModel;
    protected $customerModel;
    protected $stadiumModel;
    protected $bookingModel; 

    public function __construct()
    {
        $this->vendorModel   = new VendorModel();
        $this->customerModel = new CustomerModel();
        $this->stadiumModel  = new StadiumModel();
        $this->bookingModel  = new BookingModel(); 
    }

    public function index()
    {
        
        $pendingVendors = $this->vendorModel->where('status', 'pending')->countAllResults();

        
        $timeLimit = date('Y-m-d H:i:s', strtotime('-24 hours'));
        $newCustomers = $this->customerModel->where('created_at >=', $timeLimit)->countAllResults();

        
        $totalStadiums = $this->stadiumModel->countAllResults();

        
        $today = date('Y-m-d');
        
        $todayBookings = $this->bookingModel->like('created_at', $today, 'after')->countAllResults();
        
        
        $recentVendors = $this->vendorModel->orderBy('id', 'DESC')->findAll(5);
        $recentCustomers = $this->customerModel->orderBy('id', 'DESC')->findAll(5);

        $data = [
            'title'           => 'Admin Dashboard',
            'pendingCount'    => $pendingVendors,
            'newCustomerCount'=> $newCustomers,
            'stadiumCount'    => $totalStadiums,
            'todayBookings'   => $todayBookings, 
            'recentVendors'   => $recentVendors,
            'recentCustomers' => $recentCustomers
        ];

        return view('admin/dashboard', $data);
    }
}