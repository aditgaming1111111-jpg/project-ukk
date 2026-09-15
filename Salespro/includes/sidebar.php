<?php
require_once __DIR__ . '/auth.php';
$user = currentUser();
$role = $user['role'] ?? '';
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="brand">
            <span>SALESPRO</span>
        </div>
    </div>

    <nav class="nav-menu">
        <a href="<?php echo BASE_URL; ?>/dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">
            <span>Dashboard</span>
        </a>

        <?php if ($role === 'administrator') { ?>
            <a href="<?php echo BASE_URL; ?>/produk/index.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/produk/') !== false ? 'active' : ''; ?>">
                <span>Data Produk</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/pelanggan/index.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/pelanggan/') !== false ? 'active' : ''; ?>">
                <span>Data Pelanggan</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/pengguna/index.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/pengguna/') !== false ? 'active' : ''; ?>">
                <span>Pengguna</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/laporan/index.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/laporan/') !== false ? 'active' : ''; ?>">
                <span>Laporan</span>
            </a>
        <?php } ?>

        <a href="<?php echo BASE_URL; ?>/stok/index.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/stok/') !== false ? 'active' : ''; ?>">
            <span>Stok Barang</span>
        </a>

        <a href="<?php echo BASE_URL; ?>/transaksi/index.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/transaksi/') !== false ? 'active' : ''; ?>">
            <span>Transaksi</span>
        </a>

        <a href="<?php echo BASE_URL; ?>/transaksi/riwayat.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/transaksi/riwayat.php') !== false ? 'active' : ''; ?>">
            <span>Riwayat Transaksi</span>
        </a>

        <a href="<?php echo BASE_URL; ?>/auth/logout.php" class="nav-link logout-link">
            <span>Logout</span>
        </a>
    </nav>
</aside>
