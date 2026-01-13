<?php
/**
 * ============================================
 * FILE: includes/header.php
 * ============================================
 * Header component yang digunakan di semua halaman
 * Berisi HTML head, CSS libraries, dan navigation bar
 * 
 * SKKNI Mapping:
 * - J.620100.004.01: Mengimplementasikan User Interface
 * - J.620100.023.01: Menggunakan Library Pre-Existing (Bootstrap)
 * - J.620100.019.01: Menyusun File/Sumber Daya Pemrograman
 * ============================================
 */

// Start session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- Judul halaman - dinamis berdasarkan $page_title -->
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Sistem Inventaris Gudang</title>
    
    <!-- ======================================== -->
    <!-- CSS LIBRARIES -->
    <!-- ======================================== -->
    
    <!-- Bootstrap 5 CSS - Framework CSS untuk styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons - Icon pack dari Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- DataTables CSS - Library untuk tabel interaktif -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <!-- SweetAlert2 CSS - Library untuk alert yang cantik -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- ======================================== -->
    <!-- NAVIGATION BAR -->
    <!-- ======================================== -->
    <?php if (isset($_SESSION['user_id'])): ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <!-- Logo / Brand -->
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-boxes"></i>
                <strong>Inventaris Gudang</strong>
            </a>
            
            <!-- Toggle button untuk mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Menu navigasi -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <!-- Menu Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" 
                           href="dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    
                    <!-- Menu Data Barang -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'barang.php' ? 'active' : ''; ?>" 
                           href="barang.php">
                            <i class="bi bi-box-seam"></i> Data Barang
                        </a>
                    </li>
                    
                    <!-- Menu Transaksi -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'transaksi.php' ? 'active' : ''; ?>" 
                           href="transaksi.php">
                            <i class="bi bi-arrow-left-right"></i> Transaksi
                        </a>
                    </li>
                </ul>
                
                <!-- User info dan logout -->
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i>
                            <?php echo isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : 'User'; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item text-danger" href="logout.php">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <?php endif; ?>
    
    <!-- ======================================== -->
    <!-- MAIN CONTENT CONTAINER -->
    <!-- ======================================== -->
    <div class="container-fluid mt-4">
        <?php
        // Tampilkan flash message jika ada
        if (function_exists('get_flash_message')) {
            echo get_flash_message();
        }
        ?>
