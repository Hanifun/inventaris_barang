<?php
/**
 * ============================================
 * FILE: barang.php
 * ============================================
 * Halaman Data Barang - READ Operation (CRUD)
 * Menampilkan semua data barang dalam tabel interaktif
 * 
 * SKKNI Mapping:
 * - J.620100.004.01: Mengimplementasikan User Interface
 * - J.620100.025.02: Mengakses Data dari Basis Data (SELECT)
 * - J.620100.023.01: Menggunakan Library Pre-Existing (DataTables)
 * - J.620100.016.01: Menerapkan Perintah Eksekusi (foreach)
 * ============================================
 */

// Load dependencies
require_once 'config.php';
require_once 'koneksi.php';
require_once 'functions.php';

// Cek login
cek_login();

// Set page title
$page_title = 'Data Barang';

// ============================================
// QUERY SEMUA DATA BARANG
// ============================================
// SELECT semua kolom dari tabel barang, urutkan berdasarkan nama
$query = "SELECT * FROM barang ORDER BY nama_barang ASC";
$result = $koneksi->query($query);

// Include header
include 'includes/header.php';
?>

<!-- ======================================== -->
<!-- BARANG CONTENT -->
<!-- ======================================== -->

<div class="row mb-4">
    <div class="col-md-8">
        <h2><i class="bi bi-box-seam"></i> Data Barang</h2>
        <p class="text-muted">Kelola data barang inventaris gudang</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="tambah_barang.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Barang Baru
        </a>
    </div>
</div>

<!-- ======================================== -->
<!-- TABLE DATA BARANG -->
<!-- ======================================== -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <!-- DataTable akan otomatis membuat search, sorting, dan pagination -->
            <table id="tableBarang" class="table table-striped table-hover">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Stok</th>
                        <th>Satuan</th>
                        <th>Harga</th>
                        <th>Total Nilai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Cek apakah ada data
                    if ($result->num_rows > 0):
                        $no = 1;
                        // Loop untuk menampilkan setiap baris data
                        while ($row = $result->fetch_assoc()):
                            // Hitung total nilai (stok × harga)
                            $total_nilai = $row['stok'] * $row['harga'];
                            
                            // Tentukan warna badge stok
                            if ($row['stok'] == 0) {
                                $badge_class = 'bg-danger';
                            } elseif ($row['stok'] < 10) {
                                $badge_class = 'bg-warning text-dark';
                            } else {
                                $badge_class = 'bg-success';
                            }
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><code><?php echo $row['kode_barang']; ?></code></td>
                        <td>
                            <strong><?php echo $row['nama_barang']; ?></strong>
                            <?php if ($row['deskripsi']): ?>
                                <br><small class="text-muted"><?php echo substr($row['deskripsi'], 0, 50); ?>...</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge <?php echo $badge_class; ?>">
                                <?php echo $row['stok']; ?>
                            </span>
                        </td>
                        <td><?php echo $row['satuan']; ?></td>
                        <td><?php echo format_rupiah($row['harga']); ?></td>
                        <td><?php echo format_rupiah($total_nilai); ?></td>
                        <td>
                            <!-- Tombol Edit -->
                            <a href="edit_barang.php?id=<?php echo $row['id']; ?>" 
                               class="btn btn-sm btn-warning" 
                               title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            
                            <!-- Tombol Hapus dengan konfirmasi JavaScript -->
                            <button onclick="hapusBarang(<?php echo $row['id']; ?>, '<?php echo addslashes($row['nama_barang']); ?>')"
                                    class="btn btn-sm btn-danger"
                                    title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada data barang. 
                            <a href="tambah_barang.php">Tambah barang pertama</a>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
// Set inline script untuk konfirmasi hapus dengan SweetAlert2
$inline_script = "
/**
 * Fungsi untuk konfirmasi hapus barang
 * Menggunakan SweetAlert2 untuk dialog konfirmasi yang cantik
 * 
 * @param int id ID barang yang akan dihapus
 * @param string nama Nama barang (untuk ditampilkan di konfirmasi)
 */
function hapusBarang(id, nama) {
    // Tampilkan dialog konfirmasi
    Swal.fire({
        title: 'Apakah Anda yakin?',
        html: 'Barang <strong>' + nama + '</strong> akan dihapus permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        // Jika user klik 'Ya, Hapus!'
        if (result.isConfirmed) {
            // Redirect ke halaman hapus_barang.php dengan parameter id
            window.location.href = 'hapus_barang.php?id=' + id;
        }
    });
}
";

include 'includes/footer.php'; 
?>
