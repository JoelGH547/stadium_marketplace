<?php

namespace App\Controllers\Owner;

use App\Controllers\BaseController;

class Welcome extends BaseController
{
    public function index()
    {
        return view('owner/welcome');
    }
}
