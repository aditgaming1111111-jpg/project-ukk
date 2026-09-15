<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['administrator']);

$pageTitle = 'Laporan - SALESPRO';
$conn = dbConnect();

$selectedMonth = $_GET['bulan'] ?? date('Y-m');
$monthsResult = $conn->query("SELECT DISTINCT DATE_FORMAT(tanggal, '%Y-%m') AS bulan FROM transaksi ORDER BY bulan DESC");

$dailyStmt = $conn->prepare("SELECT DATE(tanggal) AS tanggal, COUNT(*) AS jumlah_transaksi, SUM(total_harga) AS total FROM transaksi WHERE DATE_FORMAT(tanggal, '%Y-%m') = ? GROUP BY DATE(tanggal) ORDER BY DATE(tanggal) DESC");
$dailyStmt->bind_param('s', $selectedMonth);
$dailyStmt->execute();
$daily = $dailyStmt->get_result();
$dailyStmt->close();

$monthly = $conn->query("SELECT DATE_FORMAT(tanggal, '%Y-%m') AS bulan, COUNT(*) AS jumlah_transaksi, SUM(total_harga) AS total FROM transaksi GROUP BY DATE_FORMAT(tanggal, '%Y-%m') ORDER BY bulan DESC");

$selectedMonthStmt = $conn->prepare("SELECT DATE_FORMAT(tanggal, '%Y-%m') AS bulan, COUNT(*) AS jumlah_transaksi, SUM(total_harga) AS total FROM transaksi WHERE DATE_FORMAT(tanggal, '%Y-%m') = ? GROUP BY DATE_FORMAT(tanggal, '%Y-%m')");
$selectedMonthStmt->bind_param('s', $selectedMonth);
$selectedMonthStmt->execute();
$selectedMonthData = $selectedMonthStmt->get_result()->fetch_assoc();
$selectedMonthStmt->close();

$topProductsStmt = $conn->prepare("SELECT pr.nama_produk, SUM(dt.jumlah) AS total_terjual FROM detail_transaksi dt JOIN transaksi t ON t.transaksi_id = dt.transaksi_id JOIN produk pr ON pr.produk_id = dt.produk_id WHERE DATE_FORMAT(t.tanggal, '%Y-%m') = ? GROUP BY pr.produk_id ORDER BY total_terjual DESC LIMIT 5");
$topProductsStmt->bind_param('s', $selectedMonth);
$topProductsStmt->execute();
$topProducts = $topProductsStmt->get_result();
$topProductsStmt->close();

$exportType = $_GET['export'] ?? '';
if ($exportType === 'excel') {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="laporan_salespro_' . $selectedMonth . '.xls"');
    echo "Tanggal\tJumlah Transaksi\tTotal\n";
    $exportStmt = $conn->prepare("SELECT DATE(tanggal) AS tanggal, COUNT(*) AS jumlah_transaksi, SUM(total_harga) AS total FROM transaksi WHERE DATE_FORMAT(tanggal, '%Y-%m') = ? GROUP BY DATE(tanggal) ORDER BY DATE(tanggal) DESC");
    $exportStmt->bind_param('s', $selectedMonth);
    $exportStmt->execute();
    $exportResult = $exportStmt->get_result();
    while ($row = $exportResult->fetch_assoc()) {
        echo $row['tanggal'] . "\t" . $row['jumlah_transaksi'] . "\t" . $row['total'] . "\n";
    }
    $exportStmt->close();
    $conn->close();
    exit;
}

if ($exportType === 'pdf') {
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="laporan_salespro_' . $selectedMonth . '.pdf"');
    echo "Laporan PDF belum tersedia pada versi ini. Silakan gunakan export Excel atau lihat data di halaman ini.";
    $conn->close();
    exit;
}

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
                    <h2>Laporan Penjualan</h2>
                    <div class="controls" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
                        <form method="GET" style="display:flex; gap:10px; align-items:center;">
                            <select name="bulan" aria-label="Pilih bulan laporan">
                                <?php while ($month = $monthsResult->fetch_assoc()): ?>
                                    <option value="<?php echo htmlspecialchars($month['bulan']); ?>" <?php echo $month['bulan'] === $selectedMonth ? 'selected' : ''; ?>>
                                        <?php echo date('F Y', strtotime($month['bulan'] . '-01')); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <button type="submit" class="btn btn-primary">Lihat</button>
                        </form>
                        <a href="?bulan=<?php echo urlencode($selectedMonth); ?>&export=excel" class="link-btn success">Export Excel</a>
                        <a href="?bulan=<?php echo urlencode($selectedMonth); ?>&export=pdf" class="link-btn primary">Export PDF</a>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">Laporan Bulan</div>
                        <div class="stat-value"><?php echo date('F Y', strtotime($selectedMonth . '-01')); ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">Total Transaksi</div>
                        <div class="stat-value"><?php echo htmlspecialchars((string) ($selectedMonthData['jumlah_transaksi'] ?? 0)); ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">Total Pendapatan</div>
                        <div class="stat-value"><?php echo formatRupiah($selectedMonthData['total'] ?? 0); ?></div>
                    </div>
                </div>

                <div class="panel" style="margin-bottom:20px;">
                    <div class="panel-header">
                        <h3>Laporan Harian - <?php echo date('F Y', strtotime($selectedMonth . '-01')); ?></h3>
                    </div>
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jumlah Transaksi</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($daily->num_rows > 0): ?>
                                    <?php while ($row = $daily->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo formatDate($row['tanggal']); ?></td>
                                            <td><?php echo htmlspecialchars($row['jumlah_transaksi']); ?></td>
                                            <td><?php echo formatRupiah($row['total']); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="text-center">Belum ada laporan harian pada bulan ini.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-header">
                        <h3>Produk Terlaris - <?php echo date('F Y', strtotime($selectedMonth . '-01')); ?></h3>
                    </div>
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Jumlah Terjual</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($topProducts->num_rows > 0): ?>
                                    <?php while ($row = $topProducts->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['nama_produk']); ?></td>
                                            <td><?php echo htmlspecialchars($row['total_terjual']); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="2" class="text-center">Belum ada penjualan pada bulan ini.</td></tr>
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
