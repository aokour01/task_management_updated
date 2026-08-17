<?php

namespace App\Middleware;

use App\Core\Exceptions\RedirectException;

class GuestMiddleware implements Middleware
{
    public function handle(): void
    {
        if (isset($_SESSION['user_id'])) {
            throw new RedirectException('/tasks');
        }
    }
}
