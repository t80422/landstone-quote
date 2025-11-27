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