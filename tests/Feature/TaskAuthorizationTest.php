<?php

use App\Controllers\TaskController;
use App\Core\Exceptions\AbortException;

test('a user cannot view another user\'s task', function () {
    $ownerId = $this->registerUser(email: 'owner@example.com');
    $intruderId = $this->registerUser(email: 'intruder@example.com');

    $this->pdo->prepare('INSERT INTO tasks (user_id, title, status) VALUES (?, ?, ?)')
        ->execute([$ownerId, 'Secret task', 'pending']);
    $taskId = (int) $this->pdo->lastInsertId();

    $_SESSION['user_id'] = $intruderId;

    $controller = $this->container->make(TaskController::class);

    try {
        $controller->show((string) $taskId);
        $this->fail('Expected an AbortException for cross-user access.');
    } catch (AbortException $e) {
        expect($e->status)->toBe(403);
    }
});

test('a user cannot edit another user\'s task', function () {
    $ownerId = $this->registerUser(email: 'owner@example.com');
    $intruderId = $this->registerUser(email: 'intruder@example.com');

    $this->pdo->prepare('INSERT INTO tasks (user_id, title, status) VALUES (?, ?, ?)')
        ->execute([$ownerId, 'Secret task', 'pending']);
    $taskId = (int) $this->pdo->lastInsertId();

    $_SESSION['user_id'] = $intruderId;
    $_POST = ['title' => 'Hijacked title', 'description' => '', 'status' => 'completed'];

    $controller = $this->container->make(TaskController::class);

    try {
        $controller->update((string) $taskId);
        $this->fail('Expected an AbortException for cross-user access.');
    } catch (AbortException $e) {
        expect($e->status)->toBe(403);
    }

    $stmt = $this->pdo->prepare('SELECT title FROM tasks WHERE id = ?');
    $stmt->execute([$taskId]);
    expect($stmt->fetchColumn())->toBe('Secret task');
});

test('a user cannot delete another user\'s task', function () {
    $ownerId = $this->registerUser(email: 'owner@example.com');
    $intruderId = $this->registerUser(email: 'intruder@example.com');

    $this->pdo->prepare('INSERT INTO tasks (user_id, title, status) VALUES (?, ?, ?)')
        ->execute([$ownerId, 'Secret task', 'pending']);
    $taskId = (int) $this->pdo->lastInsertId();

    $_SESSION['user_id'] = $intruderId;

    $controller = $this->container->make(TaskController::class);

    try {
        $controller->delete((string) $taskId);
        $this->fail('Expected an AbortException for cross-user access.');
    } catch (AbortException $e) {
        expect($e->status)->toBe(403);
    }

    $count = (int) $this->pdo
        ->query("SELECT COUNT(*) FROM tasks WHERE id = {$taskId}")
        ->fetchColumn();
    expect($count)->toBe(1);
});

test('requesting a task that does not exist returns a 404', function () {
    $_SESSION['user_id'] = $this->registerUser();

    $controller = $this->container->make(TaskController::class);

    try {
        $controller->show('999999');
        $this->fail('Expected an AbortException for a missing task.');
    } catch (AbortException $e) {
        expect($e->status)->toBe(404);
    }
});

test('a non-numeric task id returns a 404 rather than a database error', function () {
    $_SESSION['user_id'] = $this->registerUser();

    $controller = $this->container->make(TaskController::class);

    try {
        $controller->show('not-a-number');
        $this->fail('Expected an AbortException for an invalid id.');
    } catch (AbortException $e) {
        expect($e->status)->toBe(404);
    }
});
