<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$pageTitle = 'Dashboard - SALESPRO';
$conn = dbConnect();

$totalProduk = (int) $conn->query('SELECT COUNT(*) AS total FROM produk')->fetch_assoc()['total'];
$totalPelanggan = (int) $conn->query('SELECT COUNT(*) AS total FROM pelanggan')->fetch_assoc()['total'];
$totalTransaksi = (int) $conn->query('SELECT COUNT(*) AS total FROM transaksi')->fetch_assoc()['total'];
$stokMenipis = (int) $conn->query('SELECT COUNT(*) AS total FROM produk WHERE stok <= 5')->fetch_assoc()['total'];

$chartData = [];
$chartResult = $conn->query("SELECT DATE(tanggal) AS tanggal, COALESCE(SUM(total_harga), 0) AS total FROM transaksi GROUP BY DATE(tanggal) ORDER BY DATE(tanggal) DESC LIMIT 7");
while ($row = $chartResult->fetch_assoc()) {
    $chartData[] = [
        'label' => date('d M', strtotime($row['tanggal'])),
        'value' => (float) $row['total'],
    ];
}
$chartData = array_reverse($chartData);

$recentTransactions = $conn->query("SELECT t.transaksi_id, t.tanggal, p.nama_pelanggan, t.total_harga FROM transaksi t LEFT JOIN pelanggan p ON p.pelanggan_id = t.pelanggan_id ORDER BY t.tanggal DESC LIMIT 5");
$conn->close();

$message = flash('success');
$error = flash('error');
?>
<?php include __DIR__ . '/includes/header.php'; ?>
<body>
    <div class="app-shell">
        <?php include __DIR__ . '/includes/sidebar.php'; ?>

        <main class="main-content">
            <?php include __DIR__ . '/includes/topbar.php'; ?>

            <div class="page-content">
                <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
                <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">Total Produk</div>
                        <div class="stat-value"><?php echo $totalProduk; ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">Total Pelanggan</div>
                        <div class="stat-value"><?php echo $totalPelanggan; ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">Total Transaksi</div>
                        <div class="stat-value"><?php echo $totalTransaksi; ?></div>
                    </div>
                    <div class="stat-card warning">
                        <div class="stat-header">Stok Menipis</div>
                        <div class="stat-value"><?php echo $stokMenipis; ?></div>
                    </div>
                </div>

                <div class="panel chart-panel">
                    <div class="panel-header">
                        <h3>Grafik Penjualan Harian</h3>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="salesChart" height="260"></canvas>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-header">
                        <h3>Transaksi Terbaru</h3>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Transaksi</th>
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recentTransactions->num_rows > 0): ?>
                                <?php $no = 1; while ($row = $recentTransactions->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td>#<?php echo htmlspecialchars($row['transaksi_id']); ?></td>
                                        <td><?php echo formatDateTime($row['tanggal']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_pelanggan'] ?? '-'); ?></td>
                                        <td><?php echo formatRupiah($row['total_harga']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada transaksi.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="<?php echo BASE_URL; ?>/assets/js/script.js"></script>
    <script>
        const salesChartData = <?php echo json_encode($chartData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP); ?>;

        function formatShortCurrency(value) {
            if (value >= 1000000) {
                return 'Rp ' + (value / 1000000).toFixed(1).replace(/\.0$/, '') + 'M';
            }
            if (value >= 1000) {
                return 'Rp ' + (value / 1000).toFixed(1).replace(/\.0$/, '') + 'k';
            }
            return 'Rp ' + Math.round(value);
        }

        function drawSalesChart() {
            const canvas = document.getElementById('salesChart');
            if (!canvas || salesChartData.length === 0) return;

            const ctx = canvas.getContext('2d');
            const rect = canvas.getBoundingClientRect();
            const ratio = window.devicePixelRatio || 1;
            const width = Math.max(rect.width || 600, 300);
            const height = 260;

            canvas.width = width * ratio;
            canvas.height = height * ratio;
            ctx.setTransform(ratio, 0, 0, ratio, 0, 0);

            const padding = { top: 20, right: 20, bottom: 40, left: 60 };
            const chartWidth = width - padding.left - padding.right;
            const chartHeight = height - padding.top - padding.bottom;
            const maxValue = Math.max(...salesChartData.map(item => item.value), 1);

            ctx.clearRect(0, 0, width, height);

            ctx.strokeStyle = '#dfe7f5';
            ctx.lineWidth = 1;
            ctx.font = '11px sans-serif';
            ctx.fillStyle = '#64748b';

            for (let i = 0; i <= 4; i++) {
                const y = padding.top + (chartHeight / 4) * i;
                const value = maxValue - (maxValue / 4) * i;

                ctx.beginPath();
                ctx.moveTo(padding.left, y);
                ctx.lineTo(width - padding.right, y);
                ctx.stroke();

                ctx.textAlign = 'right';
                ctx.fillText(formatShortCurrency(value), padding.left - 8, y + 4);
            }

            ctx.beginPath();
            ctx.moveTo(padding.left, padding.top);
            ctx.lineTo(padding.left, height - padding.bottom);
            ctx.lineTo(width - padding.right, height - padding.bottom);
            ctx.strokeStyle = '#94a3b8';
            ctx.stroke();

            const barWidth = Math.min(42, chartWidth / salesChartData.length * 0.7);
            const gap = chartWidth / salesChartData.length;

            salesChartData.forEach((item, index) => {
                const barHeight = (item.value / maxValue) * chartHeight;
                const x = padding.left + (index * gap) + (gap - barWidth) / 2;
                const y = height - padding.bottom - barHeight;

                ctx.fillStyle = '#2f6fed';
                ctx.fillRect(x, y, barWidth, barHeight);

                ctx.fillStyle = '#475569';
                ctx.textAlign = 'center';
                ctx.fillText(item.label, x + barWidth / 2, height - 14);

                ctx.fillStyle = '#1e293b';
                ctx.font = '10px sans-serif';
                ctx.fillText(formatShortCurrency(item.value), x + barWidth / 2, y - 8);
                ctx.font = '11px sans-serif';
            });
        }

        drawSalesChart();
        window.addEventListener('resize', drawSalesChart);
    </script>
</body>
</html>
