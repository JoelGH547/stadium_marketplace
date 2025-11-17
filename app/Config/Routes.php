<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ==========================================================
// --- 1. PUBLIC ROUTES (หน้าหลัก, Customer Auth) ---
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
// --- 2. ADMIN SECTION (ต้อง Login และมี Role 'admin') ---
// ==========================================================
$routes->get('admin/login', 'admin\AdminAuthController::login');
$routes->post('admin/login', 'admin\AdminAuthController::processLogin');
$routes->get('admin/logout', 'admin\AdminAuthController::logout');

$routes->group('admin', ['filter' => ['auth', 'admin']], static function ($routes) {
    
    $routes->get('dashboard', 'admin\DashboardController::index');

    // (CRUD Categories, Stadiums... อยู่เหมือนเดิม)
    $routes->get('categories', 'admin\CategoryController::index');
    // ... (ฯลฯ) ...
    $routes->get('stadiums', 'admin\StadiumController::index');
    // ... (ฯลฯ) ...


    // --- (ส่วน User Management) ---
    $routes->get('users', 'admin\UserController::index');
    $routes->get('users/create', 'admin\UserController::create');
    $routes->post('users', 'admin\UserController::store');
    $routes->get('users/edit/(:segment)/(:num)', 'admin\UserController::edit/$1/$2');
    $routes->post('users/update/(:segment)/(:num)', 'admin\UserController::update/$1/$2');
    $routes->get('users/delete/(:segment)/(:num)', 'admin\UserController::delete/$1/$2');

    // ⬇️ 1. (เพิ่ม) นี่คือ "หน้าอนุมัติ Vendor" (ข้อ 3.1) ⬇️
    
    // (ลิงก์จาก Dashboard: 'admin/vendors/pending')
    $routes->get('vendors/pending', 'admin\UserController::pendingList');
    
    // (ลิงก์สำหรับปุ่ม "Approve")
    $routes->get('vendors/approve/(:num)', 'admin\UserController::approveVendor/$1');
    
    // (ลิงก์สำหรับปุ่ม "Reject")
    $routes->get('vendors/reject/(:num)', 'admin\UserController::rejectVendor/$1');


    // --- (ส่วน Booking Management) ---
    // (Routes ของ Booking ที่เราเพิ่งทำ... อยู่เหมือนเดิม)
    $routes->get('bookings/new', 'admin\BookingController::indexNew');
    $routes->get('bookings/pending', 'admin\BookingController::indexPending');
    $routes->get('bookings/cancel/(:num)', 'admin\BookingController::cancel/$1');

});


// ==========================================================
// --- 3. VENDOR SECTION (ต้อง Login และมี Role 'vendor') ---
// ==========================================================
$routes->get('vendor/login', 'vendor\VendorAuthController::login');
// ... (ฯลฯ Routes ของ Vendor) ...
$routes->group('vendor', ['filter' => ['auth', 'vendor']], static function ($routes) {
    $routes->get('dashboard', 'vendor\DashboardController::index'); 
});


// ==========================================================
// --- 4. CUSTOMER SECTION (ต้อง Login) ---
// ==========================================================
$routes->group('customer', ['filter' => ['auth', 'customer']], static function ($routes) {
    // (Routes ทั้งหมดของ Customer อยู่ที่นี่... เหมือนเดิม)
    $routes->get('dashboard', 'CustomerController::index');
    $routes->get('booking/stadium/(:num)', 'BookingController::viewStadium/$1');
    $routes->post('booking/process', 'BookingController::processBooking');
    $routes->get('payment/checkout/(:num)', 'BookingController::checkout/$1');
    $routes->post('payment/process', 'BookingController::processPayment');
    $routes->get('payment/success/(:num)', 'BookingController::paymentSuccess/$1');
});