<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_URL', '/project-ukk/Salespro');

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_salespro');

function dbConnect()
{
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die('Koneksi database gagal: ' . $conn->connect_error);
    }

    $conn->set_charset('utf8mb4');
    return $conn;
}

function redirect(string $path): void
{
    header('Location: ' . BASE_URL . $path);
    exit;
}

function flash(string $key, ?string $message = null)
{
    if ($message === null) {
        if (!empty($_SESSION[$key])) {
            $value = $_SESSION[$key];
            unset($_SESSION[$key]);
            return $value;
        }
        return null;
    }

    $_SESSION[$key] = $message;
}
