<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use Bramus\Router\Router;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Define base path
define('BASE_PATH', '/php-sneakers-store/public');

// Initialize Router
$router = new Router();

// Remove base path from URLs for routing
$router->setBasePath(BASE_PATH);

// Define routes
$router->get('/', 'App\Controllers\HomeController@index');

// Product routes
$router->get('/products', 'App\Controllers\ProductController@index');
$router->get('/product/{id}', 'App\Controllers\ProductController@show');

// Cart routes
$router->get('/cart', 'App\Controllers\CartController@index');
$router->post('/cart/add', 'App\Controllers\CartController@add');
$router->post('/cart/update', 'App\Controllers\CartController@update');
$router->post('/cart/remove', 'App\Controllers\CartController@remove');

// Auth routes
$router->get('/login', 'App\Controllers\AuthController@loginForm');
$router->post('/login', 'App\Controllers\AuthController@login');
$router->get('/register', 'App\Controllers\AuthController@registerForm');
$router->post('/register', 'App\Controllers\AuthController@register');
$router->get('/logout', 'App\Controllers\AuthController@logout');

// Profile routes
$router->get('/profile', 'App\Controllers\ProfileController@index');
$router->post('/profile/update', 'App\Controllers\ProfileController@update');

// Checkout routes
$router->get('/checkout', 'App\Controllers\CheckoutController@index');
$router->post('/checkout/process', 'App\Controllers\CheckoutController@process');

// Admin routes
$router->get('/admin', 'App\Controllers\AdminController@index');

// Admin Product routes
$router->get('/admin/products', 'App\Controllers\Admin\ProductController@index');
$router->post('/admin/products/create', 'App\Controllers\Admin\ProductController@create');
$router->post('/admin/products/update', 'App\Controllers\Admin\ProductController@update');
$router->post('/admin/products/delete', 'App\Controllers\Admin\ProductController@delete');

// Admin Order routes
$router->get('/admin/orders', 'App\Controllers\AdminController@orders');
$router->post('/admin/update-order-status', 'App\Controllers\AdminController@updateOrderStatus');

// Admin User routes
$router->get('/admin/users', 'App\Controllers\AdminController@users');
$router->post('/admin/toggle-admin', 'App\Controllers\AdminController@toggleAdmin');

// Run the router
$router->run(); 