<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';
require_once 'middleware/ApiAuth.php';
require_once 'helpers/ApiResponse.php';

$url      = isset($_GET['url']) ? trim($_GET['url'], '/') : '';
$segments = explode('/', $url);
$resource = $segments[0] ?? '';
$id       = $segments[1] ?? null;
$method   = $_SERVER['REQUEST_METHOD'];

// Public routes (no auth needed)
$publicRoutes = ['auth', 'api/auth'];


// Check authentication for protected routes
if (!in_array($resource, $publicRoutes)) {
    $auth = new ApiAuth();
    if (!$auth->verify()) {
        ApiResponse::error('Unauthorized. Please provide valid token.', 401);
        exit();
    }
}

// Route to correct handler
$controllerFile = "controllers/{$resource}.php";

if (file_exists($controllerFile)) {
    require_once $controllerFile;
} else {
    ApiResponse::error('Endpoint not found', 404);
}