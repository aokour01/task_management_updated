<?php

use App\Core\Exceptions\AbortException;
use App\Core\Exceptions\RedirectException;

/** @var \App\Core\Router $router */
$router = require __DIR__ . '/../bootstrap/app.php';

try {
    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (RedirectException $e) {
    header('Location: ' . $e->location);
} catch (AbortException $e) {
    http_response_code($e->status);
    echo $e->getMessage();
}
