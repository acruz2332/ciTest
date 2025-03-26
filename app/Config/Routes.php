<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->options('(:any)', function() {
    return $this->response->setStatusCode(200);
});
$routes->resource('auth');

