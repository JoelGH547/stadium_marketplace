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

});