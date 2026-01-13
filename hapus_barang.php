<?php
/**
 * ============================================
 * FILE: hapus_barang.php
 * ============================================
 * Halaman Hapus Barang - DELETE Operation (CRUD)
 * Script backend untuk menghapus barang dari database
 * 
 * SKKNI Mapping:
 * - J.620100.025.02: Mengakses Data dari Basis Data (DELETE)
 * - J.620100.016.01: Menerapkan Perintah Eksekusi (if-else)
 * ============================================
 */

// Load dependencies
require_once 'config.php';
require_once 'koneksi.php';
require_once 'functions.php';

// Cek login
cek_login();

// ============================================
// CEK PARAMETER ID
// ============================================
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('barang.php', 'ID barang tidak valid!', 'danger');
}

$id = (int) $_GET['id'];

// ============================================
// CEK APAKAH BARANG ADA DI DATABASE
// ============================================
$check_query = "SELECT nama_barang FROM barang WHERE id = ?";
$stmt = $koneksi->prepare($check_query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    redirect('barang.php', 'Barang tidak ditemukan!', 'danger');
}

$barang = $result->fetch_assoc();

// ============================================
// HAPUS BARANG DARI DATABASE
// ============================================
// Karena ada foreign key constraint CASCADE, 
// semua transaksi terkait akan terhapus otomatis
$delete_query = "DELETE FROM barang WHERE id = ?";
$stmt_delete = $koneksi->prepare($delete_query);
$stmt_delete->bind_param("i", $id);

if ($stmt_delete->execute()) {
    // Berhasil dihapus
    redirect('barang.php', 'Barang "' . $barang['nama_barang'] . '" berhasil dihapus!', 'success');
} else {
    // Gagal menghapus
    redirect('barang.php', 'Gagal menghapus barang: ' . $koneksi->error, 'danger');
}
?>
