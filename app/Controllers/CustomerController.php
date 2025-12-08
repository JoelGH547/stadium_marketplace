<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StadiumModel; 

class CustomerController extends BaseController
{
    
    public function index()
    {
        
        $stadiumModel = new StadiumModel();

        
        $data = [
            'title' => 'Stadium Booking Dashboard',

            
            'stadiums' => $stadiumModel->getStadiumsWithCategory(),
        ];

        
        return view('customer/dashboard', $data);
    }
}
