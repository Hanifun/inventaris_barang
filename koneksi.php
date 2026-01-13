<?php
/**
 * ============================================
 * FILE: koneksi.php
 * ============================================
 * File untuk koneksi ke database MySQL
 * Menggunakan MySQLi extension
 * 
 * SKKNI Mapping:
 * - J.620100.025.02: Mengakses Data dari Basis Data (SQL)
 * - J.620100.019.01: Menyusun File/Sumber Daya Pemrograman
 * ============================================
 */

// Load konfigurasi database
require_once 'config.php';

/**
 * Membuat koneksi ke database MySQL
 * Menggunakan MySQLi dengan Object-Oriented style
 */
$koneksi = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// ============================================
// CEK KONEKSI DATABASE
// ============================================
/**
 * Jika koneksi gagal, tampilkan error dan hentikan script
 * connect_error akan berisi pesan error jika koneksi gagal
 */
if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

// ============================================
// SET CHARACTER SET
// ============================================
/**
 * Set charset ke UTF-8 untuk mendukung karakter Indonesia
 * Mencegah masalah encoding pada data yang disimpan
 */
$koneksi->set_charset("utf8mb4");

/**
 * Fungsi helper untuk menjalankan query
 * Mempermudah eksekusi query dan penanganan error
 * 
 * @param string $query SQL query yang akan dijalankan
 * @return mysqli_result|bool Result dari query atau false jika gagal
 */
function query($query) {
    global $koneksi;
    $result = $koneksi->query($query);
    
    if (!$result) {
        // Log error untuk debugging
        error_log("Query Error: " . $koneksi->error);
    }
    
    return $result;
}

/**
 * Fungsi helper untuk escape string (mencegah SQL Injection)
 * PENTING: Gunakan ini setiap kali menerima input dari user
 * 
 * @param string $string String yang akan di-escape
 * @return string String yang sudah aman dari SQL injection
 */
function escape_string($string) {
    global $koneksi;
    return $koneksi->real_escape_string($string);
}

/**
 * Fungsi helper untuk prepared statement (lebih aman dari SQL Injection)
 * Recommended untuk query yang menggunakan data dari user
 * 
 * @param string $query Query dengan placeholder (?)
 * @param string $types Tipe data parameter (s=string, i=integer, d=double)
 * @param array $params Array parameter yang akan di-bind
 * @return mysqli_result|bool Result dari query
 */
function prepared_query($query, $types, $params) {
    global $koneksi;
    
    // Prepare statement
    $stmt = $koneksi->prepare($query);
    
    if (!$stmt) {
        error_log("Prepare Error: " . $koneksi->error);
        return false;
    }
    
    // Bind parameters
    $stmt->bind_param($types, ...$params);
    
    // Execute
    $stmt->execute();
    
    // Return result
    return $stmt->get_result();
}

// Koneksi berhasil - siap digunakan!
?>
