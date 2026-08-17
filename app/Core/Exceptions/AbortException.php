<?php

namespace App\Core\Exceptions;

/**
 * Thrown by Controller::abort() (and by the router on a 404) instead of
 * calling http_response_code()+die directly, for the same testability
 * reason as RedirectException.
 */
class AbortException extends \RuntimeException
{
    public function __construct(public readonly int $status, string $message)
    {
        parent::__construct($message);
    }
}
