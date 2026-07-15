<?php

namespace App\Config;

class Router
{
    private array $routes = [];
    private array $middlewareMap = [];

    public function __construct()
    {
        $routes = require __DIR__ . '/../../config/routes.php';
        foreach ($routes as $pattern => $config) {
            $this->routes[] = [
                'pattern' => $this->patternToRegex($pattern),
                'original' => $pattern,
                'config' => $config,
            ];
        }
    }

    private function patternToRegex(string $pattern): string
    {
        $regex = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $pattern);
        return '#^' . $regex . '$#';
    }

    public function registerMiddleware(string $name, string $class): void
    {
        $this->middlewareMap[$name] = $class;
    }

    public function dispatch(string $uri, string $method): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        if ($basePath !== '/' && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }
        $uri = $uri ?: '/';

        foreach ($this->routes as $route) {
            if (!preg_match($route['pattern'], $uri, $matches)) {
                continue;
            }

            if ($route['config']['method'] !== $method) {
                continue;
            }

            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

            foreach ($params as $k => $v) {
                $_GET[$k] = $v;
                $_REQUEST[$k] = $v;
            }

            if (isset($route['config']['middleware'])) {
                foreach ($route['config']['middleware'] as $mwName) {
                    if (isset($this->middlewareMap[$mwName])) {
                        $mwClass = $this->middlewareMap[$mwName];
                        $middleware = new $mwClass();
                        if (!$middleware->handle()) {
                            return;
                        }
                    }
                }
            }

            $controllerClass = 'App\\Controllers\\' . $route['config']['controller'];
            $action = $route['config']['action'];

            if (!class_exists($controllerClass)) {
                http_response_code(500);
                echo "Controller {$controllerClass} not found";
                return;
            }

            $controller = new $controllerClass();
            if (!method_exists($controller, $action)) {
                http_response_code(500);
                echo "Action {$action} not found in {$controllerClass}";
                return;
            }

            $controller->$action();
            return;
        }

        http_response_code(404);
        require base_path('Views' . DIRECTORY_SEPARATOR . 'errors' . DIRECTORY_SEPARATOR . '404.php');
    }
}
