<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->options('(:any)', 'Auth::$1', ['filter' => 'cors']); 
// $routes->resource('auth');

$routes->resource('auth', ['filter' => 'cors']);

$routes->post('auth/login', 'Auth::login');
