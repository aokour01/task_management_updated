<?php

use App\Core\Container;
use App\Core\Database;
use App\Core\Router;

require __DIR__ . '/../vendor/autoload.php';

// Tiny .env loader -- good enough for local dev; swap for vlucas/phpdotenv
// if the project grows past this.
$envFile = __DIR__ . '/../.env';

if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$name, $value] = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        $_ENV[$name] = $value;
        putenv("{$name}={$value}");
    }
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$container = new Container();

// One shared PDO connection for the whole request, handed out to anything
// that asks for it (repositories, in this case).
$container->bind(PDO::class, fn () => Database::connect());

$router = new Router($container);
require __DIR__ . '/../routes/web.php';

return $router;
