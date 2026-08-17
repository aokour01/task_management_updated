<?php

use App\Controllers\TaskController;
use App\Core\Exceptions\RedirectException;

beforeEach(function () {
    $this->userId = $this->registerUser();
    $_SESSION['user_id'] = $this->userId;
    $_SESSION['user_name'] = 'Test User';
});

test('a user can create a task', function () {
    $_POST = ['title' => 'Buy milk', 'description' => 'Whole milk, 2%'];

    $controller = $this->container->make(TaskController::class);

    $redirectedTo = null;

    try {
        $controller->create();
    } catch (RedirectException $e) {
        $redirectedTo = $e->location;
    }

    expect($redirectedTo)->toBe('/tasks');

    $stmt = $this->pdo->prepare('SELECT * FROM tasks WHERE user_id = ?');
    $stmt->execute([$this->userId]);
    $task = $stmt->fetch();

    expect($task['title'])->toBe('Buy milk');
    expect($task['status'])->toBe('pending');
});

test('task creation fails when the title is too short', function () {
    $_POST = ['title' => 'ab', 'description' => ''];

    $controller = $this->container->make(TaskController::class);

    ob_start();
    $controller->create();
    $html = ob_get_clean();

    expect($html)->toContain('Title must be at least 3 characters.');

    $count = (int) $this->pdo->query('SELECT COUNT(*) FROM tasks')->fetchColumn();
    expect($count)->toBe(0);
});

test('a user can see their own tasks in the list', function () {
    $this->pdo->prepare('INSERT INTO tasks (user_id, title, status) VALUES (?, ?, ?)')
        ->execute([$this->userId, 'Walk the dog', 'pending']);

    $controller = $this->container->make(TaskController::class);

    ob_start();
    $controller->index();
    $html = ob_get_clean();

    expect($html)->toContain('Walk the dog');
});

test('the task list can be filtered by status', function () {
    $this->pdo->prepare('INSERT INTO tasks (user_id, title, status) VALUES (?, ?, ?)')
        ->execute([$this->userId, 'Walk the dog', 'pending']);
    $this->pdo->prepare('INSERT INTO tasks (user_id, title, status) VALUES (?, ?, ?)')
        ->execute([$this->userId, 'File taxes', 'completed']);

    $_GET = ['status' => 'completed'];

    $controller = $this->container->make(TaskController::class);

    ob_start();
    $controller->index();
    $html = ob_get_clean();

    expect($html)->toContain('File taxes');
    expect($html)->not->toContain('Walk the dog');
});

test('a user can update their own task', function () {
    $this->pdo->prepare('INSERT INTO tasks (user_id, title, status) VALUES (?, ?, ?)')
        ->execute([$this->userId, 'Walk the dog', 'pending']);
    $taskId = (int) $this->pdo->lastInsertId();

    $_POST = ['title' => 'Walk the dog twice', 'description' => '', 'status' => 'completed'];

    $controller = $this->container->make(TaskController::class);

    $redirectedTo = null;

    try {
        $controller->update((string) $taskId);
    } catch (RedirectException $e) {
        $redirectedTo = $e->location;
    }

    expect($redirectedTo)->toBe("/tasks/{$taskId}");

    $stmt = $this->pdo->prepare('SELECT * FROM tasks WHERE id = ?');
    $stmt->execute([$taskId]);
    $task = $stmt->fetch();

    expect($task['title'])->toBe('Walk the dog twice');
    expect($task['status'])->toBe('completed');
});

test('a user can delete their own task', function () {
    $this->pdo->prepare('INSERT INTO tasks (user_id, title, status) VALUES (?, ?, ?)')
        ->execute([$this->userId, 'Old task', 'pending']);
    $taskId = (int) $this->pdo->lastInsertId();

    $controller = $this->container->make(TaskController::class);

    $redirectedTo = null;

    try {
        $controller->delete((string) $taskId);
    } catch (RedirectException $e) {
        $redirectedTo = $e->location;
    }

    expect($redirectedTo)->toBe('/tasks');

    $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM tasks WHERE id = ?');
    $stmt->execute([$taskId]);
    expect((int) $stmt->fetchColumn())->toBe(0);
});
