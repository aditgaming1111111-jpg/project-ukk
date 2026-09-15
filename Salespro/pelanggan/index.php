<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['administrator']);

$pageTitle = 'Data Pelanggan - SALESPRO';
$conn = dbConnect();
$search = trim($_GET['search'] ?? '');

$sql = 'SELECT * FROM pelanggan WHERE nama_pelanggan LIKE ? ORDER BY pelanggan_id DESC';
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
                    <h2>Data Pelanggan</h2>
                    <div class="controls">
                        <form method="GET" class="controls" style="margin:0;">
                            <input type="text" class="search-box" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari pelanggan...">
                        </form>
                        <a href="tambah.php" class="link-btn primary">+ Tambah Pelanggan</a>
                    </div>
                </div>

                <div class="panel">
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Alamat</th>
                                    <th>Telepon</th>
                                    <th>Jenis</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo htmlspecialchars($row['nama_pelanggan']); ?></td>
                                            <td><?php echo htmlspecialchars($row['alamat'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($row['telepon'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars(ucfirst($row['jenis'] ?? 'umum')); ?></td>
                                            <td>
                                                <div class="inline-actions">
                                                    <a href="edit.php?id=<?php echo $row['pelanggan_id']; ?>" class="link-btn success">Edit</a>
                                                    <a href="hapus.php?id=<?php echo $row['pelanggan_id']; ?>" class="link-btn danger" onclick="return confirm('Hapus pelanggan ini?')">Hapus</a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada data pelanggan.</td>
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
