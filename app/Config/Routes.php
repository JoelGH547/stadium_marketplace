<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ==========================================================
//  PUBLIC CUSTOMER AND LOGIN REGISTER
// ==========================================================

// เข้าเว็บครั้งแรก → ไปหน้า home (ตอนนี้ใช้ route sport เดิมของคุณ)
$routes->get('/', static function () {
    return redirect()->to('/sport');
});

$routes->get('customer/login',  'Customer\CustomerAuthController::login');
$routes->post('customer/login', 'Customer\CustomerAuthController::processLogin');
$routes->post('customer/ajax_login', 'Customer\CustomerAuthController::ajaxLogin');
$routes->get('customer/register',  'Customer\CustomerAuthController::register');
$routes->post('customer/register', 'Customer\CustomerAuthController::processRegister');
$routes->get('customer/logout', 'Customer\CustomerAuthController::logout');

$routes->get('sport', 'Customer\HomeController::index');
$routes->get('sport/show/(:num)', 'Customer\StadiumController::show/$1');
$routes->get('sport/fields/(:num)', 'Customer\StadiumController::fields/$1');

$routes->group('sport', ['filter' => 'customer'], static function ($routes) {
    $routes->get('view', 'customer\StadiumController::view');
    $routes->get('cart', 'customer\CartController::index');
    $routes->post('cart/add', 'customer\CartController::add');
    $routes->get('checkout', 'customer\CheckoutController::index');
    $routes->post('checkout/confirm', 'Customer\CheckoutController::confirm');
    $routes->get('profile', 'customer\ProfileController::show');
    $routes->get('profile/edit', 'customer\ProfileController::edit');
    $routes->post('profile/update', 'customer\ProfileController::update');
    $routes->get('booking_history', 'customer\BookingController::index');
});


// ==========================================================
// --- 2. ADMIN DASHBOARD AND LOGIN ---
// ==========================================================
$routes->get('admin/login', 'Admin\AdminAuthController::login');
$routes->post('admin/login', 'Admin\AdminAuthController::processLogin');
$routes->get('admin/logout', 'Admin\AdminAuthController::logout');

$routes->group('admin', ['filter' => ['admin']], static function ($routes) {
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
    $routes->post('stadiums/fields/toggle-facility', 'Admin\StadiumController::toggleFieldFacility');
    $routes->get('stadiums/fields/delete/(:num)', 'Admin\StadiumController::deleteField/$1');

    // --- Vendor Products (สินค้าในสนามย่อย) ---
    $routes->post('stadiums/fields/product/save', 'Admin\StadiumController::saveProduct');
    $routes->get('stadiums/fields/product/delete/(:num)', 'Admin\StadiumController::deleteProduct/$1');

    // ==========================================================
    // +++ [ระบบใหม่] Vendor Items (จัดการสินค้า/บริการเสริม) +++
    // ==========================================================
    $routes->group('vendor-items', static function ($routes) {
        $routes->get('/', 'Admin\VendorItemsController::index');
        $routes->post('store', 'Admin\VendorItemsController::store');
        $routes->post('update', 'Admin\VendorItemsController::update');
        $routes->get('delete/(:num)', 'Admin\VendorItemsController::delete/$1');
        $routes->post('quick-create', 'Admin\VendorItemsController::quickCreate');
    });


    // URL ที่ถูกต้องจะเป็น: /admin/get-stadium-facility-types/1
    $routes->get('get-stadium-facility-types/(:num)', 'Admin\VendorItemController::getStadiumFacilityTypes/$1');

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
// --- 3. VENDOR ---
// ==========================================================

$routes->get('php-version', function () {
    return phpversion();
});
