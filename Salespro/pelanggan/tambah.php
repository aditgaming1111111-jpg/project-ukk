<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['administrator']);

$pageTitle = 'Tambah Pelanggan - SALESPRO';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_pelanggan'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    $jenis = $_POST['jenis'] ?? 'umum';

    if ($nama === '') {
        flash('error', 'Nama pelanggan wajib diisi.');
        redirect('/pelanggan/tambah.php');
    }

    $conn = dbConnect();
    $stmt = $conn->prepare('INSERT INTO pelanggan (nama_pelanggan, alamat, telepon, jenis) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('ssss', $nama, $alamat, $telepon, $jenis);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    flash('success', 'Data pelanggan berhasil ditambahkan.');
    redirect('/pelanggan/index.php');
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<body>
    <div class="app-shell">
        <?php include __DIR__ . '/../includes/sidebar.php'; ?>
        <main class="main-content">
            <?php include __DIR__ . '/../includes/topbar.php'; ?>
            <div class="page-content">
                <div class="page-header">
                    <h2>Tambah Pelanggan</h2>
                    <a href="index.php" class="link-btn primary">Kembali</a>
                </div>

                <div class="form-card">
                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nama Pelanggan</label>
                                <input type="text" name="nama_pelanggan" required>
                            </div>
                            <div class="form-group">
                                <label>Telepon</label>
                                <input type="text" name="telepon">
                            </div>
                            <div class="form-group">
                                <label>Jenis</label>
                                <select name="jenis">
                                    <option value="umum">Umum</option>
                                    <option value="member">Member</option>
                                </select>
                            </div>
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label>Alamat</label>
                                <input type="text" name="alamat">
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-success">Simpan</button>
                            <a href="index.php" class="btn btn-primary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
