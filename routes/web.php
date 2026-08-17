<?php

use App\Controllers\AuthController;
use App\Controllers\TaskController;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

/** @var \App\Core\Router $router */

$router->get('/register', [AuthController::class, 'showRegisterForm'], [GuestMiddleware::class]);
$router->post('/register', [AuthController::class, 'register'], [GuestMiddleware::class]);
$router->get('/login', [AuthController::class, 'showLoginForm'], [GuestMiddleware::class]);
$router->post('/login', [AuthController::class, 'login'], [GuestMiddleware::class]);
$router->post('/logout', [AuthController::class, 'logout'], [AuthMiddleware::class]);

$router->get('/tasks', [TaskController::class, 'index'], [AuthMiddleware::class]);
$router->get('/tasks/create', [TaskController::class, 'showCreateForm'], [AuthMiddleware::class]);
$router->post('/tasks', [TaskController::class, 'create'], [AuthMiddleware::class]);
$router->get('/tasks/{id}', [TaskController::class, 'show'], [AuthMiddleware::class]);
$router->get('/tasks/{id}/edit', [TaskController::class, 'showEditForm'], [AuthMiddleware::class]);
$router->patch('/tasks/{id}', [TaskController::class, 'update'], [AuthMiddleware::class]);
$router->delete('/tasks/{id}', [TaskController::class, 'delete'], [AuthMiddleware::class]);
