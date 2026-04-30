<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $path;

// Let the built-in server handle existing static files.
if ($path !== '/' && file_exists($file) && !is_dir($file)) {
    return false;
}

require __DIR__ . '/index.php';
