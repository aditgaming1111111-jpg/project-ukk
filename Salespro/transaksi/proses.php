<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$action = $_GET['action'] ?? 'add';

if ($action === 'remove' && !empty($_GET['index'])) {
    $cart = $_SESSION['cart'] ?? [];
    unset($cart[(int) $_GET['index']]);
    $_SESSION['cart'] = array_values($cart);
    redirect('/transaksi/index.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/transaksi/index.php');
}

if ($action === 'add') {
    $produkId = (int) ($_POST['produk_id'] ?? 0);
    $jumlah = (int) ($_POST['jumlah'] ?? 0);
    $pelangganId = isset($_POST['pelanggan_id']) && $_POST['pelanggan_id'] !== '' ? (int) $_POST['pelanggan_id'] : null;

    if ($pelangganId) {
        $_SESSION['selected_customer_id'] = $pelangganId;
    } else {
        unset($_SESSION['selected_customer_id']);
    }

    if ($produkId <= 0 || $jumlah <= 0) {
        flash('error', 'Produk dan jumlah wajib dipilih.');
        redirect('/transaksi/index.php');
    }

    $conn = dbConnect();
    $stmt = $conn->prepare('SELECT * FROM produk WHERE produk_id = ? LIMIT 1');
    $stmt->bind_param('i', $produkId);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $conn->close();

    if (!$product) {
        flash('error', 'Produk tidak ditemukan.');
        redirect('/transaksi/index.php');
    }

    if ($product['stok'] < $jumlah) {
        flash('error', 'Stok tidak mencukupi untuk produk ' . $product['nama_produk'] . '.');
        redirect('/transaksi/index.php');
    }

    $cart = $_SESSION['cart'] ?? [];
    $found = false;
    foreach ($cart as $index => $item) {
        if ((int) $item['produk_id'] === $produkId) {
            $cart[$index]['jumlah'] += $jumlah;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $cart[] = [
            'produk_id' => $product['produk_id'],
            'nama_produk' => $product['nama_produk'],
            'harga' => (float) $product['harga'],
            'satuan' => $product['satuan'],
            'jumlah' => $jumlah,
        ];
    }

    $_SESSION['cart'] = $cart;
    flash('success', 'Produk berhasil ditambahkan ke keranjang.');
    redirect('/transaksi/index.php');
}

if ($action === 'save') {
    $cart = $_SESSION['cart'] ?? [];
    if (empty($cart)) {
        flash('error', 'Keranjang transaksi masih kosong.');
        redirect('/transaksi/index.php');
    }

    $pelangganId = isset($_POST['pelanggan_id']) && $_POST['pelanggan_id'] !== '' ? (int) $_POST['pelanggan_id'] : ($_SESSION['selected_customer_id'] ?? null);
    $uangPembeli = (float) ($_POST['uang_pembeli'] ?? 0);

    $subtotal = 0;
    foreach ($cart as $item) {
        $subtotal += ($item['harga'] ?? 0) * ($item['jumlah'] ?? 0);
    }

    $customerDiscount = 0;
    if ($pelangganId) {
        $discountInfo = getCustomerDiscountInfo($pelangganId);
        $customerDiscount = $subtotal * ($discountInfo['percent'] / 100);
    }

    $total = $subtotal - $customerDiscount;
    $kembalian = $uangPembeli - $total;

    if ($uangPembeli < $total) {
        flash('error', 'Uang pembeli kurang dari total transaksi.');
        redirect('/transaksi/index.php');
    }

    $conn = dbConnect();
    $conn->begin_transaction();

    try {
        $userId = $_SESSION['user_id'];
        $stmt = $conn->prepare('INSERT INTO transaksi (tanggal, pelanggan_id, pengguna_id, subtotal, diskon, total_harga, uang_pembeli, kembalian) VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('iiddddd', $pelangganId, $userId, $subtotal, $customerDiscount, $total, $uangPembeli, $kembalian);
        $stmt->execute();
        $transaksiId = $conn->insert_id;
        $stmt->close();

        foreach ($cart as $item) {
            $produkId = (int) $item['produk_id'];
            $jumlah = (int) $item['jumlah'];
            $harga = (float) $item['harga'];
            $sub = $harga * $jumlah;

            $detailStmt = $conn->prepare('INSERT INTO detail_transaksi (transaksi_id, produk_id, jumlah, harga, subtotal) VALUES (?, ?, ?, ?, ?)');
            $detailStmt->bind_param('iiidd', $transaksiId, $produkId, $jumlah, $harga, $sub);
            $detailStmt->execute();
            $detailStmt->close();

            $updateStmt = $conn->prepare('UPDATE produk SET stok = stok - ? WHERE produk_id = ?');
            $updateStmt->bind_param('ii', $jumlah, $produkId);
            $updateStmt->execute();
            $updateStmt->close();
        }

        $conn->commit();
        unset($_SESSION['cart']);
        unset($_SESSION['selected_customer_id']);
        logActivity('Transaksi berhasil', 'Transaksi', 'Menyimpan transaksi ID ' . $transaksiId . ' dengan total Rp ' . number_format($total, 0, ',', '.') . ' oleh user ' . ($_SESSION['user_name'] ?? 'system'), $_SESSION['user_id'] ?? null, 'success');
        flash('success', 'Transaksi berhasil disimpan.');
        redirect('/transaksi/struk.php?id=' . $transaksiId);
    } catch (Exception $e) {
        $conn->rollback();
        flash('error', $e->getMessage());
        redirect('/transaksi/index.php');
    }
}

redirect('/transaksi/index.php');
