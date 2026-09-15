<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['reset_cart'])) {
    unset($_SESSION['cart']);
    redirect('/transaksi/index.php');
}

$pageTitle = 'Transaksi Baru - SALESPRO';
$conn = dbConnect();
$customers = $conn->query('SELECT * FROM pelanggan ORDER BY nama_pelanggan ASC');
$products = $conn->query('SELECT * FROM produk WHERE stok > 0 ORDER BY nama_produk ASC');
$success = flash('success');
$error = flash('error');

$selectedCustomerId = $_SESSION['selected_customer_id'] ?? null;
if (!empty($_POST['pelanggan_id']) && is_numeric($_POST['pelanggan_id'])) {
    $selectedCustomerId = (int) $_POST['pelanggan_id'];
    $_SESSION['selected_customer_id'] = $selectedCustomerId;
}

$cart = $_SESSION['cart'] ?? [];
$subtotal = 0;
foreach ($cart as $item) {
    $subtotal += ($item['harga'] ?? 0) * ($item['jumlah'] ?? 0);
}

$discountPercent = 0;
if (!empty($selectedCustomerId) && is_numeric($selectedCustomerId)) {
    $customerId = (int) $selectedCustomerId;
    $discountInfo = getCustomerDiscountInfo($customerId);
    $discountPercent = $discountInfo['percent'];
}

$discount = $subtotal * ($discountPercent / 100);
$total = $subtotal - $discount;

$customerList = [];
while ($customer = $customers->fetch_assoc()) {
    $customerList[] = $customer;
}
$productsList = [];
while ($product = $products->fetch_assoc()) {
    $productsList[] = $product;
}
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
                    <h2>Transaksi Baru</h2>
                    <a href="?reset_cart=1" class="link-btn danger">Reset Keranjang</a>
                </div>

                <div class="form-card">
                    <form method="POST" action="proses.php?action=add">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Pelanggan</label>
                                <select name="pelanggan_id" id="pelangganSelect">
                                    <option value="">Pelanggan Umum</option>
                                    <?php foreach ($customerList as $customer): ?>
                                        <option value="<?php echo $customer['pelanggan_id']; ?>" <?php echo ((string) ($selectedCustomerId ?? '') === (string) $customer['pelanggan_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($customer['nama_pelanggan']); ?> (<?php echo ucfirst($customer['jenis'] ?? 'umum'); ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Produk</label>
                                <select name="produk_id" id="produkSelect">
                                    <option value="">Pilih Produk</option>
                                    <?php foreach ($productsList as $product): ?>
                                        <option value="<?php echo $product['produk_id']; ?>" data-harga="<?php echo $product['harga']; ?>" data-stok="<?php echo $product['stok']; ?>" data-satuan="<?php echo htmlspecialchars($product['satuan']); ?>"><?php echo htmlspecialchars($product['nama_produk']); ?> (<?php echo htmlspecialchars($product['satuan']); ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Harga</label>
                                <input type="text" id="hargaProduk" readonly>
                            </div>
                            <div class="form-group">
                                <label>Stok</label>
                                <input type="text" id="stokProduk" readonly>
                            </div>
                            <div class="form-group">
                                <label>Jumlah</label>
                                <input type="number" name="jumlah" min="1" value="1">
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-success">+ Tambah Produk</button>
                        </div>
                    </form>
                </div>

                <div class="panel" style="margin-top: 20px;">
                    <div class="panel-header">
                        <h3>Detail Transaksi</h3>
                    </div>
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Produk</th>
                                    <th>Satuan</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($cart)): ?>
                                    <?php foreach ($cart as $index => $item): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($item['nama_produk']); ?></td>
                                            <td><?php echo htmlspecialchars($item['satuan'] ?? 'pcs'); ?></td>
                                            <td><?php echo formatRupiah($item['harga']); ?></td>
                                            <td><?php echo htmlspecialchars($item['jumlah']); ?></td>
                                            <td><?php echo formatRupiah($item['harga'] * $item['jumlah']); ?></td>
                                            <td><a href="proses.php?action=remove&index=<?php echo $index; ?>" class="link-btn danger">Hapus</a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Belum ada produk ditambahkan.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="summary-box">
                        <div class="totals-row"><span>Subtotal</span><strong><?php echo formatRupiah($subtotal); ?></strong></div>
                        <div class="totals-row"><span>Diskon</span><strong><?php echo formatRupiah($discount); ?></strong></div>
                        <div class="totals-row"><span>Total</span><strong class="summary-total"><?php echo formatRupiah($total); ?></strong></div>
                    </div>

                    <?php if (!empty($cart)): ?>
                        <form method="POST" action="proses.php?action=save" class="form-card" style="margin-top: 20px;">
                            <input type="hidden" name="pelanggan_id" value="<?php echo htmlspecialchars((string) ($selectedCustomerId ?? '')); ?>">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Uang Pembeli</label>
                                    <input type="number" id="uangPembeli" name="uang_pembeli" min="0" step="1000" required>
                                </div>
                                <div class="form-group">
                                    <label>Jenis Pelanggan</label>
                                    <input type="text" value="<?php echo !empty($selectedCustomerId) ? 'Member' : 'Umum'; ?>" readonly>
                                </div>
                            </div>

                            <div class="payment-status-box">
                                <div class="totals-row"><span>Total Belanja</span><strong><?php echo formatRupiah($total); ?></strong></div>
                                <div class="totals-row"><span>Kembalian</span><strong id="kembalianValue">Rp 0</strong></div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">SIMPAN TRANSAKSI</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    <script src="<?php echo BASE_URL; ?>/assets/js/script.js"></script>
    <script>
        const select = document.getElementById('produkSelect');
        const harga = document.getElementById('hargaProduk');
        const stok = document.getElementById('stokProduk');
        const cashInput = document.getElementById('uangPembeli');
        const kembalianValue = document.getElementById('kembalianValue');
        const totalBelanja = Number(<?php echo (float) $total; ?>);

        const formatCurrency = (value) => new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        }).format(value);

        function updateKembalian() {
            if (!cashInput || !kembalianValue) return;

            const cash = Number(cashInput.value || 0);
            const change = cash - totalBelanja;

            if (change >= 0) {
                kembalianValue.textContent = formatCurrency(change);
                kembalianValue.style.color = '#22a06b';
                return;
            }

            kembalianValue.textContent = 'Kurang ' + formatCurrency(Math.abs(change));
            kembalianValue.style.color = '#dc3545';
        }

        if (select) {
            select.addEventListener('change', function () {
                const option = this.options[this.selectedIndex];
                if (!option || !option.value) {
                    harga.value = '';
                    stok.value = '';
                    return;
                }
                harga.value = formatCurrency(Number(option.dataset.harga));
                stok.value = option.dataset.stok + ' ' + option.dataset.satuan;
            });
        }

        if (cashInput) {
            cashInput.addEventListener('input', updateKembalian);
        }
    </script>
</body>
</html>
