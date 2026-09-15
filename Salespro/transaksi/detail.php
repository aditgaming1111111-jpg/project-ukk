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

$pageTitle = 'Detail Transaksi - SALESPRO';
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<body>
    <div class="app-shell">
        <?php include __DIR__ . '/../includes/sidebar.php'; ?>
        <main class="main-content">
            <?php include __DIR__ . '/../includes/topbar.php'; ?>
            <div class="page-content">
                <div class="page-header">
                    <h2>Detail Transaksi</h2>
                    <a href="riwayat.php" class="link-btn primary">Kembali</a>
                </div>

                <div class="panel">
                    <div class="summary-box">
                        <p><strong>Nomor Transaksi:</strong> #<?php echo htmlspecialchars($transaction['transaksi_id']); ?></p>
                        <p><strong>Tanggal:</strong> <?php echo formatDateTime($transaction['tanggal']); ?></p>
                        <p><strong>Pelanggan:</strong> <?php echo htmlspecialchars($transaction['nama_pelanggan'] ?? '-'); ?></p>
                        <p><strong>Kasir:</strong> <?php echo htmlspecialchars($transaction['nama_pengguna'] ?? '-'); ?></p>
                    </div>

                    <div class="table-wrap" style="margin-top:16px;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Produk</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($itemResult->num_rows > 0): ?>
                                    <?php $no = 1; while ($item = $itemResult->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo htmlspecialchars($item['nama_produk']); ?></td>
                                            <td><?php echo formatRupiah($item['harga']); ?></td>
                                            <td><?php echo htmlspecialchars($item['jumlah']); ?></td>
                                            <td><?php echo formatRupiah($item['subtotal']); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada item transaksi.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="summary-box">
                        <div>Total</div>
                        <div class="summary-total"><?php echo formatRupiah($transaction['total_harga']); ?></div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
