<?php
/**
 * ============================================
 * FILE: logout.php
 * ============================================
 * Halaman Logout - Menghapus session dan redirect ke login
 * 
 * SKKNI Mapping:
 * - J.620100.016.01: Menerapkan Perintah Eksekusi (session management)
 * ============================================
 */

// Start session
session_start();

// ============================================
// HAPUS SEMUA SESSION
// ============================================
// Unset semua variabel session
session_unset();

// Hancurkan session
session_destroy();

// ============================================
// REDIRECT KE LOGIN
// ============================================
header("Location: index.php");
exit();
?>
