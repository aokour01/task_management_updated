<?php

namespace App\Middleware;

interface Middleware
{
    /**
     * Let the request continue by returning normally. To block it, throw
     * App\Core\Exceptions\RedirectException (or AbortException) instead of
     * touching header()/exit directly -- that keeps middleware testable.
     */
    public function handle(): void;
}
