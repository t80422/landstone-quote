<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'HomeController::index');

$routes->group('customer', function ($routes) {
    $routes->get('/', 'CustomerController::index');
    $routes->get('create', 'CustomerController::create');
    $routes->get('edit/(:num)', 'CustomerController::edit/$1');
    $routes->post('save', 'CustomerController::save');
    $routes->get('delete/(:num)', 'CustomerController::delete/$1');
});

$routes->group('product', function ($routes) {
    $routes->get('/', 'ProductController::index');
    $routes->get('create', 'ProductController::create');
    $routes->get('edit/(:num)', 'ProductController::edit/$1');
    $routes->post('save', 'ProductController::save');
    $routes->get('delete/(:num)', 'ProductController::delete/$1');
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
