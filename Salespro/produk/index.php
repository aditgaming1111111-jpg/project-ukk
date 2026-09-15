<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['administrator']);

$pageTitle = 'Data Produk - SALESPRO';
$conn = dbConnect();
$search = trim($_GET['search'] ?? '');

$sql = 'SELECT * FROM produk WHERE nama_produk LIKE ? ORDER BY produk_id DESC';
$stmt = $conn->prepare($sql);
$like = '%' . $search . '%';
$stmt->bind_param('s', $like);
$stmt->execute();
$result = $stmt->get_result();

$success = flash('success');
$error = flash('error');
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
                <?php if ($success): ?><div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
                <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

                <div class="page-header">
                    <h2>Data Produk</h2>
                    <div class="controls">
                        <form method="GET" class="controls" style="margin:0;">
                            <input type="text" class="search-box" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari produk...">
                        </form>
                        <a href="tambah.php" class="link-btn primary">+ Tambah Produk</a>
                    </div>
                </div>

                <div class="panel">
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Produk</th>
                                    <th>Satuan</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Exp</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo htmlspecialchars($row['nama_produk']); ?></td>
                                            <td><?php echo htmlspecialchars($row['satuan'] ?? 'pcs'); ?></td>
                                            <td><?php echo formatRupiah($row['harga']); ?></td>
                                            <td><?php echo htmlspecialchars($row['stok']); ?></td>
                                            <td><?php echo formatDate($row['tanggal_kadaluarsa']); ?></td>
                                            <td>
                                                <div class="inline-actions">
                                                    <a href="edit.php?id=<?php echo $row['produk_id']; ?>" class="link-btn success">Edit</a>
                                                    <a href="hapus.php?id=<?php echo $row['produk_id']; ?>" class="link-btn danger" onclick="return confirm('Hapus produk ini?')">Hapus</a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data produk.</td>
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
