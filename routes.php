<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Core\Router;

$router = new Router();

$authController = new AuthController();
$dashboardController = new DashboardController();

// Definicia rout.
$router->get('/', static fn () => redirect('/login'));
$router->get('/login', [$authController, 'showLogin']);
$router->post('/login', [$authController, 'login']);
$router->get('/register', [$authController, 'showRegister']);
$router->get('/dashboard', [$dashboardController, 'index']);


return $router;
