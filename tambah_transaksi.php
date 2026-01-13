<?php
/**
 * ============================================
 * FILE: tambah_transaksi.php
 * ============================================
 * Halaman Tambah Transaksi - Input barang masuk/keluar
 * Otomatis update stok barang sesuai jenis transaksi
 * 
 * SKKNI Mapping:
 * - J.620100.004.01: Mengimplementasikan User Interface
 * - J.620100.025.02: Mengakses Data dari Basis Data (INSERT, UPDATE)
 * - J.620100.009.01: Mengimplementasikan Algoritma (Logika stok)
 * ============================================
 */

// Load dependencies
require_once 'config.php';
require_once 'koneksi.php';
require_once 'functions.php';

// Cek login
cek_login();

// Set page title
$page_title = 'Tambah Transaksi';

// Variable untuk error dan success
$error = '';
$success = '';

// ============================================
// PROSES FORM SUBMIT
// ============================================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $barang_id = (int) $_POST['barang_id'];
    $jenis = sanitize_input($_POST['jenis']);
    $jumlah = (int) $_POST['jumlah'];
    $keterangan = sanitize_input($_POST['keterangan']);
    $user_id = $_SESSION['user_id'];
    
    // ============================================
    // VALIDASI INPUT
    // ============================================
    if (empty($barang_id) || empty($jenis) || $jumlah <= 0) {
        $error = 'Semua field harus diisi dengan benar!';
    }
    elseif (!in_array($jenis, ['masuk', 'keluar'])) {
        $error = 'Jenis transaksi tidak valid!';
    }
    else {
        // Ambil data barang untuk cek stok
        $query_barang = "SELECT nama_barang, stok FROM barang WHERE id = ?";
        $stmt = $koneksi->prepare($query_barang);
        $stmt->bind_param("i", $barang_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {
            $error = 'Barang tidak ditemukan!';
        } else {
            $barang = $result->fetch_assoc();
            $stok_sekarang = $barang['stok'];
            
            // ============================================
            // LOGIKA ALGORITMA: Hitung stok baru
            // ============================================
            if ($jenis == 'masuk') {
                // Barang masuk: TAMBAH stok
                $stok_baru = $stok_sekarang + $jumlah;
            } else {
                // Barang keluar: KURANGI stok
                $stok_baru = $stok_sekarang - $jumlah;
                
                // VALIDASI PENTING: Stok tidak boleh minus!
                if ($stok_baru < 0) {
                    $error = 'Stok tidak mencukupi! Stok tersedia: ' . $stok_sekarang;
                }
            }
            
            // Jika tidak ada error, lakukan transaksi
            if (empty($error)) {
                // Mulai transaction untuk konsistensi data
                $koneksi->begin_transaction();
                
                try {
                    // 1. Insert transaksi
                    $insert_transaksi = "INSERT INTO transaksi (barang_id, jenis, jumlah, keterangan, user_id) 
                                        VALUES (?, ?, ?, ?, ?)";
                    $stmt_transaksi = $koneksi->prepare($insert_transaksi);
                    $stmt_transaksi->bind_param("isisi", $barang_id, $jenis, $jumlah, $keterangan, $user_id);
                    $stmt_transaksi->execute();
                    
                    // 2. Update stok barang
                    $update_stok = "UPDATE barang SET stok = ? WHERE id = ?";
                    $stmt_update = $koneksi->prepare($update_stok);
                    $stmt_update->bind_param("ii", $stok_baru, $barang_id);
                    $stmt_update->execute();
                    
                    // Commit transaction
                    $koneksi->commit();
                    
                    // Redirect dengan pesan sukses
                    redirect('transaksi.php', 'Transaksi berhasil! Stok barang telah diupdate.', 'success');
                    
                } catch (Exception $e) {
                    // Rollback jika ada error
                    $koneksi->rollback();
                    $error = 'Gagal menyimpan transaksi: ' . $e->getMessage();
                }
            }
        }
    }
}

// ============================================
// QUERY DAFTAR BARANG UNTUK DROPDOWN
// ============================================
$query_barang_list = "SELECT id, kode_barang, nama_barang, stok, satuan FROM barang ORDER BY nama_barang ASC";
$result_barang = $koneksi->query($query_barang_list);

// Include header
include 'includes/header.php';
?>

<!-- ======================================== -->
<!-- TAMBAH TRANSAKSI CONTENT -->
<!-- ======================================== -->

