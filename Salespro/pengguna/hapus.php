<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['administrator']);

$id = (int) ($_GET['id'] ?? 0);
if ($id > 0) {
    $conn = dbConnect();
    $stmt = $conn->prepare('DELETE FROM pengguna WHERE pengguna_id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    logActivity('Hapus pengguna', 'Pengguna', 'Menghapus pengguna dengan ID ' . $id, $_SESSION['user_id'] ?? null, 'warning');
    flash('success', 'Data pengguna berhasil dihapus.');
}

redirect('/pengguna/index.php');
