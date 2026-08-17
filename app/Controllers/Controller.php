<?php

namespace App\Controllers;

use App\Core\Exceptions\AbortException;
use App\Core\Exceptions\RedirectException;

class Controller
{
    protected function render(string $view, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../../views/partials/header.php';
        require __DIR__ . '/../../views/' . $view . '.php';
        require __DIR__ . '/../../views/partials/footer.php';
    }

    /**
     * Throws instead of calling header()+exit directly, so both
     * public/index.php and Pest tests can catch it -- see
     * App\Core\Exceptions\RedirectException.
     */
    protected function redirect(string $path): never
    {
        throw new RedirectException($path);
    }

    /**
     * Throws instead of calling http_response_code()+die directly, for the
     * same reason as redirect() -- see App\Core\Exceptions\AbortException.
     */
    protected function abort(int $code, string $message): never
    {
        throw new AbortException($code, $message);
    }
}
