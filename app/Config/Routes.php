<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ==========================================================
//  PUBLIC CUSTOMER AND LOGIN REGISTER
// ==========================================================

// เข้าเว็บครั้งแรก → ไปหน้า admin login
$routes->get('/', static function () {
    return redirect()->to('/admin/login');
});

$routes->get('customer/login',  'customer\CustomerAuthController::login');
$routes->post('customer/login', 'customer\CustomerAuthController::processLogin');
$routes->post('customer/ajax_login', 'customer\CustomerAuthController::ajaxLogin');
$routes->get('customer/register',  'customer\CustomerAuthController::register');
$routes->post('customer/register', 'customer\CustomerAuthController::processRegister');
$routes->get('customer/logout', 'customer\CustomerAuthController::logout');

$routes->get('sport', 'customer\HomeController::index');
$routes->get('sport/show/(:num)', 'customer\StadiumController::show/$1');
$routes->get('sport/schedule/field/(:num)', 'customer\\BookingController::fieldSchedule/$1');
$routes->get('sport/fields/(:num)', 'customer\StadiumController::fields/$1');
$routes->get('sport/view', 'customer\\StadiumController::view');

$routes->group('sport', ['filter' => 'customer'], static function ($routes) {
    $routes->get('favorites', 'customer\FavoriteController::index');
    $routes->get('cart', 'customer\CartController::index');
    $routes->post('cart/add', 'customer\CartController::add');
    $routes->get('checkout', 'customer\CheckoutController::index');
    $routes->post('checkout/confirm', 'Customer\CheckoutController::confirm');
    $routes->get('profile', 'customer\ProfileController::show');
    $routes->get('profile/edit', 'customer\ProfileController::edit');
    $routes->post('profile/update', 'customer\ProfileController::update');
    $routes->get('booking_history', 'customer\BookingController::index');
    $routes->post('reviews/store', 'customer\ReviewController::store');
});

// Favorites (AJAX toggle without auth filter; return JSON need_login)
$routes->post('sport/favorites/toggle', 'customer\FavoriteController::toggle');


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

    // --- Stadium Fields (จัดการพื้นที่สนาม & ราคา) ---
    $routes->get('stadiums/fields/(:num)', 'Admin\StadiumController::fields/$1');
    $routes->post('stadiums/fields/create', 'Admin\StadiumController::createField');
    $routes->post('stadiums/fields/update', 'Admin\StadiumController::updateField');
    $routes->post('stadiums/fields/toggle-facility', 'Admin\StadiumController::toggleFieldFacility');
    $routes->get('stadiums/fields/delete/(:num)', 'Admin\StadiumController::deleteField/$1');

    // --- Vendor Products (สินค้าในพื้นที่สนาม) ---
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
    $routes->get('bookings/api', 'Admin\BookingController::api');

    // --- Reviews (จัดการรีวิว) ---
    $routes->get('reviews', 'Admin\ReviewController::index');
    $routes->get('reviews/toggle/(:num)', 'Admin\ReviewController::toggleStatus/$1');
    $routes->get('reviews/delete/(:num)', 'Admin\ReviewController::delete/$1');

    // จัดการสถานะจอง
    $routes->post('bookings/updateStatus', 'Admin\BookingController::updateStatus');
    $routes->get('bookings/approve/(:num)', 'Admin\BookingController::approve/$1');
    $routes->get('bookings/cancel/(:num)', 'Admin\BookingController::cancel/$1');
});



// ==========================================================
// --- 3. VENDOR ---
// ==========================================================

$routes->group('owner', ['namespace' => 'App\Controllers\Owner'], function ($routes) {
    //welcome
    $routes->get('welcome', 'Welcome::index');

    // login
    $routes->get('login', 'Login::index');
    $routes->post('login', 'Login::auth');

    // register
    $routes->get('register', 'Register::index');
    $routes->post('register', 'Register::store');

    // dashboard
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('logout', 'Login::logout');


    // ---------- CREATE FIELD ----------
    $routes->get('fields/step1', 'Field::step1');
    $routes->post('fields/step1', 'Field::step1_save');

    $routes->get('fields/step2', 'Field::step2');
    $routes->post('fields/step2', 'Field::step2_save');

    $routes->get('fields/step3', 'Field::step3');
    $routes->post('fields/step3', 'Field::step3_save');

    $routes->get('fields/step4', 'Field::step4');
    $routes->post('fields/step4', 'Field::step4_save');

    $routes->get('fields/confirm', 'Field::confirm');
    $routes->post('fields/store', 'Field::store');

    $routes->get('fields/edit/(:num)', 'Field::edit/$1');
    $routes->post('fields/update/(:num)', 'Field::update/$1');

    $routes->get('fields/delete/(:num)', 'Field::delete/$1');

    $routes->get('fields/view/(:num)', 'Field::view/$1');
    $routes->get('fields/subfields/(:num)', 'Subfield::index/$1');
    $routes->post('fields/subfields/(:num)/create', 'Subfield::create/$1');
    $routes->get('fields/subfields/(:num)/delete/(:num)', 'Subfield::delete/$1/$2');
    $routes->get('fields/subfields/toggle/(:num)/(:num)', 'Subfield::toggleStatus/$1/$2');

    // AJAX Subfield Details
    $routes->get('subfields/detail/(:num)', 'Subfield::getDetail/$1');
    $routes->post('subfields/facilities/update/(:num)', 'Subfield::updateFacilities/$1');
    $routes->get('subfields/edit/(:num)', 'Subfield::edit/$1');
    $routes->post('subfields/update/(:num)', 'Subfield::update/$1');
    $routes->post('subfields/create/(:num)', 'Subfield::create/$1'); // Added missing route

    $routes->get('items/add/(:num)', 'Items::add/$1');
    $routes->post('items/store/(:num)', 'Items::store/$1');
    $routes->get('items/detail/(:num)', 'Items::getDetail/$1');
    $routes->post('items/update/(:num)', 'Items::update/$1');
    $routes->get('items/delete/(:num)', 'Items::delete/$1');
    $routes->get('items/toggleStatus/(:num)', 'Items::toggleStatus/$1');



    // Bookings
    $routes->get('bookings', 'Bookings::index');
    $routes->post('bookings/approve/(:num)', 'Bookings::approve/$1');
    $routes->post('bookings/reject/(:num)', 'Bookings::reject/$1');
    $routes->get('bookings/detail/(:num)', 'Bookings::detail/$1');

    // Calendar
    $routes->get('calendar', 'Calendar::index');
    $routes->get('calendar/events', 'Calendar::getEvents');
});
