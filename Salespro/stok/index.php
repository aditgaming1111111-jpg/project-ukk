<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$pageTitle = 'Stok Barang - SALESPRO';
$conn = dbConnect();
$search = trim($_GET['search'] ?? '');

$sql = 'SELECT * FROM produk WHERE nama_produk LIKE ? ORDER BY nama_produk ASC';
$stmt = $conn->prepare($sql);
$like = '%' . $search . '%';
$stmt->bind_param('s', $like);
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
                    <h2>Stok Barang</h2>
                    <div class="controls">
                        <form method="GET" class="controls" style="margin:0;">
                            <input type="text" class="search-box" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari stok produk...">
                        </form>
                    </div>
                </div>

                <div class="panel">
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Produk</th>
                                    <th>Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo htmlspecialchars($row['nama_produk']); ?></td>
                                            <td>
                                                <?php if ((int)$row['stok'] <= 5): ?>
                                                    <span class="badge badge-warning"><?php echo htmlspecialchars($row['stok']); ?> (Stok Menipis)</span>
                                                <?php else: ?>
                                                    <span class="badge badge-success"><?php echo htmlspecialchars($row['stok']); ?> (Stok Normal)</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">Tidak ada data stok.</td>
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