<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="bi bi-plus-circle"></i> Tambah Transaksi</h2>
        <p class="text-muted">Input transaksi barang masuk atau keluar</p>
    </div>
</div>

<!-- Tampilkan error jika ada -->
<?php if ($error): ?>
    <?php echo alert($error, 'danger'); ?>
<?php endif; ?>

<!-- ======================================== -->
<!-- FORM TRANSAKSI -->
<!-- ======================================== -->
<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="" id="formTransaksi">
                    <!-- Pilih Barang -->
                    <div class="mb-3">
                        <label for="barang_id" class="form-label">
                            Pilih Barang <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="barang_id" name="barang_id" required>
                            <option value="">-- Pilih Barang --</option>
                            <?php while ($row = $result_barang->fetch_assoc()): ?>
                                <option value="<?php echo $row['id']; ?>" 
                                        data-stok="<?php echo $row['stok']; ?>"
                                        data-satuan="<?php echo $row['satuan']; ?>">
                                    <?php echo $row['kode_barang']; ?> - <?php echo $row['nama_barang']; ?> 
                                    (Stok: <?php echo $row['stok']; ?> <?php echo $row['satuan']; ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <!-- Jenis Transaksi -->
                    <div class="mb-3">
                        <label for="jenis" class="form-label">
                            Jenis Transaksi <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="jenis" name="jenis" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="masuk">Barang Masuk (Pembelian/Penambahan Stok)</option>
                            <option value="keluar">Barang Keluar (Penjualan/Pengurangan Stok)</option>
                        </select>
                    </div>
                    
                    <!-- Jumlah -->
                    <div class="mb-3">
                        <label for="jumlah" class="form-label">
                            Jumlah <span class="text-danger">*</span>
                        </label>
                        <input type="number" 
                               class="form-control" 
                               id="jumlah" 
                               name="jumlah" 
                               min="1"
                               placeholder="0"
                               required>
                        <small class="text-muted" id="infoStok"></small>
                    </div>
                    
                    <!-- Keterangan -->
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                        <textarea class="form-control" 
                                  id="keterangan" 
                                  name="keterangan" 
                                  rows="3"
                                  placeholder="Catatan atau keterangan transaksi"></textarea>
                    </div>
                    
                    <!-- Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="transaksi.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i> Simpan Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Info Panel -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-light">
            <div class="card-body">
                <h5><i class="bi bi-info-circle"></i> Panduan</h5>
                <hr>
                <p><strong>Barang Masuk:</strong></p>
                <ul>
                    <li>Stok akan BERTAMBAH</li>
                    <li>Untuk pembelian atau restok</li>
                </ul>
                <p><strong>Barang Keluar:</strong></p>
                <ul>
                    <li>Stok akan BERKURANG</li>
                    <li>Untuk penjualan atau penggunaan</li>
                    <li>Tidak bisa jika stok tidak cukup</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
// JavaScript untuk real-time validation
$inline_script = "
// Tampilkan info stok saat barang dipilih
document.getElementById('barang_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const stok = selectedOption.getAttribute('data-stok');
    const satuan = selectedOption.getAttribute('data-satuan');
    const infoStok = document.getElementById('infoStok');
    
    if (stok) {
        infoStok.innerHTML = 'Stok tersedia: <strong>' + stok + ' ' + satuan + '</strong>';
    } else {
        infoStok.innerHTML = '';
    }
});

// Validasi sebelum submit
document.getElementById('formTransaksi').addEventListener('submit', function(e) {
    const barangSelect = document.getElementById('barang_id');
    const selectedOption = barangSelect.options[barangSelect.selectedIndex];
    const stok = parseInt(selectedOption.getAttribute('data-stok'));
    const jenis = document.getElementById('jenis').value;
    const jumlah = parseInt(document.getElementById('jumlah').value);
    
    // Jika barang keluar, cek stok
    if (jenis === 'keluar') {
        if (jumlah > stok) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Stok Tidak Cukup!',
                text: 'Stok tersedia hanya ' + stok + '. Tidak dapat mengeluarkan ' + jumlah + ' item.'
            });
            return false;
        }
    }
    
    // Validasi jumlah harus lebih dari 0
    if (jumlah <= 0) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Jumlah Tidak Valid',
            text: 'Jumlah harus lebih dari 0!'
        });
        return false;
    }
});
";

include 'includes/footer.php';
?>
