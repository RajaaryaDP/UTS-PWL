<?php

session_start();
ob_start(); // tahan output tersasar (spasi/BOM) agar gambar captcha tidak rusak

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

$noIdActions = ['create', 'store', 'login', 'logout', 'change-password', 'update-password'];

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
// 'captcha' harus bisa diakses sebelum login (gambar dipakai di form login)
if (!isset($_SESSION['user']) && !in_array($page, ['auth', 'captcha'], true)) {
    header('Location: ' . BASE_URL . '/auth');
    exit;
}

// BARU: daftar peran yang boleh membuka halaman khusus (tambah peran di sini)
$allowedRoles = ['admin', 'staff'];
$adminPages   = ['account-types', 'actions'];
$currentRole  = strtolower(trim($_SESSION['user']['account_type_name'] ?? ''));

// Hanya peran yang diizinkan yang boleh membuka halaman khusus.
if (
    isset($_SESSION['user'])
    && !in_array($currentRole, $allowedRoles, true)
    && in_array($page, $adminPages, true)
) {
    http_response_code(403);
    echo '403 - Halaman ini hanya dapat diakses oleh admin atau staff.';
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
$postOnlyActions = ['store', 'update', 'delete', 'update-password'];
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

// Exception for Auth::login and change-password which can handle both GET and POST
$isDualMethodAction = ($controllerName === 'Auth' && $action === 'login')
    || ($controllerName === 'Accounts' && in_array($action, ['change-password', 'changePassword'], true));

if (!$isDualMethodAction) {
    if (($expectsPost && $method !== 'POST') || (!$expectsPost && $method !== 'GET')) {
        http_response_code(404);
        echo '404 - Action not found';
        exit;
    }
}

// CAPTCHA: wajib benar untuk login dan pembuatan akun (hanya saat POST).
// Jika salah, kembali ke form dengan pesan error (ditampilkan oleh captcha_field.php).
require_once __DIR__ . '/core/CaptchaHelper.php';
$captchaForms = [
    'Auth'     => ['login' => '/auth'],
    'Accounts' => ['store' => '/accounts/create'],
];
if ($method === 'POST' && isset($captchaForms[$controllerName][$action])) {
    error_log('captcha input=' . ($_POST['kodecaptcha'] ?? '') . ' | session=' . ($_SESSION['code'] ?? 'KOSONG'));
    if (!CaptchaHelper::verify($_POST['kodecaptcha'] ?? '')) {
        $_SESSION['captcha_error'] = 'Captcha salah atau kedaluwarsa. Silakan coba lagi.';
        header('Location: ' . BASE_URL . $captchaForms[$controllerName][$action]);
        exit;
    }
}

$controller = new $controllerName();

$callAction = $action;
if (!method_exists($controller, $callAction)) {
    $methodName = lcfirst(str_replace('-', '', ucwords($action, '-')));
    if (method_exists($controller, $methodName)) {
        $callAction = $methodName;
    } else {
        http_response_code(404);
        echo '404 - Action not found';
        exit;
    }
}

if ($id === null) {
    $controller->$callAction();
} else {
    $controller->$callAction($id);
}