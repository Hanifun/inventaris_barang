-- ============================================
-- DATABASE: Sistem Inventaris Barang Gudang
-- ============================================
-- Dibuat untuk memenuhi Level 3: Web Development
-- SKKNI Junior Web Developer
-- ============================================

-- Buat database baru
CREATE DATABASE IF NOT EXISTS inventaris_gudang;
USE inventaris_gudang;

-- ============================================
-- TABEL 1: users
-- ============================================
-- Menyimpan data pengguna yang dapat login ke sistem
-- Password di-hash menggunakan bcrypt untuk keamanan
-- ============================================
CREATE TABLE users (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL COMMENT 'Password ter-hash dengan bcrypt',
    nama_lengkap VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABEL 2: barang
-- ============================================
-- Menyimpan data barang/item dalam inventaris gudang
-- Stok tidak boleh minus (akan divalidasi di aplikasi)
-- ============================================
CREATE TABLE barang (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    kode_barang VARCHAR(20) NOT NULL UNIQUE COMMENT 'Kode unik barang (contoh: BRG001)',
    nama_barang VARCHAR(100) NOT NULL,
    stok INT(11) NOT NULL DEFAULT 0 COMMENT 'Jumlah stok barang',
    harga DECIMAL(15,2) NOT NULL DEFAULT 0 COMMENT 'Harga per satuan dalam Rupiah',
    satuan VARCHAR(20) NOT NULL DEFAULT 'pcs' COMMENT 'Satuan barang (pcs, box, kg, dll)',
    deskripsi TEXT COMMENT 'Deskripsi atau keterangan barang',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_kode (kode_barang),
    INDEX idx_nama (nama_barang)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABEL 3: transaksi (OPSIONAL - Nilai Tambah)
-- ============================================
-- Mencatat setiap transaksi barang masuk/keluar
-- Membantu tracking pergerakan barang
-- ============================================
CREATE TABLE transaksi (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    barang_id INT(11) NOT NULL,
    jenis ENUM('masuk', 'keluar') NOT NULL COMMENT 'Jenis transaksi: masuk atau keluar',
    jumlah INT(11) NOT NULL COMMENT 'Jumlah barang yang masuk/keluar',
    keterangan TEXT COMMENT 'Keterangan transaksi',
    user_id INT(11) NOT NULL COMMENT 'User yang melakukan transaksi',
    tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign key constraints untuk data integrity
    CONSTRAINT fk_transaksi_barang FOREIGN KEY (barang_id) REFERENCES barang(id) ON DELETE CASCADE,
    CONSTRAINT fk_transaksi_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_barang (barang_id),
    INDEX idx_tanggal (tanggal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- DATA SAMPLE untuk TESTING
-- ============================================

-- Insert user admin (password: admin123)
-- Password sudah di-hash menggunakan bcrypt
INSERT INTO users (username, password, nama_lengkap) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator Gudang');
-- Note: Password 'admin123' - untuk testing only

-- Insert sample barang
INSERT INTO barang (kode_barang, nama_barang, stok, harga, satuan, deskripsi) VALUES
('BRG001', 'Laptop ASUS ROG', 15, 15000000.00, 'unit', 'Laptop gaming untuk kebutuhan development'),
('BRG002', 'Mouse Logitech MX Master', 50, 1200000.00, 'unit', 'Mouse wireless untuk produktivitas'),
('BRG003', 'Keyboard Mechanical RGB', 30, 850000.00, 'unit', 'Keyboard mechanical dengan lampu RGB'),
('BRG004', 'Monitor Dell 27 inch', 20, 3500000.00, 'unit', 'Monitor IPS 2K resolution'),
('BRG005', 'Webcam Logitech C920', 25, 1500000.00, 'unit', 'Webcam HD untuk video conference');

-- Insert sample transaksi
INSERT INTO transaksi (barang_id, jenis, jumlah, keterangan, user_id) VALUES
(1, 'masuk', 10, 'Pembelian stok awal', 1),
(2, 'masuk', 50, 'Pembelian stok awal', 1),
(3, 'keluar', 5, 'Penjualan ke customer', 1),
(4, 'masuk', 20, 'Restock dari supplier', 1);

-- ============================================
-- SELESAI
-- ============================================
-- Database siap digunakan!
-- Default Login:
-- Username: admin
-- Password: admin123
-- ============================================
