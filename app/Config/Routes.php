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

$routes->get('customer/login',  'customer\CustomerAuthController::login');
$routes->post('customer/login', 'customer\CustomerAuthController::processLogin');
$routes->get('customer/register',  'customer\CustomerAuthController::register');
$routes->post('customer/register', 'customer\CustomerAuthController::processRegister');
$routes->get('customer/logout', 'customer\CustomerAuthController::logout');

$routes->get('sport', 'customer\HomeController::index');

$routes->group('sport', ['filter' => 'customer'], static function ($routes) {
    $routes->get('view', 'customer\HomeController::view');
    $routes->get('show/(:num)', 'customer\HomeController::show/$1');
});


// ==========================================================
// --- 2. ADMIN DASHBOARD AND LOGIN ---
// ==========================================================
$routes->get('admin/login', 'admin\AdminAuthController::login');
$routes->post('admin/login', 'admin\AdminAuthController::processLogin');
$routes->get('admin/logout', 'admin\AdminAuthController::logout');

$routes->group('admin', ['filter' => ['admin']], static function ($routes) {

    $routes->get('dashboard', 'admin\DashboardController::index');

    $routes->get('categories', 'admin\CategoryController::index');
    $routes->get('categories/new', 'admin\CategoryController::new');
    $routes->post('categories/create', 'admin\CategoryController::create');
    $routes->get('categories/edit/(:num)', 'admin\CategoryController::edit/$1');
    $routes->post('categories/update/(:num)', 'admin\CategoryController::update/$1');
    $routes->get('categories/delete/(:num)', 'admin\CategoryController::delete/$1');

    $routes->get('stadiums', 'admin\StadiumController::index');
    $routes->get('stadiums/create', 'admin\StadiumController::create');
    $routes->post('stadiums', 'admin\StadiumController::store');
    $routes->get('stadiums/edit/(:num)', 'admin\StadiumController::edit/$1');
    $routes->post('stadiums/update/(:num)', 'admin\StadiumController::update/$1');
    $routes->get('stadiums/delete/(:num)', 'admin\StadiumController::delete/$1');
    $routes->get('stadiums/view/(:num)', 'admin\StadiumController::view/$1');

    $routes->get('stadiums/fields/(:num)', 'admin\StadiumController::fields/$1');
    $routes->post('stadiums/fields/create', 'admin\StadiumController::createField');
    $routes->get('stadiums/fields/delete/(:num)', 'admin\StadiumController::deleteField/$1');
    $routes->post('stadiums/fields/update', 'admin\StadiumController::updateField');

    $routes->group('users', static function ($routes) {

        $routes->get('admins', 'admin\UserController::admins');
        $routes->get('vendors', 'admin\UserController::vendors');
        $routes->get('customers', 'admin\UserController::customers');

        $routes->get('new_customers', 'admin\UserController::newCustomers');


        $routes->get('create/(:segment)', 'admin\UserController::create/$1');
        $routes->post('store/(:segment)', 'admin\UserController::store/$1');

        $routes->get('edit/(:segment)/(:num)', 'admin\UserController::edit/$1/$2');
        $routes->post('update/(:segment)/(:num)', 'admin\UserController::update/$1/$2');

        $routes->get('delete/(:segment)/(:num)', 'admin\UserController::delete/$1/$2');
    });

    $routes->get('vendors/pending', 'admin\UserController::pendingList');
    $routes->get('vendors/approve/(:num)', 'admin\UserController::approveVendor/$1');
    $routes->get('vendors/reject/(:num)', 'admin\UserController::rejectVendor/$1');


    $routes->get('bookings', 'admin\BookingController::index');
    $routes->get('bookings/new', 'admin\BookingController::indexNew');
    $routes->get('bookings/pending', 'admin\BookingController::indexPending');
    $routes->get('bookings/approve/(:num)', 'admin\BookingController::approve/$1');
    $routes->get('bookings/cancel/(:num)', 'admin\BookingController::cancel/$1');
});


// ==========================================================
// --- 3. VENDOR ---
// ==========================================================

$routes->get('php-version', function () {
    return phpversion();
});
