<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['administrator']);

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    flash('error', 'ID pengguna tidak valid.');
    redirect('/pengguna/index.php');
}

$conn = dbConnect();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_pengguna'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $peran = $_POST['peran'] ?? 'petugas';
    $password = $_POST['password'] ?? '';

    if ($nama === '' || $email === '') {
        flash('error', 'Nama dan email wajib diisi.');
        redirect('/pengguna/edit.php?id=' . $id);
    }

    if ($password !== '') {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('UPDATE pengguna SET nama_pengguna = ?, email = ?, password = ?, peran = ? WHERE pengguna_id = ?');
        $stmt->bind_param('ssssi', $nama, $email, $hashed, $peran, $id);
    } else {
        $stmt = $conn->prepare('UPDATE pengguna SET nama_pengguna = ?, email = ?, peran = ? WHERE pengguna_id = ?');
        $stmt->bind_param('sssi', $nama, $email, $peran, $id);
    }

    $stmt->execute();
    $stmt->close();
    $conn->close();

    logActivity('Edit pengguna', 'Pengguna', 'Mengubah data pengguna ID ' . $id . ' menjadi: ' . $nama . ' (' . $email . ', ' . $peran . ')', $_SESSION['user_id'] ?? null, 'success');
    flash('success', 'Data pengguna berhasil diperbarui.');
    redirect('/pengguna/index.php');
}

$stmt = $conn->prepare('SELECT * FROM pengguna WHERE pengguna_id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

if (!$user) {
    flash('error', 'Data pengguna tidak ditemukan.');
    redirect('/pengguna/index.php');
}

$pageTitle = 'Edit Pengguna - SALESPRO';
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<body>
    <div class="app-shell">
        <?php include __DIR__ . '/../includes/sidebar.php'; ?>
        <main class="main-content">
            <?php include __DIR__ . '/../includes/topbar.php'; ?>
            <div class="page-content">
                <div class="page-header">
                    <h2>Edit Pengguna</h2>
                    <a href="index.php" class="link-btn primary">Kembali</a>
                </div>

                <div class="form-card">
                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nama</label>
                                <input type="text" name="nama_pengguna" value="<?php echo htmlspecialchars($user['nama_pengguna']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Password Baru</label>
                                <input type="password" name="password" placeholder="Kosongkan jika tidak diubah">
                            </div>
                            <div class="form-group">
                                <label>Peran</label>
                                <select name="peran">
                                    <option value="administrator" <?php echo $user['peran'] === 'administrator' ? 'selected' : ''; ?>>Administrator</option>
                                    <option value="petugas" <?php echo $user['peran'] === 'petugas' ? 'selected' : ''; ?>>Petugas</option>
                                </select>
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
