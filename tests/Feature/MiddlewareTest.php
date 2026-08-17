<?php

use App\Core\Exceptions\RedirectException;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

test('AuthMiddleware redirects guests to the login page', function () {
    try {
        (new AuthMiddleware())->handle();
        $this->fail('Expected a redirect for a guest.');
    } catch (RedirectException $e) {
        expect($e->location)->toBe('/login');
    }
});

test('AuthMiddleware lets a logged-in user through', function () {
    $_SESSION['user_id'] = 1;

    (new AuthMiddleware())->handle();

    // No exception means the request was allowed to continue.
    expect(true)->toBeTrue();
});

test('GuestMiddleware redirects logged-in users to the task list', function () {
    $_SESSION['user_id'] = 1;

    try {
        (new GuestMiddleware())->handle();
        $this->fail('Expected a redirect for a logged-in user.');
    } catch (RedirectException $e) {
        expect($e->location)->toBe('/tasks');
    }
});

test('GuestMiddleware lets a guest through', function () {
    (new GuestMiddleware())->handle();

    expect($_SESSION)->not->toHaveKey('user_id');
});
