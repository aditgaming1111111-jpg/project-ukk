<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['administrator']);

$conn = dbConnect();
$rows = $conn->query("SELECT DATE(tanggal) AS tanggal, COUNT(*) AS jumlah_transaksi, SUM(total_harga) AS total FROM transaksi GROUP BY DATE(tanggal) ORDER BY DATE(tanggal) DESC");

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="laporan_harian.csv"');

$f = fopen('php://output', 'w');
fputcsv($f, ['Tanggal', 'Jumlah Transaksi', 'Total']);
while ($row = $rows->fetch_assoc()) {
    fputcsv($f, [$row['tanggal'], $row['jumlah_transaksi'], $row['total']]);
}
fclose($f);
$conn->close();
