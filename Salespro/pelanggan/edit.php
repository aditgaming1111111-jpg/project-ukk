<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['administrator']);

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    flash('error', 'ID pelanggan tidak valid.');
    redirect('/pelanggan/index.php');
}

$conn = dbConnect();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_pelanggan'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    $jenis = $_POST['jenis'] ?? 'umum';

    if ($nama === '') {
        flash('error', 'Nama pelanggan wajib diisi.');
        redirect('/pelanggan/edit.php?id=' . $id);
    }

    $stmt = $conn->prepare('UPDATE pelanggan SET nama_pelanggan = ?, alamat = ?, telepon = ?, jenis = ? WHERE pelanggan_id = ?');
    $stmt->bind_param('ssssi', $nama, $alamat, $telepon, $jenis, $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    flash('success', 'Data pelanggan berhasil diperbarui.');
    redirect('/pelanggan/index.php');
}

$stmt = $conn->prepare('SELECT * FROM pelanggan WHERE pelanggan_id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

if (!$customer) {
    flash('error', 'Data pelanggan tidak ditemukan.');
    redirect('/pelanggan/index.php');
}

$pageTitle = 'Edit Pelanggan - SALESPRO';
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<body>
    <div class="app-shell">
        <?php include __DIR__ . '/../includes/sidebar.php'; ?>
        <main class="main-content">
            <?php include __DIR__ . '/../includes/topbar.php'; ?>
            <div class="page-content">
                <div class="page-header">
                    <h2>Edit Pelanggan</h2>
                    <a href="index.php" class="link-btn primary">Kembali</a>
                </div>

                <div class="form-card">
                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nama Pelanggan</label>
                                <input type="text" name="nama_pelanggan" value="<?php echo htmlspecialchars($customer['nama_pelanggan']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Telepon</label>
                                <input type="text" name="telepon" value="<?php echo htmlspecialchars($customer['telepon'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Jenis</label>
                                <select name="jenis">
                                    <option value="umum" <?php echo ($customer['jenis'] ?? 'umum') === 'umum' ? 'selected' : ''; ?>>Umum</option>
                                    <option value="member" <?php echo ($customer['jenis'] ?? 'umum') === 'member' ? 'selected' : ''; ?>>Member</option>
                                </select>
                            </div>
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label>Alamat</label>
                                <input type="text" name="alamat" value="<?php echo htmlspecialchars($customer['alamat'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-success">Update</button>
                            <a href="index.php" class="btn btn-primary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
