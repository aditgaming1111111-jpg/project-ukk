<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['administrator']);

$id = (int) ($_GET['id'] ?? 0);
if ($id > 0) {
    $conn = dbConnect();
    $stmt = $conn->prepare('DELETE FROM pelanggan WHERE pelanggan_id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    logActivity('Hapus pelanggan', 'Pelanggan', 'Menghapus pelanggan dengan ID ' . $id, $_SESSION['user_id'] ?? null, 'warning');
    flash('success', 'Data pelanggan berhasil dihapus.');
}

redirect('/pelanggan/index.php');
