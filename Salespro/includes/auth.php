<?php
require_once __DIR__ . '/../config/database.php';

function requireLogin(): void
{
    if (empty($_SESSION['user_id'])) {
        flash('error', 'Silakan login terlebih dahulu.');
        redirect('/auth/login.php');
    }
}

function requireRole(array $roles): void
{
    requireLogin();

    $role = $_SESSION['user_role'] ?? '';
    if (!in_array($role, $roles, true)) {
        flash('error', 'Anda tidak memiliki akses ke halaman ini.');
        redirect('/dashboard.php');
    }
}

function currentUser(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    return [
        'id' => $_SESSION['user_id'],
        'nama' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'role' => $_SESSION['user_role'],
    ];
}

function isAdmin(): bool
{
    return (currentUser()['role'] ?? '') === 'administrator';
}

function isKasir(): bool
{
    return (currentUser()['role'] ?? '') === 'petugas';
}

function formatRupiah($angka)
{
    if (!is_numeric($angka)) {
        return 'Rp 0';
    }

    return 'Rp ' . number_format((float) $angka, 0, ',', '.');
}

function formatDateTime($value)
{
    if (empty($value)) {
        return '-';
    }

    return date('d-m-Y H:i', strtotime($value));
}

function formatDate($value)
{
    if (empty($value)) {
        return '-';
    }

    return date('d-m-Y', strtotime($value));
}

function getMemberDiscountPercent(int $jumlahTransaksi): int
{
    if ($jumlahTransaksi >= 20) {
        return 10;
    }

    if ($jumlahTransaksi >= 10) {
        return 5;
    }

    return 0;
}

function getCustomerDiscountInfo(int $customerId): array
{
    $conn = dbConnect();
    $stmt = $conn->prepare('SELECT COUNT(*) AS total FROM transaksi WHERE pelanggan_id = ?');
    $stmt->bind_param('i', $customerId);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $conn->close();

    $total = (int) ($data['total'] ?? 0);
    $percent = getMemberDiscountPercent($total);

    return [
        'total' => $total,
        'percent' => $percent,
        'label' => $percent > 0 ? $percent . '% diskon member' : 'Tanpa diskon',
    ];
}
