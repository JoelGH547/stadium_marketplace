<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ==========================================================
// --- 1. PUBLIC ROUTES (หน้าแรก & Login ลูกค้า) ---
// ==========================================================
$routes->get('/', static function () {
    return redirect()->to('/login');
});

$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::processLogin');
$routes->get('register', 'AuthController::register');
$routes->post('register', 'AuthController::processRegister');
$routes->get('logout', 'AuthController::logout');


// ==========================================================
// --- 2. ADMIN SECTION (ต้อง Login เป็น Admin) ---
// ==========================================================
$routes->get('admin/login', 'admin\AdminAuthController::login');
$routes->post('admin/login', 'admin\AdminAuthController::processLogin');
$routes->get('admin/logout', 'admin\AdminAuthController::logout');

$routes->group('admin', ['filter' => ['auth', 'admin']], static function ($routes) {
    
    $routes->get('dashboard', 'admin\DashboardController::index');

    // --- Categories ---
    $routes->get('categories', 'admin\CategoryController::index');
    $routes->get('categories/new', 'admin\CategoryController::new');
    $routes->post('categories/create', 'admin\CategoryController::create');
    $routes->get('categories/edit/(:num)', 'admin\CategoryController::edit/$1');
    $routes->post('categories/update/(:num)', 'admin\CategoryController::update/$1');
    $routes->get('categories/delete/(:num)', 'admin\CategoryController::delete/$1');

    // --- Stadiums ---
    $routes->get('stadiums', 'admin\StadiumController::index');
    $routes->get('stadiums/create', 'admin\StadiumController::create');
    $routes->post('stadiums', 'admin\StadiumController::store');
    $routes->get('stadiums/edit/(:num)', 'admin\StadiumController::edit/$1');
    $routes->post('stadiums/update/(:num)', 'admin\StadiumController::update/$1');
    $routes->get('stadiums/delete/(:num)', 'admin\StadiumController::delete/$1');
    
    // --- User Management ---
    $routes->group('users', static function ($routes) {
        
        // 1. หน้าแสดงรายการ (Read)
        $routes->get('admins', 'admin\UserController::admins');
        $routes->get('vendors', 'admin\UserController::vendors');
        $routes->get('customers', 'admin\UserController::customers');
        
        // [เพิ่มใหม่] หน้าลูกค้าใหม่ (24 ชม.)
        $routes->get('new_customers', 'admin\UserController::newCustomers');

        // 2. CRUD (Create, Edit, Delete)
        $routes->get('create/(:segment)', 'admin\UserController::create/$1');
        $routes->post('store/(:segment)', 'admin\UserController::store/$1');

        $routes->get('edit/(:segment)/(:num)', 'admin\UserController::edit/$1/$2');
        $routes->post('update/(:segment)/(:num)', 'admin\UserController::update/$1/$2');

        $routes->get('delete/(:segment)/(:num)', 'admin\UserController::delete/$1/$2');
    });
    
    // --- Vendor Approval ---
    $routes->get('vendors/pending', 'admin\UserController::pendingList');
    $routes->get('vendors/approve/(:num)', 'admin\UserController::approveVendor/$1');
    $routes->get('vendors/reject/(:num)', 'admin\UserController::rejectVendor/$1');

    // --- Bookings ---
    $routes->get('bookings/new', 'admin\BookingController::indexNew');
    $routes->get('bookings/pending', 'admin\BookingController::indexPending');
    $routes->get('bookings/cancel/(:num)', 'admin\BookingController::cancel/$1');
});


// ==========================================================
// --- 3. CUSTOMER SECTION (Frontend) ---
// ==========================================================
$routes->group('customer', ['filter' => ['auth', 'customer']], static function ($routes) {
    $routes->get('dashboard', 'CustomerController::index');
    $routes->get('booking/stadium/(:num)', 'BookingController::viewStadium/$1');
    $routes->post('booking/process', 'BookingController::processBooking');
    $routes->get('payment/checkout/(:num)', 'BookingController::checkout/$1');
    $routes->post('payment/process', 'BookingController::processPayment');
    $routes->get('payment/success/(:num)', 'BookingController::paymentSuccess/$1');
});

$routes->get('sport', 'customer\HomeController::index');
$routes->get('sport/view', 'customer\HomeController::view');
$routes->get('sport/show/(:num)', 'customer\HomeController::show/$1');