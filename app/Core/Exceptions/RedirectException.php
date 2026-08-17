<?php

namespace App\Core\Exceptions;

/**
 * Thrown by Controller::redirect() and by middleware instead of calling
 * header()+exit directly. The front controller (public/index.php) catches
 * this and performs the real redirect; tests catch it too, so redirects can
 * be asserted without killing the PHP process.
 */
class RedirectException extends \RuntimeException
{
    public function __construct(public readonly string $location)
    {
        parent::__construct("Redirecting to {$location}");
    }
}
