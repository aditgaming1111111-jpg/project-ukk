<?php 

    session_start();

    require_once __DIR__ . '/config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>

    <h1>Dashboard</h1>
    <p>Selamat datang, <?php echo $_SESSION['nama_pengguna']; ?>!</p>
    <a href="auth/logout.php">Logout</a>

    <div class="navbar">
        <a href="pengguna/index.php">Pengguna</a>
        <a href="produk/index.php">Produk</a>
        <a href="pelanggan/index.php">Pelanggan</a>
        <a href="transaksi/index.php">Transaksi</a>
    </div>

</body>
</html>