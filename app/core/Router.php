<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /**
     * @var array<string, array<string, callable|array>>
     */
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get(string $uri, callable|array $action): void
    {
        $this->addRoute('GET', $uri, $action);
    }

    public function post(string $uri, callable|array $action): void
    {
        $this->addRoute('POST', $uri, $action);
    }

    public function dispatch(string $uri, string $method): void
    {
        $path = $this->normalizePath($uri);
        $method = strtoupper($method);
        $action = $this->routes[$method][$path] ?? null;

        if ($action === null) {
            http_response_code(404);
            echo '404 | Stranka nebola najdena.';

            return;
        }

        $this->invoke($action);
    }

    private function addRoute(string $method, string $uri, callable|array $action): void
    {
        $this->routes[$method][$this->normalizePath($uri)] = $action;
    }

    private function normalizePath(string $uri): string
    {
        // Upravi URL na jednotny tvar.
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');

        if ($base !== '' && $base !== '/' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
        }

        $path = '/' . trim($path, '/');

        return $path === '//' ? '/' : $path;
    }

    private function invoke(callable|array $action): void
    {
        // Spusti controller alebo callback.
        if (is_array($action) && count($action) === 2) {
            [$controller, $method] = $action;
            $controller->{$method}();

            return;
        }

        call_user_func($action);
    }
}
