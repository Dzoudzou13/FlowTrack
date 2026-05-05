<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

session_start();

require_once dirname(__DIR__) . '/app/functions.php';

// Nacita routy a spracuje poziadavku.
$router = require base_path('routes.php');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
// Method override pre PUT/DELETE cez skryte pole _method vo formulari.
if ($method === 'POST' && isset($_POST['_method'])) {
    $override = strtoupper($_POST['_method']);
    if (in_array($override, ['PUT', 'PATCH', 'DELETE'], true)) {
        $method = $override;
    }
}

$router->dispatch($_SERVER['REQUEST_URI'] ?? '/', $method);
