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
    $routes->get('dashboard', 'DashboardController::index');

    // Category CRUD
    $routes->get('categories', 'CategoryController::index');
    $routes->get('categories/new', 'CategoryController::new');
    $routes->post('categories/create', 'CategoryController::create');
    $routes->get('categories/edit/(:num)', 'CategoryController::edit/$1');
    $routes->post('categories/update/(:num)', 'CategoryController::update/$1');
    $routes->get('categories/delete/(:num)', 'CategoryController::delete/$1');

    // Stadium (Admin) CRUD
    $routes->get('stadiums', 'StadiumController::index');
    $routes->get('stadiums/create', 'StadiumController::create');
    $routes->post('stadiums', 'StadiumController::store');
    $routes->get('stadiums/edit/(:num)', 'StadiumController::edit/$1');
    $routes->post('stadiums/update/(:num)', 'StadiumController::update/$1');
    $routes->get('stadiums/delete/(:num)', 'StadiumController::delete/$1');
    
    // User Management (3 Roles)
    $routes->get('users', 'admin\UserController::index');
    $routes->get('users/create', 'admin\UserController::create');
    $routes->post('users', 'admin\UserController::store');
    $routes->get('users/edit/(:segment)/(:num)', 'admin\UserController::edit/$1/$2');
    $routes->post('users/update/(:segment)/(:num)', 'admin\UserController::update/$1/$2');
    $routes->get('users/delete/(:segment)/(:num)', 'admin\UserController::delete/$1/$2');
});


// ==========================================================
// --- 3. VENDOR SECTION (ต้อง Login และมี Role 'vendor') ---
// ==========================================================

// (Auth สำหรับ Vendor)
$routes->get('vendor/login', 'vendor\VendorAuthController::login');
$routes->post('vendor/login', 'vendor\VendorAuthController::processLogin');
$routes->get('vendor/logout', 'vendor\VendorAuthController::logout');

$routes->group('vendor', ['filter' => ['auth', 'vendor']], static function ($routes) {
    
    $routes->get('dashboard', 'VendorController::index'); // (Dashboard ที่เราทำเสร็จแล้ว)

    // (CRUD สนาม... ที่เราทำเสร็จแล้ว)
    $routes->get('stadiums', 'vendor\StadiumController::index');
    $routes->get('stadiums/create', 'vendor\StadiumController::create');
    $routes->post('stadiums', 'vendor\StadiumController::store');
    $routes->get('stadiums/edit/(:num)', 'vendor\StadiumController::edit/$1');
    $routes->post('stadiums/update/(:num)', 'vendor\StadiumController::update/$1');
    $routes->get('stadiums/delete/(:num)', 'vendor\StadiumController::delete/$1');
    
    // --- ⬇️ 1. "เปิด" (Uncomment) บรรทัดนี้ ⬇️ ---
    $routes->get('bookings', 'vendor\BookingController::index'); // (อนาคต -> ปัจจุบัน)

});


// ==========================================================
// --- 4. CUSTOMER SECTION (ต้อง Login) ---
// ==========================================================

$routes->group('customer', ['filter' => ['auth', 'customer']], static function ($routes) {

    $routes->get('dashboard', 'CustomerController::index');
    
    // (อนาคต: $routes->get('bookings', 'customer\BookingController::index'); )

});