<?php
require_once __DIR__ . '/../config/database.php';

$userId = $_SESSION['user_id'] ?? null;
$userName = $_SESSION['user_name'] ?? null;
if ($userId || $userName) {
    logActivity('Logout', 'Auth', 'User logout dari sistem', (int) $userId, 'info');
}

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'], $params['secure'], $params['httponly']
    );
}
session_destroy();

redirect('/auth/login.php');
