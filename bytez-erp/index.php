<?php
session_start();
// Note: Avoid ini_set() after session_start() in PHP.
// Session cookie settings should be configured in php.ini or via ini files before session_start().


require_once 'config/database.php';
require_once 'app/helpers/auth_helper.php';

// Lightweight debug logging to track auth redirect loop behavior.
$__dbgLog = __DIR__ . '/storage/debug-auth.log';
if (!is_dir(dirname($__dbgLog))) {
    @mkdir(dirname($__dbgLog), 0777, true);
}
$__dbg = function(string $line) use ($__dbgLog) {
    @file_put_contents($__dbgLog, '[' . date('c') . '] ' . $line . PHP_EOL, FILE_APPEND);
};
$__dbg('REQUEST ' . ($_SERVER['REQUEST_METHOD'] ?? '') . ' ' . ($_SERVER['REQUEST_URI'] ?? '') . ' url=' . (isset($_GET['url']) ? $_GET['url'] : ''));
$__dbg('SESSION user_id=' . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null') . ' sid=' . session_id());

// The PHP built-in server doesn't populate $_GET['url'] for pretty URLs.
// We extract the route from REQUEST_URI and remove the /Codebytez prefix.
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
$url = str_replace('/Codebytez', '', $requestPath);
$url = trim($url, '/');

if (empty($url)) {
    // If no path is provided, go to dashboard if logged in, otherwise login.
    $url = isset($_SESSION['user_id']) ? 'dashboard/index' : 'auth/login';
}

$segments = explode('/', $url);
$controllerName = ucfirst($segments[0]) . 'Controller';
$method = $segments[1] ?? 'index';
$param = $segments[2] ?? null;

$controllerFile = "app/controllers/{$controllerName}.php";

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controller = new $controllerName();
    if (method_exists($controller, $method)) {
        $controller->$method($param);
    } else {
        http_response_code(404);
        echo "<h2>Page not found</h2>";
    }
} else {
    http_response_code(404);
    echo "<h2>Controller not found: $controllerName</h2>";
}