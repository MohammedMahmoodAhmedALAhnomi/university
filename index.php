<?php
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = rtrim(dirname($scriptName), '/\\');
if ($basePath === '.' || $basePath === '/' || $basePath === '\\') {
    $basePath = '';
}
$relativePath = '/' . ltrim(substr($requestUri, strlen($basePath)), '/\\');
$publicFile = __DIR__ . '/public' . $relativePath;

if (file_exists($publicFile) && !is_dir($publicFile)) {
    $mimeTypes = [
        'css' => 'text/css', 'js' => 'application/javascript',
        'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
        'gif' => 'image/gif', 'svg' => 'image/svg+xml',
        'woff' => 'font/woff', 'woff2' => 'font/woff2',
        'pdf' => 'application/pdf', 'zip' => 'application/zip',
    ];
    $ext = strtolower(pathinfo($publicFile, PATHINFO_EXTENSION));
    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
    }
    readfile($publicFile);
    exit;
}

chdir(__DIR__ . '/public');
require __DIR__ . '/public/index.php';
