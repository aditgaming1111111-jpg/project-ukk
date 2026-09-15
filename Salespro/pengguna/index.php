<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['administrator']);

$pageTitle = 'Data Pengguna - SALESPRO';
$conn = dbConnect();
$sql = 'SELECT * FROM pengguna ORDER BY pengguna_id DESC';
$result = $conn->query($sql);
$success = flash('success');
$error = flash('error');
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
                    <h2>Data Pengguna</h2>
                    <a href="tambah.php" class="link-btn primary">+ Tambah Pengguna</a>
                </div>

                <div class="panel">
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Peran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo htmlspecialchars($row['nama_pengguna']); ?></td>
                                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                                            <td><?php echo htmlspecialchars($row['peran']); ?></td>
                                            <td>
                                                <div class="inline-actions">
                                                    <a href="edit.php?id=<?php echo $row['pengguna_id']; ?>" class="link-btn success">Edit</a>
                                                    <a href="hapus.php?id=<?php echo $row['pengguna_id']; ?>" class="link-btn danger" onclick="return confirm('Hapus pengguna ini?')">Hapus</a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data pengguna.</td>
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
