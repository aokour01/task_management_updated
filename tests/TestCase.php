<?php

namespace Tests;

use App\Core\Container;
use PDO;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected Container $container;
    protected PDO $pdo;

    protected function setUp(): void
    {
        parent::setUp();

        // Fresh superglobals per test, so nothing leaks between tests.
        $_SESSION = [];
        $_POST = [];
        $_GET = [];

        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec((string) file_get_contents(__DIR__ . '/../database/schema.sqlite.sql'));

        $this->container = new Container();
        $this->container->bind(PDO::class, fn () => $this->pdo);
    }

    /**
     * Inserts a user directly (bypassing AuthController::register) and
     * returns their id, so CRUD/authorization tests can set up an
     * "already logged in" user without re-testing registration each time.
     */
    protected function registerUser(
        string $name = 'Test User',
        string $email = 'test@example.com',
        string $password = 'secret123',
    ): int {
        $stmt = $this->pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);

        return (int) $this->pdo->lastInsertId();
    }
}
