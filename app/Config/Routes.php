<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// 1. ⬇️ --- Redirect หน้าแรกไป /login (สำหรับ Customer) --- ⬇️
$routes->get('/', static function () {
    return redirect()->to('/login');
});


// 2. ⬇️ --- AUTH (CUSTOMER) ROUTES (สาธารณะ) --- ⬇️
// (Controller: AuthController.php)
$routes->get('login', 'AuthController::index');
$routes->post('login', 'AuthController::processLogin');
$routes->get('register', 'AuthController::register');
$routes->post('register', 'AuthController::processRegister');
$routes->get('logout', 'AuthController::logout'); // Logout สาธารณะ (สำหรับ Customer)


// 3. ⬇️ --- ADMIN LOGIN ROUTES (หน้าล็อคอินแอดมิน) --- ⬇️
// (Controller: admin/AdminAuthController.php)
$routes->get('admin/login', 'admin\AdminAuthController::index');
$routes->post('admin/login', 'admin\AdminAuthController::processLogin');
$routes->get('admin/logout', 'admin\AdminAuthController::logout');


// 4. ⬇️ --- VENDOR LOGIN ROUTES (หน้าล็อคอินเจ้าของสนาม) --- ⬇️
// (Controller: vendor/VendorAuthController.php)
$routes->get('vendor/login', 'vendor\VendorAuthController::index');
$routes->post('vendor/login', 'vendor\VendorAuthController::processLogin');
$routes->get('vendor/logout', 'vendor\VendorAuthController::logout');


// 5. ⬇️ --- ADMIN SECTION (หน้า Dashboard ของแอดมิน) --- ⬇️
// (Filter: 'auth' และ 'admin')
$routes->group('admin', ['filter' => ['auth', 'admin']], static function ($routes) {

    $routes->get('dashboard', 'DashboardController::index');

    // --- Category CRUD Routes ---
    $routes->get('categories', 'CategoryController::index');
    $routes->get('categories/new', 'CategoryController::new');
    $routes->post('categories/create', 'CategoryController::create');
    $routes->get('categories/edit/(:num)', 'CategoryController::edit/$1');
    $routes->post('categories/update/(:num)', 'CategoryController::update/$1');
    $routes->get('categories/delete/(:num)', 'CategoryController::delete/$1');

    // --- Stadium CRUD Routes ---
    $routes->get('stadiums', 'StadiumController::index');
    $routes->get('stadiums/create', 'StadiumController::create');
    $routes->post('stadiums', 'StadiumController::store');
    $routes->get('stadiums/edit/(:num)', 'StadiumController::edit/$1');
    $routes->post('stadiums/update/(:num)', 'StadiumController::update/$1');
    $routes->get('stadiums/delete/(:num)', 'StadiumController::delete/$1');
    
    // 6. ⬇️ --- USER MANAGEMENT ROUTES (แก้ไขใหม่!) --- ⬇️
    // (Controller: admin/UserController.php)
    $routes->get('users', 'admin\UserController::index');
    $routes->get('users/create', 'admin\UserController::create');
    $routes->post('users', 'admin\UserController::store');
    
    // (แก้ไข: รับ :segment (คือ role) และ :num (คือ id))
    $routes->get('users/edit/(:segment)/(:num)', 'admin\UserController::edit/$1/$2');
    $routes->post('users/update/(:segment)/(:num)', 'admin\UserController::update/$1/$2');
    $routes->get('users/delete/(:segment)/(:num)', 'admin\UserController::delete/$1/$2');
});


// 7. ⬇️ --- VENDOR SECTION (หน้า Dashboard ของเจ้าของสนาม) --- ⬇️
// (Filter: 'auth' และ 'vendor')
$routes->group('vendor', ['filter' => ['auth', 'vendor']], static function ($routes) {
    $routes->get('dashboard', 'vendor\VendorController::index');
    // (อนาคต: เพิ่ม 'vendor/stadiums' ฯลฯ ที่นี่)
});


// 8. ⬇️ --- CUSTOMER SECTION (หน้า Dashboard ของลูกค้า) --- ⬇️
// (Filter: 'auth' และ 'customer')
$routes->group('customer', ['filter' => ['auth', 'customer']], static function ($routes) {
    $routes->get('dashboard', 'CustomerController::index');
    // (อนาคต: เพิ่ม 'customer/bookings' ฯลฯ ที่นี่)
});