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
$routes->get('admin/login', 'Admin\AdminAuthController::login');
$routes->post('admin/login', 'Admin\AdminAuthController::processLogin');
$routes->get('admin/logout', 'Admin\AdminAuthController::logout');

$routes->group('admin', ['filter' => ['auth', 'admin']], static function ($routes) {
    
    // --- Dashboard ---
    $routes->get('dashboard', 'Admin\DashboardController::index');

    // --- Categories (ประเภทกีฬา) ---
    $routes->get('categories', 'Admin\CategoryController::index');
    $routes->get('categories/new', 'Admin\CategoryController::new');
    $routes->post('categories/create', 'Admin\CategoryController::create');
    $routes->get('categories/edit/(:num)', 'Admin\CategoryController::edit/$1');
    $routes->post('categories/update/(:num)', 'Admin\CategoryController::update/$1');
    $routes->get('categories/delete/(:num)', 'Admin\CategoryController::delete/$1');

    // ==========================================================
    // +++ [ระบบใหม่] Facility Types (หมวดหมู่สิ่งอำนวยความสะดวก) +++
    // ==========================================================
    $routes->get('facility-types', 'Admin\FacilityTypeController::index');
    $routes->post('facility-types/create', 'Admin\FacilityTypeController::create');
    $routes->get('facility-types/delete/(:num)', 'Admin\FacilityTypeController::delete/$1');
    // ==========================================================

    // --- Stadiums (สนามหลัก) ---
    $routes->get('stadiums', 'Admin\StadiumController::index');
    $routes->get('stadiums/create', 'Admin\StadiumController::create');
    $routes->post('stadiums', 'Admin\StadiumController::store'); // รับค่า Create
    $routes->get('stadiums/edit/(:num)', 'Admin\StadiumController::edit/$1');
    $routes->post('stadiums/update/(:num)', 'Admin\StadiumController::update/$1');
    $routes->get('stadiums/delete/(:num)', 'Admin\StadiumController::delete/$1');
    $routes->get('stadiums/view/(:num)', 'Admin\StadiumController::view/$1');

    // --- Stadium Fields (จัดการสนามย่อย & ราคา) ---
    $routes->get('stadiums/fields/(:num)', 'Admin\StadiumController::fields/$1');
    $routes->post('stadiums/fields/create', 'Admin\StadiumController::createField');
    $routes->post('stadiums/fields/update', 'Admin\StadiumController::updateField');
    $routes->get('stadiums/fields/delete/(:num)', 'Admin\StadiumController::deleteField/$1');

    // ==========================================================
    // +++ [ระบบใหม่] Vendor Items (จัดการสินค้า/บริการเสริม) +++
    // ==========================================================
    $routes->group('vendor-items', static function ($routes) {
        $routes->get('/', 'Admin\VendorItemController::index');
        $routes->post('store', 'Admin\VendorItemController::store');
        $routes->post('update', 'Admin\VendorItemController::update');
        $routes->get('delete/(:num)', 'Admin\VendorItemController::delete/$1');
    });
    // ==========================================================

    // --- User Management (จัดการผู้ใช้) ---
    $routes->group('users', static function ($routes) {
        
        // 1. หน้าแสดงรายการ (Read)
        $routes->get('admins', 'Admin\UserController::admins');
        $routes->get('vendors', 'Admin\UserController::vendors');
        $routes->get('customers', 'Admin\UserController::customers');
        $routes->get('new_customers', 'Admin\UserController::newCustomers');

        // 2. CRUD (Create, Edit, Delete)
        $routes->get('create/(:segment)', 'Admin\UserController::create/$1');
        $routes->post('store/(:segment)', 'Admin\UserController::store/$1');
        $routes->get('edit/(:segment)/(:num)', 'Admin\UserController::edit/$1/$2');
        $routes->post('update/(:segment)/(:num)', 'Admin\UserController::update/$1/$2');
        $routes->get('delete/(:segment)/(:num)', 'Admin\UserController::delete/$1/$2');
    });
    
    // --- Vendor Approval (อนุมัติผู้ขาย) ---
    $routes->get('vendors/pending', 'Admin\UserController::pendingList');
    $routes->get('vendors/approve/(:num)', 'Admin\UserController::approveVendor/$1');
    $routes->get('vendors/reject/(:num)', 'Admin\UserController::rejectVendor/$1');

    // --- Bookings (การจอง) ---
    $routes->get('bookings', 'Admin\BookingController::index'); 
    
    // จัดการสถานะจอง
    $routes->post('bookings/updateStatus', 'Admin\BookingController::updateStatus');
    $routes->get('bookings/approve/(:num)', 'Admin\BookingController::approve/$1');
    $routes->get('bookings/cancel/(:num)', 'Admin\BookingController::cancel/$1');
});


// ==========================================================
// --- 3. CUSTOMER SECTION (Frontend - ของเพื่อน) ---
// ==========================================================
$routes->group('customer', ['filter' => ['auth', 'customer']], static function ($routes) {
    $routes->get('dashboard', 'CustomerController::index');
    $routes->get('booking/stadium/(:num)', 'BookingController::viewStadium/$1');
    $routes->post('booking/process', 'BookingController::processBooking');
    $routes->get('payment/checkout/(:num)', 'BookingController::checkout/$1');
    $routes->post('payment/process', 'BookingController::processPayment');
    $routes->get('payment/success/(:num)', 'BookingController::paymentSuccess/$1');
});