<?php

namespace App\Core;

use App\Core\Exceptions\AbortException;

class Router
{
    private array $routes = [];

    public function __construct(private Container $container)
    {
    }

    public function get(string $path, array $action, array $middleware = []): void
    {
        $this->add('GET', $path, $action, $middleware);
    }

    public function post(string $path, array $action, array $middleware = []): void
    {
        $this->add('POST', $path, $action, $middleware);
    }

    public function patch(string $path, array $action, array $middleware = []): void
    {
        $this->add('PATCH', $path, $action, $middleware);
    }

    public function delete(string $path, array $action, array $middleware = []): void
    {
        $this->add('DELETE', $path, $action, $middleware);
    }

    private function add(string $method, string $path, array $action, array $middleware): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => trim($path, '/'),
            'action' => $action,
            'middleware' => $middleware,
        ];
    }

    /**
     * Matches and runs a route. Blocking middleware and unhandled
     * "stop the request" cases (auth redirects, 403s, 404s) are all
     * signalled by throwing, so both public/index.php and tests can catch
     * them without header()/exit ever running mid-test.
     */
    public function dispatch(string $method, string $uri): void
    {
        $path = trim(parse_url($uri, PHP_URL_PATH), '/');

        // Real HTML forms can only send GET/POST, so a hidden "_method"
        // field is used to spoof PATCH/DELETE, same idea most PHP courses
        // teach.
        if ($method === 'POST' && isset($_POST['_method'])) {
            $spoofed = strtoupper($_POST['_method']);
            if (in_array($spoofed, ['PATCH', 'DELETE'], true)) {
                $method = $spoofed;
            }
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->match($route['path'], $path);
            if ($params === null) {
                continue;
            }

            foreach ($route['middleware'] as $middlewareClass) {
                /** @var \App\Middleware\Middleware $middleware */
                $middleware = $this->container->make($middlewareClass);
                $middleware->handle();
            }

            [$class, $action] = $route['action'];
            $controller = $this->container->make($class);
            $controller->$action(...$params);
            return;
        }

        throw new AbortException(404, '404 - Page not found.');
    }

    private function match(string $routePath, string $requestPath): ?array
    {
        $routeParts = $routePath === '' ? [] : explode('/', $routePath);
        $requestParts = $requestPath === '' ? [] : explode('/', $requestPath);

        if (count($routeParts) !== count($requestParts)) {
            return null;
        }

        $params = [];

        foreach ($routeParts as $index => $part) {
            $requestPart = $requestParts[$index];

            if (str_starts_with($part, '{') && str_ends_with($part, '}')) {
                $params[] = $requestPart;
                continue;
            }

            if ($part !== $requestPart) {
                return null;
            }
        }

        return $params;
    }
}
