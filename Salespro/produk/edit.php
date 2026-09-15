<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['administrator']);

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    flash('error', 'ID produk tidak valid.');
    redirect('/produk/index.php');
}

$conn = dbConnect();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_produk'] ?? '');
    $harga = (float) ($_POST['harga'] ?? 0);
    $stok = (int) ($_POST['stok'] ?? 0);
    $satuan = trim($_POST['satuan'] ?? 'pcs');
    $jenisBarang = $_POST['jenis_barang'] ?? 'umum';
    $tanggalKadaluarsa = trim($_POST['tanggal_kadaluarsa'] ?? '');

    if ($nama === '' || $harga < 0 || $stok < 0) {
        flash('error', 'Data produk tidak valid.');
        redirect('/produk/edit.php?id=' . $id);
    }

    $stmt = $conn->prepare('UPDATE produk SET nama_produk = ?, harga = ?, stok = ?, satuan = ?, jenis_barang = ?, tanggal_kadaluarsa = ? WHERE produk_id = ?');
    $stmt->bind_param('sdisssi', $nama, $harga, $stok, $satuan, $jenisBarang, $tanggalKadaluarsa, $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    flash('success', 'Data produk berhasil diperbarui.');
    redirect('/produk/index.php');
}

$stmt = $conn->prepare('SELECT * FROM produk WHERE produk_id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

if (!$product) {
    flash('error', 'Data produk tidak ditemukan.');
    redirect('/produk/index.php');
}

$pageTitle = 'Edit Produk - SALESPRO';
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<body>
    <div class="app-shell">
        <?php include __DIR__ . '/../includes/sidebar.php'; ?>
        <main class="main-content">
            <?php include __DIR__ . '/../includes/topbar.php'; ?>
            <div class="page-content">
                <div class="page-header">
                    <h2>Edit Produk</h2>
                    <a href="index.php" class="link-btn primary">Kembali</a>
                </div>

                <div class="form-card">
                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nama Produk</label>
                                <input type="text" name="nama_produk" value="<?php echo htmlspecialchars($product['nama_produk']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Harga</label>
                                <input type="number" name="harga" min="0" step="1000" value="<?php echo htmlspecialchars($product['harga']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Stok</label>
                                <input type="number" name="stok" min="0" value="<?php echo htmlspecialchars($product['stok']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Satuan</label>
                                <select name="satuan">
                                    <option value="pcs" <?php echo ($product['satuan'] ?? 'pcs') === 'pcs' ? 'selected' : ''; ?>>pcs</option>
                                    <option value="kg" <?php echo ($product['satuan'] ?? 'pcs') === 'kg' ? 'selected' : ''; ?>>kg</option>
                                    <option value="lusin" <?php echo ($product['satuan'] ?? 'pcs') === 'lusin' ? 'selected' : ''; ?>>lusin</option>
                                    <option value="box" <?php echo ($product['satuan'] ?? 'pcs') === 'box' ? 'selected' : ''; ?>>box</option>
                                    <option value="liter" <?php echo ($product['satuan'] ?? 'pcs') === 'liter' ? 'selected' : ''; ?>>liter</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Jenis Barang</label>
                                <select name="jenis_barang">
                                    <option value="umum" <?php echo ($product['jenis_barang'] ?? 'umum') === 'umum' ? 'selected' : ''; ?>>Umum</option>
                                    <option value="elektronik" <?php echo ($product['jenis_barang'] ?? 'umum') === 'elektronik' ? 'selected' : ''; ?>>Elektronik</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Tanggal Kadaluarsa</label>
                                <input type="date" name="tanggal_kadaluarsa" value="<?php echo htmlspecialchars($product['tanggal_kadaluarsa'] ?? ''); ?>">
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
