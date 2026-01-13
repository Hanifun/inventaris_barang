<?php
/**
 * ============================================
 * FILE: config.php
 * ============================================
 * File konfigurasi utama aplikasi
 * Berisi konstanta untuk database dan pengaturan aplikasi
 * 
 * SKKNI Mapping:
 * - J.620100.019.01: Menyusun File/Sumber Daya Pemrograman
 * ============================================
 */

// ============================================
// KONFIGURASI DATABASE
// ============================================
// Sesuaikan dengan environment lokal Anda
define('DB_HOST', 'localhost');      // Host database (biasanya localhost)
define('DB_USER', 'root');           // Username database
define('DB_PASS', '');               // Password database (kosong untuk XAMPP default)
define('DB_NAME', 'inventaris_gudang'); // Nama database

// ============================================
// KONFIGURASI APLIKASI
// ============================================
define('APP_NAME', 'Sistem Inventaris Gudang');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost:8000'); // Sesuaikan dengan URL lokal Anda

// ============================================
// KONFIGURASI SESSION
// ============================================
// Nama session untuk keamanan
define('SESSION_NAME', 'inventaris_session');

// Waktu timeout session (dalam detik)
// 3600 = 1 jam
define('SESSION_TIMEOUT', 3600);

// ============================================
// TIMEZONE
// ============================================
// Set timezone untuk Indonesia
date_default_timezone_set('Asia/Jakarta');

// ============================================
// ERROR REPORTING
// ============================================
// Untuk development, tampilkan semua error
// Untuk production, set ke 0
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Fungsi untuk mendapatkan base URL aplikasi
 * 
 * @return string Base URL
 */
function base_url($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}
?>
