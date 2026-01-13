<?php
/**
 * ============================================
 * FILE: transaksi.php
 * ============================================
 * Halaman Transaksi - Menampilkan history transaksi barang masuk/keluar
 * Fitur bonus untuk nilai tambah
 * 
 * SKKNI Mapping:
 * - J.620100.004.01: Mengimplementasikan User Interface
 * - J.620100.025.02: Mengakses Data dari Basis Data (JOIN query)
 * - J.620100.023.01: Menggunakan Library Pre-Existing
 * ============================================
 */

// Load dependencies
require_once 'config.php';
require_once 'koneksi.php';
require_once 'functions.php';

// Cek login
cek_login();

// Set page title
$page_title = 'Riwayat Transaksi';

// ============================================
// QUERY TRANSAKSI DENGAN JOIN
// ============================================
// Gabungkan tabel transaksi, barang, dan users
$query = "SELECT t.*, b.kode_barang, b.nama_barang, b.satuan, u.nama_lengkap
          FROM transaksi t
          JOIN barang b ON t.barang_id = b.id
          JOIN users u ON t.user_id = u.id
          ORDER BY t.tanggal DESC";
$result = $koneksi->query($query);

// Include header
include 'includes/header.php';
?>

<!-- ======================================== -->
<!-- TRANSAKSI CONTENT -->
<!-- ======================================== -->

<div class="row mb-4">
    <div class="col-md-8">
        <h2><i class="bi bi-arrow-left-right"></i> Riwayat Transaksi</h2>
        <p class="text-muted">History pergerakan barang masuk dan keluar gudang</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="tambah_transaksi.php" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Tambah Transaksi
        </a>
    </div>
</div>

<!-- ======================================== -->
<!-- TABLE TRANSAKSI -->
<!-- ======================================== -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table id="tableTransaksi" class="table table-striped table-hover">
                <thead class="table-success">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                        <th>User</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($result->num_rows > 0):
                        $no = 1;
                        while ($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo format_tanggal($row['tanggal']); ?></td>
                        <td><code><?php echo $row['kode_barang']; ?></code></td>
                        <td><?php echo $row['nama_barang']; ?></td>
                        <td>
                            <?php if ($row['jenis'] == 'masuk'): ?>
                                <span class="badge bg-success">
                                    <i class="bi bi-arrow-down-circle"></i> Masuk
                                </span>
                            <?php else: ?>
                                <span class="badge bg-danger">
                                    <i class="bi bi-arrow-up-circle"></i> Keluar
                                </span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo $row['jumlah']; ?></strong> <?php echo $row['satuan']; ?></td>
                        <td><?php echo $row['keterangan'] ? $row['keterangan'] : '-'; ?></td>
                        <td><?php echo $row['nama_lengkap']; ?></td>
                    </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada transaksi.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
