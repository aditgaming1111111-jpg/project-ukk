<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['administrator']);

$pageTitle = 'Tambah Pengguna - SALESPRO';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_pengguna'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $peran = $_POST['peran'] ?? 'petugas';

    if ($nama === '' || $email === '' || $password === '') {
        flash('error', 'Nama, email, dan password wajib diisi.');
        redirect('/pengguna/tambah.php');
    }

    $conn = dbConnect();
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare('INSERT INTO pengguna (nama_pengguna, email, password, peran) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('ssss', $nama, $email, $hashed, $peran);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    flash('success', 'Data pengguna berhasil ditambahkan.');
    redirect('/pengguna/index.php');
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
                    <h2>Tambah Pengguna</h2>
                    <a href="index.php" class="link-btn primary">Kembali</a>
                </div>

                <div class="form-card">
                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nama</label>
                                <input type="text" name="nama_pengguna" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" name="password" required>
                            </div>
                            <div class="form-group">
                                <label>Peran</label>
                                <select name="peran">
                                    <option value="administrator">Administrator</option>
                                    <option value="petugas">Petugas</option>
                                </select>
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
