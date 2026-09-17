<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['administrator']);

$pageTitle = 'Tambah Produk - SALESPRO';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_produk'] ?? '');
    $harga = (float) ($_POST['harga'] ?? 0);
    $stok = (int) ($_POST['stok'] ?? 0);
    $satuan = trim($_POST['satuan'] ?? 'pcs');
    $jenisBarang = $_POST['jenis_barang'] ?? 'umum';
    $tanggalKadaluarsa = trim($_POST['tanggal_kadaluarsa'] ?? '');

    if ($nama === '' || $harga < 0 || $stok < 0) {
        flash('error', 'Nama, harga, dan stok harus valid.');
        redirect('/produk/tambah.php');
    }

    if ($jenisBarang === 'umum' && $tanggalKadaluarsa === '') {
        $tanggalKadaluarsa = null;
    }

    $conn = dbConnect();
    $stmt = $conn->prepare('INSERT INTO produk (nama_produk, harga, stok, satuan, jenis_barang, tanggal_kadaluarsa) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('sdisss', $nama, $harga, $stok, $satuan, $jenisBarang, $tanggalKadaluarsa);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    logActivity('Tambah produk', 'Produk', 'Menambah produk: ' . $nama . ' (stok: ' . $stok . ', harga: ' . $harga . ')', $_SESSION['user_id'] ?? null, 'success');
    flash('success', 'Data produk berhasil ditambahkan.');
    redirect('/produk/index.php');
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
                    <h2>Tambah Produk</h2>
                    <a href="index.php" class="link-btn primary">Kembali</a>
                </div>

                <div class="form-card">
                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nama Produk</label>
                                <input type="text" name="nama_produk" required>
                            </div>
                            <div class="form-group">
                                <label>Harga</label>
                                <input type="number" name="harga" min="0" step="1000" required>
                            </div>
                            <div class="form-group">
                                <label>Stok</label>
                                <input type="number" name="stok" min="0" required>
                            </div>
                            <div class="form-group">
                                <label>Satuan</label>
                                <select name="satuan">
                                    <option value="pcs">pcs</option>
                                    <option value="kg">kg</option>
                                    <option value="lusin">lusin</option>
                                    <option value="box">box</option>
                                    <option value="liter">liter</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Jenis Barang</label>
                                <select name="jenis_barang">
                                    <option value="umum">Umum</option>
                                    <option value="elektronik">Elektronik</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Tanggal Kadaluarsa</label>
                                <input type="date" name="tanggal_kadaluarsa">
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
