<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../app/Config/Helpers.php';

$dotenv = __DIR__ . '/../.env';
if (file_exists($dotenv)) {
    $lines = file($dotenv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

error_reporting(E_ALL);
$debug = env('APP_DEBUG', true);
if ($debug) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    ini_set('display_errors', 0);
}

$router = new \App\Config\Router();

$router->registerMiddleware('Auth', \App\Middleware\Auth::class);
$router->registerMiddleware('Admin', \App\Middleware\Admin::class);

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$router->dispatch($uri, $method);
