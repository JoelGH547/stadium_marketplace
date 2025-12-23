<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// (Auth สำหรับ Admin)

$routes->get('/', static function () {
    return redirect()->to('/admin/login');
});
$routes->get('admin/login', 'admin\AdminAuthController::login');
$routes->post('admin/login', 'admin\AdminAuthController::processLogin');
$routes->get('admin/logout', 'admin\AdminAuthController::logout');

$routes->group('admin', ['filter' => ['auth', 'admin']], static function ($routes) {
    $routes->get('dashboard', 'Admin\DashboardController::index');

    // Category CRUD
    $routes->get('categories', 'Admin\CategoryController::index');
    $routes->get('categories/new', 'Admin\CategoryController::new');
    $routes->post('categories/create', 'Admin\CategoryController::create');
    $routes->get('categories/edit/(:num)', 'Admin\CategoryController::edit/$1');
    $routes->post('categories/update/(:num)', 'Admin\CategoryController::update/$1');
    $routes->get('categories/delete/(:num)', 'Admin\CategoryController::delete/$1');

    // Stadium (Admin) CRUD
    $routes->get('stadiums', 'Admin\StadiumController::index');
    $routes->get('stadiums/create', 'Admin\StadiumController::create');
    $routes->post('stadiums', 'Admin\StadiumController::store');
    $routes->get('stadiums/edit/(:num)', 'Admin\StadiumController::edit/$1');
    $routes->post('stadiums/update/(:num)', 'Admin\StadiumController::update/$1');
    $routes->get('stadiums/delete/(:num)', 'Admin\StadiumController::delete/$1');

    // Booking Management
    $routes->get('bookings', 'Admin\BookingController::index');
    $routes->get('bookings/approve/(:num)', 'Admin\BookingController::approve/$1');
    $routes->get('bookings/cancel/(:num)', 'Admin\BookingController::cancel/$1');
    
    // User Management
    $routes->get('users/admins', 'Admin\UserController::admins');
    $routes->get('users/vendors', 'Admin\UserController::vendors');
    $routes->get('users/customers', 'Admin\UserController::customers');
    $routes->get('users/new_customers', 'Admin\UserController::newCustomers');

    // Vendor Approval
    $routes->get('vendors/pending', 'Admin\UserController::pendingList');
    $routes->get('vendors/approve/(:num)', 'Admin\UserController::approveVendor/$1');
    $routes->get('vendors/reject/(:num)', 'Admin\UserController::rejectVendor/$1');

    // CRUD Generic (Role specified as segment)
    $routes->get('users/create/(:segment)', 'Admin\UserController::create/$1');
    $routes->post('users/store/(:segment)', 'Admin\UserController::store/$1');
    $routes->get('users/edit/(:segment)/(:num)', 'Admin\UserController::edit/$1/$2');
    $routes->post('users/update/(:segment)/(:num)', 'Admin\UserController::update/$1/$2');
    $routes->get('users/delete/(:segment)/(:num)', 'Admin\UserController::delete/$1/$2');
});

//vendor

$routes->group('owner', ['namespace' => 'App\Controllers\Owner'], function($routes){
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