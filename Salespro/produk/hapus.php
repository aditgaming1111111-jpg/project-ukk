<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['administrator']);

$id = (int) ($_GET['id'] ?? 0);
if ($id > 0) {
    $conn = dbConnect();
    $stmt = $conn->prepare('DELETE FROM produk WHERE produk_id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    flash('success', 'Data produk berhasil dihapus.');
}

redirect('/produk/index.php');
