<?php
/**
 * ============================================
 * FILE: dashboard.php
 * ============================================
 * Halaman Dashboard - Tampilan utama setelah login
 * Menampilkan ringkasan data dan quick stats
 * 
 * SKKNI Mapping:
 * - J.620100.004.01: Mengimplementasikan User Interface
 * - J.620100.025.02: Mengakses Data dari Basis Data (Query SELECT)
 * - J.620100.016.01: Menerapkan Perintah Eksekusi (foreach loop)
 * ============================================
 */

// Load dependencies
require_once 'config.php';
require_once 'koneksi.php';
require_once 'functions.php';

// Cek login
cek_login();

// Set page title
$page_title = 'Dashboard';

// ============================================
// QUERY DATA UNTUK STATISTICS
// ============================================

// Hitung total jenis barang
$query_total_barang = "SELECT COUNT(*) as total FROM barang";
$result_total_barang = $koneksi->query($query_total_barang);
$total_barang = $result_total_barang->fetch_assoc()['total'];

// Hitung total stok keseluruhan
$query_total_stok = "SELECT SUM(stok) as total FROM barang";
$result_total_stok = $koneksi->query($query_total_stok);
$total_stok = $result_total_stok->fetch_assoc()['total'] ?? 0;

// Hitung total nilai inventaris (stok × harga)
$query_total_nilai = "SELECT SUM(stok * harga) as total FROM barang";
$result_total_nilai = $koneksi->query($query_total_nilai);
$total_nilai = $result_total_nilai->fetch_assoc()['total'] ?? 0;

// Hitung barang dengan stok menipis (< 10)
$query_low_stock = "SELECT COUNT(*) as total FROM barang WHERE stok < 10";
$result_low_stock = $koneksi->query($query_low_stock);
$low_stock = $result_low_stock->fetch_assoc()['total'];

// ============================================
// QUERY TRANSAKSI TERBARU
// ============================================
$query_transaksi = "SELECT t.*, b.nama_barang, u.nama_lengkap 
                    FROM transaksi t
                    JOIN barang b ON t.barang_id = b.id
                    JOIN users u ON t.user_id = u.id
                    ORDER BY t.tanggal DESC
                    LIMIT 5";
$result_transaksi = $koneksi->query($query_transaksi);

// ============================================
// QUERY BARANG STOK MENIPIS
// ============================================
$query_barang_menipis = "SELECT * FROM barang WHERE stok < 10 ORDER BY stok ASC LIMIT 5";
$result_barang_menipis = $koneksi->query($query_barang_menipis);

// Include header
include 'includes/header.php';
?>

<!-- ======================================== -->
<!-- DASHBOARD CONTENT -->
<!-- ======================================== -->

<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="bi bi-speedometer2"></i> Dashboard</h2>
        <p class="text-muted">Selamat datang, <strong><?php echo $_SESSION['nama_lengkap']; ?></strong>!</p>
    </div>
</div>

<!-- ======================================== -->
<!-- STATISTICS CARDS -->
<!-- ======================================== -->
<div class="row g-3 mb-4">
    <!-- Card 1: Total Barang -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Jenis Barang</h6>
                        <h3 class="mb-0"><?php echo number_format($total_barang); ?></h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded">
                        <i class="bi bi-box-seam text-primary fs-2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Card 2: Total Stok -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Stok</h6>
                        <h3 class="mb-0"><?php echo number_format($total_stok); ?></h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded">
                        <i class="bi bi-boxes text-success fs-2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Card 3: Total Nilai -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Nilai Inventaris</h6>
                        <h3 class="mb-0 small"><?php echo format_rupiah($total_nilai); ?></h3>
                    </div>
                    <div class="bg-info bg-opacity-10 p-3 rounded">
                        <i class="bi bi-cash-stack text-info fs-2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Card 4: Stok Menipis -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Stok Menipis</h6>
                        <h3 class="mb-0"><?php echo number_format($low_stock); ?></h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded">
                        <i class="bi bi-exclamation-triangle text-warning fs-2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ======================================== -->
<!-- CONTENT ROW -->
<!-- ======================================== -->
<div class="row g-3">
    <!-- Transaksi Terbaru -->
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Transaksi Terbaru</h5>
            </div>
            <div class="card-body">
                <?php if ($result_transaksi->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Barang</th>
                                    <th>Jenis</th>
                                    <th>Jumlah</th>
                                    <th>User</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result_transaksi->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i', strtotime($row['tanggal'])); ?></td>
                                    <td><?php echo $row['nama_barang']; ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $row['jenis'] == 'masuk' ? 'success' : 'danger'; ?>">
                                            <?php echo ucfirst($row['jenis']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $row['jumlah']; ?></td>
                                    <td><?php echo $row['nama_lengkap']; ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-3">Belum ada transaksi</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Barang Stok Menipis -->
    <div class="col-md-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-exclamation-triangle text-warning"></i> Stok Menipis</h5>
            </div>
            <div class="card-body">
                <?php if ($result_barang_menipis->num_rows > 0): ?>
                    <div class="list-group list-group-flush">
                        <?php while ($row = $result_barang_menipis->fetch_assoc()): ?>
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?php echo $row['nama_barang']; ?></strong>
                                    <br>
                                    <small class="text-muted"><?php echo $row['kode_barang']; ?></small>
                                </div>
                                <span class="badge bg-danger fs-6"><?php echo $row['stok']; ?> <?php echo $row['satuan']; ?></span>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-3">Semua stok aman! 👍</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm bg-light">
            <div class="card-body text-center">
                <h5 class="mb-3">Quick Actions</h5>
                <a href="tambah_barang.php" class="btn btn-primary me-2">
                    <i class="bi bi-plus-circle"></i> Tambah Barang
                </a>
                <a href="tambah_transaksi.php" class="btn btn-success me-2">
                    <i class="bi bi-arrow-left-right"></i> Tambah Transaksi
                </a>
                <a href="barang.php" class="btn btn-info text-white">
                    <i class="bi bi-box-seam"></i> Lihat Semua Barang
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
