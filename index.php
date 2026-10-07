<?php

session_start();

// catch any real error (not our own 404s) and show it as a meme instead of
// a raw PHP fatal-error dump
set_exception_handler(function (Throwable $exception) {
    http_response_code(500);
    require __DIR__ . '/views/errors/crash.php';
});

// core runner, pls do not remove it.
if (PHP_SAPI === 'cli-server') {
    $requestedFile = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($requestedFile)) {
        return false;
    }
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Loader.php';

// parser URL, this line will divide the uri into several parts
$uri      = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base     = trim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$path     = trim(substr($uri, strlen($base) + ($base === '' ? 0 : 1)), '/');
$segments = explode('/', $path);

define('BASE_URL', $base === '' ? '' : '/' . $base);

$noIdActions = ['create', 'store', 'login', 'logout'];

// Routing alias & default controller
$rawPage = strtolower($segments[0] ?? '');
if ($rawPage === '' || $rawPage === 'home') {
    $page = isset($_SESSION['user']) ? 'accounts' : 'auth';
} elseif ($rawPage === 'login') {
    $page = 'auth';
} elseif ($rawPage === 'account-type') {
    $page = 'account-types';
} else {
    $page = $rawPage;
}

// Global Auth Guard: Redirect unauthenticated user to login
if (!isset($_SESSION['user']) && $page !== 'auth') {
    header('Location: ' . BASE_URL . '/auth');
    exit;
}

// Non-admin users can only access their own role-specific account dashboard.
if (
    isset($_SESSION['user'])
    && strcasecmp(trim($_SESSION['user']['account_type_name'] ?? ''), 'Admin') !== 0
    && in_array($page, ['account-types', 'actions'], true)
) {
    http_response_code(403);
    echo '403 - Halaman ini hanya dapat diakses oleh admin.';
    exit;
}

// If already logged in, prevent accessing login page unless logging out
if (isset($_SESSION['user']) && $page === 'auth' && ($segments[1] ?? '') !== 'logout') {
    header('Location: ' . BASE_URL . '/accounts');
    exit;
}

if (!isset($segments[1]) || $segments[1] === '') {
    $id     = null;
    $action = ($page === 'auth') ? 'login' : 'index';
} elseif (in_array($segments[1], $noIdActions, true)) {
    $id     = null;
    $action = $segments[1];
} else {
    $id     = $segments[1];
    $action = $segments[2] ?? 'detail';
}

$method = $_SERVER['REQUEST_METHOD'];

// actions that must come from a form submission (POST), not a plain link/URL (GET)
$postOnlyActions = ['store', 'update', 'delete'];
$expectsPost     = in_array($action, $postOnlyActions, true);

$controllerName = str_replace('-', '', ucwords($page, '-'));
$controllerFile = __DIR__ . '/controllers/' . $controllerName . '.php';

if (!file_exists($controllerFile)) {
    http_response_code(404);
    echo '404 - Page not found';
    exit;
}

require_once $controllerFile;

if (!class_exists($controllerName)) {
    http_response_code(404);
    echo '404 - Page not found';
    exit;
}

// Exception for Auth::login which can handle both GET (render form) and POST (submit login)
$isAuthLogin = ($controllerName === 'Auth' && $action === 'login');

if (!$isAuthLogin) {
    if (($expectsPost && $method !== 'POST') || (!$expectsPost && $method !== 'GET')) {
        http_response_code(404);
        echo '404 - Action not found';
        exit;
    }
}

$controller = new $controllerName();

if (!method_exists($controller, $action)) {
    http_response_code(404);
    echo '404 - Action not found';
    exit;
}

if ($id === null) {
    $controller->$action();
} else {
    $controller->$action($id);
}
