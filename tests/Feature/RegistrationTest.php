<?php

use App\Controllers\AuthController;
use App\Core\Exceptions\RedirectException;

test('a visitor can register with valid details', function () {
    $_POST = [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'secret123',
        'password_confirm' => 'secret123',
    ];

    $controller = $this->container->make(AuthController::class);

    $redirectedTo = null;

    try {
        $controller->register();
    } catch (RedirectException $e) {
        $redirectedTo = $e->location;
    }

    expect($redirectedTo)->toBe('/tasks');
    expect($_SESSION['user_id'] ?? null)->not->toBeNull();
    expect($_SESSION['user_name'])->toBe('Jane Doe');

    $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute(['jane@example.com']);
    $user = $stmt->fetch();

    expect($user)->not->toBeFalse();
    expect(password_verify('secret123', $user['password']))->toBeTrue();
});

test('registration fails when the passwords do not match', function () {
    $_POST = [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'secret123',
        'password_confirm' => 'something-else',
    ];

    $controller = $this->container->make(AuthController::class);

    ob_start();
    $controller->register();
    $html = ob_get_clean();

    expect($html)->toContain('Password confirmation does not match.');
    expect($_SESSION)->not->toHaveKey('user_id');

    $count = (int) $this->pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    expect($count)->toBe(0);
});

test('registration fails when the password is too short', function () {
    $_POST = [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => '123',
        'password_confirm' => '123',
    ];

    $controller = $this->container->make(AuthController::class);

    ob_start();
    $controller->register();
    $html = ob_get_clean();

    expect($html)->toContain('Password must be at least 6 characters.');
});

test('registration fails when the email is already registered', function () {
    $this->registerUser(email: 'taken@example.com');

    $_POST = [
        'name' => 'Someone Else',
        'email' => 'taken@example.com',
        'password' => 'secret123',
        'password_confirm' => 'secret123',
    ];

    $controller = $this->container->make(AuthController::class);

    ob_start();
    $controller->register();
    $html = ob_get_clean();

    expect($html)->toContain('That email is already registered.');
});
