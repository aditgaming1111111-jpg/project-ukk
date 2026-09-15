<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    flash('error', 'ID transaksi tidak valid.');
    redirect('/transaksi/riwayat.php');
}

$conn = dbConnect();
$stmt = $conn->prepare('SELECT t.*, p.nama_pelanggan, u.nama_pengguna FROM transaksi t LEFT JOIN pelanggan p ON p.pelanggan_id = t.pelanggan_id LEFT JOIN pengguna u ON u.pengguna_id = t.pengguna_id WHERE t.transaksi_id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$transaction = $stmt->get_result()->fetch_assoc();
$stmt->close();

$items = $conn->prepare('SELECT dt.*, pr.nama_produk FROM detail_transaksi dt JOIN produk pr ON pr.produk_id = dt.produk_id WHERE dt.transaksi_id = ?');
$items->bind_param('i', $id);
$items->execute();
$itemResult = $items->get_result();
$items->close();
$conn->close();

if (!$transaction) {
    flash('error', 'Data transaksi tidak ditemukan.');
    redirect('/transaksi/riwayat.php');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi - SALESPRO</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <style>
        body { background: #fff; padding: 32px; }
        .receipt { max-width: 420px; margin: 0 auto; border: 1px solid #ddd; padding: 24px; border-radius: 12px; }
        .receipt h2, .receipt h3 { text-align: center; margin: 0 0 12px; }
        .receipt-table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        .receipt-table th, .receipt-table td { padding: 6px 0; border-bottom: 1px dashed #ddd; font-size: 12px; }
        .totals { margin-top: 18px; }
        .totals-row { display: flex; justify-content: space-between; margin: 6px 0; }
        .receipt-actions { display: flex; gap: 10px; justify-content: center; margin-top: 20px; flex-wrap: wrap; }
        .receipt-actions .btn { text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
    </style>
</head>
<body>
    <div class="receipt">
        <h2>SALESPRO</h2>
        <h3>Struk Penjualan</h3>
        <p><strong>No Transaksi:</strong> #<?php echo htmlspecialchars($transaction['transaksi_id']); ?></p>
        <p><strong>Tanggal:</strong> <?php echo formatDateTime($transaction['tanggal']); ?></p>
        <p><strong>Pelanggan:</strong> <?php echo htmlspecialchars($transaction['nama_pelanggan'] ?? '-'); ?></p>
        <p><strong>Kasir:</strong> <?php echo htmlspecialchars($transaction['nama_pengguna'] ?? '-'); ?></p>

        <table class="receipt-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $itemResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['nama_produk']); ?></td>
                        <td><?php echo htmlspecialchars($item['jumlah']); ?></td>
                        <td><?php echo formatRupiah($item['subtotal']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="totals">
            <div class="totals-row"><span>Subtotal</span><span><?php echo formatRupiah($transaction['subtotal']); ?></span></div>
            <div class="totals-row"><span>Diskon</span><span><?php echo formatRupiah($transaction['diskon']); ?></span></div>
            <div class="totals-row"><span>Total</span><span><?php echo formatRupiah($transaction['total_harga']); ?></span></div>
            <div class="totals-row"><span>Uang Pembeli</span><span><?php echo formatRupiah($transaction['uang_pembeli']); ?></span></div>
            <div class="totals-row"><span>Kembali</span><span><?php echo formatRupiah($transaction['kembalian']); ?></span></div>
        </div>

        <div class="receipt-actions">
            <button class="btn btn-primary" onclick="window.print()">Print Struk</button>
            <a class="btn btn-success" href="<?php echo BASE_URL; ?>/transaksi/index.php">Kembali</a>
        </div>
    </div>
</body>
</html>
