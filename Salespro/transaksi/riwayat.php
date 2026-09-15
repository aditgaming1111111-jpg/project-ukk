<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$pageTitle = 'Riwayat Transaksi - SALESPRO';
$conn = dbConnect();
$search = trim($_GET['search'] ?? '');

$sql = 'SELECT t.*, p.nama_pelanggan FROM transaksi t LEFT JOIN pelanggan p ON p.pelanggan_id = t.pelanggan_id WHERE t.transaksi_id LIKE ? OR p.nama_pelanggan LIKE ? ORDER BY t.tanggal DESC';
$stmt = $conn->prepare($sql);
$likeTerm = '%' . $search . '%';
$stmt->bind_param('ss', $likeTerm, $likeTerm);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
$conn->close();
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<body>
    <div class="app-shell">
        <?php include __DIR__ . '/../includes/sidebar.php'; ?>
        <main class="main-content">
            <?php include __DIR__ . '/../includes/topbar.php'; ?>
            <div class="page-content">
                <div class="page-header">
                    <h2>Riwayat Transaksi</h2>
                    <div class="controls">
                        <form method="GET" class="controls" style="margin:0;">
                            <input type="text" class="search-box" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari transaksi...">
                        </form>
                    </div>
                </div>

                <div class="panel">
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>No Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Pelanggan</th>
                                    <th>Total</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td>#<?php echo htmlspecialchars($row['transaksi_id']); ?></td>
                                            <td><?php echo formatDateTime($row['tanggal']); ?></td>
                                            <td><?php echo htmlspecialchars($row['nama_pelanggan'] ?? '-'); ?></td>
                                            <td><?php echo formatRupiah($row['total_harga']); ?></td>
                                            <td><a href="detail.php?id=<?php echo $row['transaksi_id']; ?>" class="link-btn primary">Detail</a></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada transaksi.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
