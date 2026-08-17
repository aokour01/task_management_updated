<?php

use App\Controllers\AuthController;
use App\Core\Exceptions\RedirectException;

test('a registered user can log in with correct credentials', function () {
    $this->registerUser(name: 'Jane Doe', email: 'jane@example.com', password: 'secret123');

    $_POST = ['email' => 'jane@example.com', 'password' => 'secret123'];

    $controller = $this->container->make(AuthController::class);

    $redirectedTo = null;

    try {
        $controller->login();
    } catch (RedirectException $e) {
        $redirectedTo = $e->location;
    }

    expect($redirectedTo)->toBe('/tasks');
    expect($_SESSION['user_name'])->toBe('Jane Doe');
});

test('login fails with an incorrect password', function () {
    $this->registerUser(email: 'jane@example.com', password: 'secret123');

    $_POST = ['email' => 'jane@example.com', 'password' => 'wrong-password'];

    $controller = $this->container->make(AuthController::class);

    ob_start();
    $controller->login();
    $html = ob_get_clean();

    expect($html)->toContain('Invalid email or password.');
    expect($_SESSION)->not->toHaveKey('user_id');
});

test('login fails for an email that is not registered', function () {
    $_POST = ['email' => 'nobody@example.com', 'password' => 'secret123'];

    $controller = $this->container->make(AuthController::class);

    ob_start();
    $controller->login();
    $html = ob_get_clean();

    expect($html)->toContain('Invalid email or password.');
});

test('login fails when a field is left blank', function () {
    $_POST = ['email' => '', 'password' => ''];

    $controller = $this->container->make(AuthController::class);

    ob_start();
    $controller->login();
    $html = ob_get_clean();

    expect($html)->toContain('Email is required.');
    expect($html)->toContain('Password is required.');
});
