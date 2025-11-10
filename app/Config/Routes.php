<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');


// --- Auth Routes (Login, Register) ---
$routes->get('/register', 'AuthController::register');
$routes->post('/register', 'AuthController::processRegister');
$routes->get('/login', 'AuthController::index');
$routes->post('/login', 'AuthController::processLogin');
$routes->get('/logout', 'AuthController::logout');


// --- User Dashboard (สำหรับทุกคนที่ Login แล้ว) ---
$routes->get('/dashboard', 'DashboardController::index', ['filter' => 'auth']);


// ==========================================================
// --- ADMIN SECTION (ต้อง Login และมี Role 'admin') ---
// ==========================================================
$routes->group('admin', ['filter' => ['auth', 'admin']], static function ($routes) {

    $routes->get('dashboard', 'DashboardController::index');

    // --- Category CRUD Routes ---
    $routes->get('categories', 'CategoryController::index');
    $routes->get('categories/new', 'CategoryController::new');
    $routes->post('categories/create', 'CategoryController::create');
    $routes->get('categories/edit/(:num)', 'CategoryController::edit/$1');
    $routes->post('categories/update/(:num)', 'CategoryController::update/$1');
    $routes->get('categories/delete/(:num)', 'CategoryController::delete/$1');


    // --- Stadium CRUD Routes (แก้ไขจาก Products) ---
    $routes->get('stadiums', 'StadiumController::index');
    $routes->get('stadiums/create', 'StadiumController::create');
    $routes->post('stadiums', 'StadiumController::store');
    $routes->get('stadiums/edit/(:num)', 'StadiumController::edit/$1');
    $routes->post('stadiums/update/(:num)', 'StadiumController::update/$1');
    $routes->get('stadiums/delete/(:num)', 'StadiumController::delete/$1');

    
    // --- Stock Management Routes (ลบส่วนนี้ทิ้ง) ---
    // (เราไม่ใช้ StockController แล้ว)


    // --- User Management Routes ---
    $routes->get('users', 'admin\UserController::index');
    $routes->get('users/create', 'admin\UserController::create');
    $routes->post('users', 'admin\UserController::store');
    $routes->get('users/edit/(:num)', 'admin\UserController::edit/$1');
    $routes->post('users/update/(:num)', 'admin\UserController::update/$1');
    $routes->get('users/delete/(:num)', 'admin\UserController::delete/$1');

});