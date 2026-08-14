<?php

declare(strict_types=1);

$publicDirectory = __DIR__;
$requestedPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$requestedFile = $publicDirectory . $requestedPath;

// Le serveur PHP intégré doit livrer les assets (CSS, JS, images) directement.
if ($requestedPath !== '/' && is_file($requestedFile)) {
    return false;
}

require $publicDirectory . '/index.php';
