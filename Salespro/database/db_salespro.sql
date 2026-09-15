CREATE DATABASE IF NOT EXISTS db_salespro;
USE db_salespro;

CREATE TABLE IF NOT EXISTS pengguna (
  pengguna_id INT AUTO_INCREMENT PRIMARY KEY,
  nama_pengguna VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  peran ENUM('administrator','petugas') NOT NULL
);

CREATE TABLE IF NOT EXISTS pelanggan (
  pelanggan_id INT AUTO_INCREMENT PRIMARY KEY,
  nama_pelanggan VARCHAR(100) NOT NULL,
  alamat TEXT,
  telepon VARCHAR(20),
  jenis ENUM('umum','member') NOT NULL DEFAULT 'umum'
);

CREATE TABLE IF NOT EXISTS produk (
  produk_id INT AUTO_INCREMENT PRIMARY KEY,
  nama_produk VARCHAR(100) NOT NULL,
  harga DECIMAL(12,2) NOT NULL DEFAULT 0,
  stok INT NOT NULL DEFAULT 0,
  satuan VARCHAR(20) NOT NULL DEFAULT 'pcs',
  jenis_barang ENUM('umum','elektronik') NOT NULL DEFAULT 'umum',
  tanggal_kadaluarsa DATE NULL
);

CREATE TABLE IF NOT EXISTS transaksi (
  transaksi_id INT AUTO_INCREMENT PRIMARY KEY,
  tanggal DATETIME NOT NULL,
  pelanggan_id INT NULL,
  pengguna_id INT NOT NULL,
  subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
  diskon DECIMAL(12,2) NOT NULL DEFAULT 0,
  total_harga DECIMAL(12,2) NOT NULL DEFAULT 0,
  uang_pembeli DECIMAL(12,2) NOT NULL DEFAULT 0,
  kembalian DECIMAL(12,2) NOT NULL DEFAULT 0,
  FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(pelanggan_id) ON DELETE SET NULL,
  FOREIGN KEY (pengguna_id) REFERENCES pengguna(pengguna_id) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS detail_transaksi (
  detail_id INT AUTO_INCREMENT PRIMARY KEY,
  transaksi_id INT NOT NULL,
  produk_id INT NOT NULL,
  jumlah INT NOT NULL,
  harga DECIMAL(12,2) NOT NULL,
  subtotal DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (transaksi_id) REFERENCES transaksi(transaksi_id) ON DELETE CASCADE,
  FOREIGN KEY (produk_id) REFERENCES produk(produk_id) ON DELETE RESTRICT
);

INSERT INTO pengguna (nama_pengguna, email, password, peran) VALUES
('Administrator', 'admin@salespro.com', 'admin123', 'administrator'),
('Petugas', 'kasir@salespro.com', 'kasir123', 'petugas')
ON DUPLICATE KEY UPDATE
  nama_pengguna = VALUES(nama_pengguna),
  password = VALUES(password),
  peran = VALUES(peran);

INSERT INTO pelanggan (nama_pelanggan, alamat, telepon, jenis) VALUES
('Pelanggan Umum', 'Umum', '-', 'umum'),
('Budi Santoso', 'Jl. Merdeka 1', '081234567890', 'member'),
('Siti Aminah', 'Jl. Sudirman 10', '082345678901', 'member');

INSERT INTO produk (nama_produk, harga, stok, satuan, jenis_barang, tanggal_kadaluarsa) VALUES
('Keyboard Mechanical', 450000, 15, 'pcs', 'umum', '2027-05-20'),
('Mouse Wireless', 180000, 25, 'pcs', 'elektronik', NULL),
('Monitor 24 Inch', 1450000, 8, 'pcs', 'elektronik', NULL),
('Laptop 14 Inch', 6500000, 5, 'pcs', 'elektronik', NULL),
('Printer Deskjet', 900000, 10, 'pcs', 'elektronik', NULL),
('Beras 5kg', 65000, 30, 'kg', 'umum', '2026-12-15');
