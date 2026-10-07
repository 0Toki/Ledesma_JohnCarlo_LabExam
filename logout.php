<?php
require_once 'config.php';

// Clear session
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}
session_destroy();

// Clear remember me cookie
setcookie('remember_token', '', time() - 3600, '/');

redirect('login.php');
