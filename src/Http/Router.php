<?php

declare(strict_types=1);

namespace DocPlatform\Http;

final class Router
{
    private array $routes = [];

    public function get(string $pattern, callable $handler): void
    {
        $this->routes[] = ['GET', $pattern, $handler];
    }

    public function post(string $pattern, callable $handler): void
    {
        $this->routes[] = ['POST', $pattern, $handler];
    }

    public function put(string $pattern, callable $handler): void
    {
        $this->routes[] = ['PUT', $pattern, $handler];
    }

    public function delete(string $pattern, callable $handler): void
    {
        $this->routes[] = ['DELETE', $pattern, $handler];
    }

    public function dispatch(Request $request): Response
    {
        foreach ($this->routes as [$method, $pattern, $handler]) {
            if ($method !== $request->method) {
                continue;
            }
            $regex = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $pattern);
            if (preg_match('#^' . $regex . '$#', $request->path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return $handler($request, ...array_values($params));
            }
        }
        return Response::json(['error' => 'Not Found'], 404);
    }
}
