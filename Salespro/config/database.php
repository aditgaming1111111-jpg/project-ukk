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
    $conn->query("CREATE TABLE IF NOT EXISTS activity_log (
        log_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NULL,
        nama_pengguna VARCHAR(100) NULL,
        module VARCHAR(50) NOT NULL,
        action VARCHAR(100) NOT NULL,
        details TEXT NULL,
        status ENUM('success', 'error', 'warning', 'info') NOT NULL DEFAULT 'success',
        ip_address VARCHAR(45) DEFAULT NULL,
        user_agent TEXT DEFAULT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        KEY idx_user_id (user_id),
        KEY idx_created_at (created_at),
        KEY idx_module (module)
    )");

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

function logActivity(string $action, string $module = 'General', string $details = '', ?int $userId = null, string $status = 'success'): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $conn = dbConnect();
    $currentUserId = $userId ?? ($_SESSION['user_id'] ?? null);
    $currentUserName = $_SESSION['user_name'] ?? null;
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '-';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '-';

    $stmt = $conn->prepare('INSERT INTO activity_log (user_id, nama_pengguna, module, action, details, status, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('isssssss', $currentUserId, $currentUserName, $module, $action, $details, $status, $ipAddress, $userAgent);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}
