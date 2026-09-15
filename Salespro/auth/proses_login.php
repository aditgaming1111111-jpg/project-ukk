<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/auth/login.php');
}

$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    flash('error', 'Email dan password wajib diisi.');
    redirect('/auth/login.php');
}

$conn = dbConnect();
$stmt = $conn->prepare('SELECT pengguna_id, nama_pengguna, email, password, peran FROM pengguna WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();

$passwordValid = false;

if ($user) {
    $storedPassword = (string) ($user['password'] ?? '');
    $passwordValid = password_verify($password, $storedPassword) || hash_equals($storedPassword, $password);
}

if (!$user || !$passwordValid) {
    flash('error', 'Email atau password salah.');
    redirect('/auth/login.php');
}

// Upgrade legacy/plain-text demo passwords to a secure bcrypt hash after login.
if ($user && hash_equals((string) $user['password'], $password)) {
    $conn = dbConnect();
    $newHash = password_hash($password, PASSWORD_DEFAULT);
    $update = $conn->prepare('UPDATE pengguna SET password = ? WHERE pengguna_id = ?');
    $update->bind_param('si', $newHash, $user['pengguna_id']);
    $update->execute();
    $update->close();
    $conn->close();
}

$_SESSION['user_id'] = $user['pengguna_id'];
$_SESSION['user_name'] = $user['nama_pengguna'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_role'] = $user['peran'];

redirect('/dashboard.php');
