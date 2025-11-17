<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ==========================================================
// --- 1. PUBLIC ROUTES (หน้าหลัก, Customer Auth) ---
// ==========================================================

// (หน้าหลัก: ให้เด้งไปหน้า Customer Login)
$routes->get('/', static function () {
    return redirect()->to('/login');
});

// (Auth สำหรับ Customer)
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::processLogin');
$routes->get('register', 'AuthController::register');
$routes->post('register', 'AuthController::processRegister');
$routes->get('logout', 'AuthController::logout'); // (Logout ของ Customer)


// ==========================================================
// --- 2. ADMIN SECTION (ต้อง Login และมี Role 'admin') ---
// ==========================================================

// (Auth สำหรับ Admin)
$routes->get('admin/login', 'admin\AdminAuthController::login');
$routes->post('admin/login', 'admin\AdminAuthController::processLogin');
$routes->get('admin/logout', 'admin\AdminAuthController::logout');

$routes->group('admin', ['filter' => ['auth', 'admin']], static function ($routes) {
    
    $routes->get('dashboard', 'admin\DashboardController::index');

    // Category CRUD
    $routes->get('categories', 'admin\CategoryController::index');
    $routes->get('categories/new', 'admin\CategoryController::new');
    $routes->post('categories/create', 'admin\CategoryController::create');
    $routes->get('categories/edit/(:num)', 'admin\CategoryController::edit/$1');
    $routes->post('categories/update/(:num)', 'admin\CategoryController::update/$1');
    $routes->get('categories/delete/(:num)', 'admin\CategoryController::delete/$1');

    // Stadium (Admin) CRUD
    $routes->get('stadiums', 'admin\StadiumController::index');
    $routes->get('stadiums/create', 'admin\StadiumController::create');
    $routes->post('stadiums', 'admin\StadiumController::store');
    $routes->get('stadiums/edit/(:num)', 'admin\StadiumController::edit/$1');
    $routes->post('stadiums/update/(:num)', 'admin\StadiumController::update/$1');
    $routes->get('stadiums/delete/(:num)', 'admin\StadiumController::delete/$1');
    
    // User Management (3 Roles)
    $routes->get('users', 'admin\UserController::index');
    $routes->get('users/create', 'admin\UserController::create');
    $routes->post('users', 'admin\UserController::store');
    $routes->get('users/edit/(:segment)/(:num)', 'admin\UserController::edit/$1/$2');
    $routes->post('users/update/(:segment)/(:num)', 'admin\UserController::update/$1/$2');
    $routes->get('users/delete/(:segment)/(:num)', 'admin\UserController::delete/$1/$2');
    
    // ⬇️ 1. (เพิ่ม) นี่คือ "หน้าอนุมัติ Vendor" (ที่ขาดไป) ⬇️
    $routes->get('vendors/pending', 'admin\UserController::pendingList');
    $routes->get('vendors/approve/(:num)', 'admin\UserController::approveVendor/$1');
    $routes->get('vendors/reject/(:num)', 'admin\UserController::rejectVendor/$1');

    // ⬇️ 2. (เพิ่ม) นี่คือ "หน้าจัดการ Booking" (ที่ขาดไป) ⬇️
    $routes->get('bookings/new', 'admin\BookingController::indexNew');
    $routes->get('bookings/pending', 'admin\BookingController::indexPending');
    $routes->get('bookings/cancel/(:num)', 'admin\BookingController::cancel/$1');
});


// ==========================================================
// --- 3. VENDOR SECTION (ต้อง Login และมี Role 'vendor') ---
// (ส่วนนี้เพื่อนคุณจะมาทำต่อ... เราสร้างเผื่อไว้)
// ==========================================================
// $routes->get('vendor/login', 'vendor\VendorAuthController::login');
// ... (ฯลฯ Routes ของ Vendor) ...


// ==========================================================
// --- 4. CUSTOMER SECTION (ต้อง Login) ---
// (นี่คือส่วนของ (ข้อ 1) และ (ข้อ 2) ที่เราสร้าง)
// ==========================================================

$routes->group('customer', ['filter' => ['auth', 'customer']], static function ($routes) {

    // (ส่วนระบบจอง - ข้อ 1)
    $routes->get('dashboard', 'CustomerController::index');
    $routes->get('booking/stadium/(:num)', 'BookingController::viewStadium/$1');
    $routes->post('booking/process', 'BookingController::processBooking');

    // (ส่วนระบบจ่ายเงิน - ข้อ 2)
    $routes->get('payment/checkout/(:num)', 'BookingController::checkout/$1');
    $routes->post('payment/process', 'BookingController::processPayment');
    $routes->get('payment/success/(:num)', 'BookingController::paymentSuccess/$1');

});