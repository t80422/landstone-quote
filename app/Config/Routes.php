<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'HomeController::index');
$routes->get('login', 'AuthController::showLogin');
$routes->post('login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');
$routes->group('profile', function ($routes) {
    $routes->get('/', 'ProfileController::edit');
    $routes->post('update', 'ProfileController::update');
});
$routes->group('user', ['filter' => 'admin'], function ($routes) {
    $routes->get('/', 'UserController::index');
    $routes->get('create', 'UserController::create');
    $routes->get('edit/(:num)', 'UserController::edit/$1');
    $routes->post('save', 'UserController::save');
    $routes->get('delete/(:num)', 'UserController::delete/$1');
});

$routes->group('customer', function ($routes) {
    $routes->get('/', 'CustomerController::index');
    $routes->get('create', 'CustomerController::create');
    $routes->get('edit/(:num)', 'CustomerController::edit/$1');
    $routes->post('save', 'CustomerController::save');
    $routes->get('delete/(:num)', 'CustomerController::delete/$1');
    $routes->get('delivery-addresses/(:num)', 'CustomerController::getDeliveryAddresses/$1');
});

$routes->group('product', function ($routes) {
    $routes->get('/', 'ProductController::index');
    $routes->get('create', 'ProductController::create');
    $routes->get('edit/(:num)', 'ProductController::edit/$1');
    $routes->post('save', 'ProductController::save');
    $routes->get('delete/(:num)', 'ProductController::delete/$1');
});

// 產品分類管理
$routes->group('product-category', function ($routes) {
    $routes->get('/', 'ProductCategoryController::index');
    $routes->get('create', 'ProductCategoryController::create');
    $routes->get('edit/(:num)', 'ProductCategoryController::edit/$1');
    $routes->post('save', 'ProductCategoryController::save');
    $routes->get('delete/(:num)', 'ProductCategoryController::delete/$1');
});

$routes->group('quote', function ($routes) {
    $routes->get('/', 'QuoteController::index');
    $routes->get('create', 'QuoteController::create');
    $routes->get('edit/(:num)', 'QuoteController::edit/$1');
    $routes->post('save', 'QuoteController::save');
    $routes->get('delete/(:num)', 'QuoteController::delete/$1');
    $routes->get('get-product/(:num)', 'QuoteController::getProduct/$1');
});

$routes->group('order', function ($routes) {
    $routes->get('/', 'OrderController::index');
    $routes->get('create', 'OrderController::create');
    $routes->get('edit/(:num)', 'OrderController::edit/$1');
    $routes->post('save', 'OrderController::save');
    $routes->get('delete/(:num)', 'OrderController::delete/$1');
    $routes->get('create-from-quote/(:num)', 'OrderController::createFromQuote/$1');
});

$routes->group('shipment', function ($routes) {
    $routes->get('/', 'ShipmentController::index');
    $routes->get('create/(:num)', 'ShipmentController::create/$1');
    $routes->get('edit/(:num)', 'ShipmentController::edit/$1');
    $routes->post('save', 'ShipmentController::save');
    $routes->get('delete/(:num)', 'ShipmentController::delete/$1');
});

$routes->group('payment-method', function ($routes) {
    $routes->get('/', 'PaymentMethodController::index');
    $routes->get('create', 'PaymentMethodController::create');
    $routes->get('edit/(:num)', 'PaymentMethodController::edit/$1');
    $routes->post('store', 'PaymentMethodController::store');
    $routes->post('update/(:num)', 'PaymentMethodController::update/$1');
    $routes->get('delete/(:num)', 'PaymentMethodController::delete/$1');
});
