<?php
/**
 * ============================================
 * FILE: tambah_barang.php
 * ============================================
 * Halaman Tambah Barang - CREATE Operation (CRUD)
 * Form untuk menambahkan barang baru ke database
 * 
 * SKKNI Mapping:
 * - J.620100.004.01: Mengimplementasikan User Interface (Form)
 * - J.620100.025.02: Mengakses Data dari Basis Data (INSERT)
 * - J.620100.009.01: Mengimplementasikan Algoritma (Validasi)
 * - J.620100.016.01: Menerapkan Perintah Eksekusi (if-else)
 * ============================================
 */

// Load dependencies
require_once 'config.php';
require_once 'koneksi.php';
require_once 'functions.php';

// Cek login
cek_login();

// Set page title
$page_title = 'Tambah Barang';

// Variable untuk menampung error dan success message
$error = '';
$success = '';

// ============================================
// PROSES FORM SUBMIT
// ============================================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil dan sanitize input dari form
    $kode_barang = sanitize_input($_POST['kode_barang']);
    $nama_barang = sanitize_input($_POST['nama_barang']);
    $stok = (int) $_POST['stok']; // Cast ke integer
    $harga = (float) $_POST['harga']; // Cast ke float
    $satuan = sanitize_input($_POST['satuan']);
    $deskripsi = sanitize_input($_POST['deskripsi']);
    
    // ============================================
    // VALIDASI INPUT
    // ============================================
    
    // Cek field yang wajib diisi
    if (empty($kode_barang) || empty($nama_barang) || empty($satuan)) {
        $error = 'Kode barang, nama barang, dan satuan harus diisi!';
    }
    // Validasi: Stok tidak boleh negatif
    elseif ($stok < 0) {
        $error = 'Stok tidak boleh bernilai negatif!';
    }
    // Validasi: Harga tidak boleh negatif
    elseif ($harga < 0) {
        $error = 'Harga tidak boleh bernilai negatif!';
    }
    // Cek apakah kode barang sudah ada (duplikasi)
    else {
        $check_query = "SELECT id FROM barang WHERE kode_barang = ?";
        $stmt = $koneksi->prepare($check_query);
        $stmt->bind_param("s", $kode_barang);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'Kode barang sudah digunakan! Gunakan kode lain.';
        } else {
            // ============================================
            // INSERT DATA KE DATABASE
            // ============================================
            // Gunakan prepared statement untuk keamanan
            $insert_query = "INSERT INTO barang (kode_barang, nama_barang, stok, harga, satuan, deskripsi) 
                            VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = $koneksi->prepare($insert_query);
            $stmt->bind_param("ssiiss", $kode_barang, $nama_barang, $stok, $harga, $satuan, $deskripsi);
            
            // Eksekusi query
            if ($stmt->execute()) {
                // Berhasil! Redirect ke halaman barang dengan pesan sukses
                redirect('barang.php', 'Barang berhasil ditambahkan!', 'success');
            } else {
                $error = 'Gagal menambahkan barang: ' . $koneksi->error;
            }
        }
    }
}

// Generate kode barang otomatis untuk placeholder
$suggested_code = generate_kode_barang();

// Include header
include 'includes/header.php';
?>

<!-- ======================================== -->
<!-- TAMBAH BARANG CONTENT -->
<!-- ======================================== -->

<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="bi bi-plus-circle"></i> Tambah Barang Baru</h2>
        <p class="text-muted">Isi formulir di bawah untuk menambahkan barang baru</p>
    </div>
</div>

<!-- Tampilkan error jika ada -->
<?php if ($error): ?>
    <?php echo alert($error, 'danger'); ?>
<?php endif; ?>

<!-- ======================================== -->
<!-- FORM TAMBAH BARANG -->
<!-- ======================================== -->
<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="" id="formBarang">
                    <!-- Kode Barang -->
                    <div class="mb-3">
                        <label for="kode_barang" class="form-label">
                            Kode Barang <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="kode_barang" 
                               name="kode_barang" 
                               placeholder="<?php echo $suggested_code; ?>"
                               value="<?php echo isset($_POST['kode_barang']) ? $_POST['kode_barang'] : ''; ?>"
                               required>
                        <small class="text-muted">Kode unik untuk barang (contoh: BRG001)</small>
                    </div>
                    
                    <!-- Nama Barang -->
                    <div class="mb-3">
                        <label for="nama_barang" class="form-label">
                            Nama Barang <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="nama_barang" 
                               name="nama_barang" 
                               placeholder="Masukkan nama barang"
                               value="<?php echo isset($_POST['nama_barang']) ? $_POST['nama_barang'] : ''; ?>"
                               required>
                    </div>
                    
                    <!-- Row untuk Stok, Harga, Satuan -->
                    <div class="row">
                        <!-- Stok -->
                        <div class="col-md-4 mb-3">
                            <label for="stok" class="form-label">
                                Stok Awal <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control" 
                                   id="stok" 
                                   name="stok" 
                                   min="0"
                                   value="<?php echo isset($_POST['stok']) ? $_POST['stok'] : '0'; ?>"
                                   required>
                        </div>
                        
                        <!-- Harga -->
                        <div class="col-md-4 mb-3">
                            <label for="harga" class="form-label">
                                Harga (Rp) <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control" 
                                   id="harga" 
                                   name="harga" 
                                   min="0"
                                   step="0.01"
                                   value="<?php echo isset($_POST['harga']) ? $_POST['harga'] : '0'; ?>"
                                   required>
                        </div>
                        
                        <!-- Satuan -->
                        <div class="col-md-4 mb-3">
                            <label for="satuan" class="form-label">
                                Satuan <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="satuan" name="satuan" required>
                                <option value="">Pilih Satuan</option>
                                <option value="pcs" <?php echo (isset($_POST['satuan']) && $_POST['satuan'] == 'pcs') ? 'selected' : ''; ?>>Pcs (Pieces)</option>
                                <option value="unit" <?php echo (isset($_POST['satuan']) && $_POST['satuan'] == 'unit') ? 'selected' : ''; ?>>Unit</option>
                                <option value="box" <?php echo (isset($_POST['satuan']) && $_POST['satuan'] == 'box') ? 'selected' : ''; ?>>Box</option>
                                <option value="kg" <?php echo (isset($_POST['satuan']) && $_POST['satuan'] == 'kg') ? 'selected' : ''; ?>>Kg (Kilogram)</option>
                                <option value="liter" <?php echo (isset($_POST['satuan']) && $_POST['satuan'] == 'liter') ? 'selected' : ''; ?>>Liter</option>
                                <option value="meter" <?php echo (isset($_POST['satuan']) && $_POST['satuan'] == 'meter') ? 'selected' : ''; ?>>Meter</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Deskripsi -->
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi (Opsional)</label>
                        <textarea class="form-control" 
                                  id="deskripsi" 
                                  name="deskripsi" 
                                  rows="3"
                                  placeholder="Keterangan atau deskripsi barang"><?php echo isset($_POST['deskripsi']) ? $_POST['deskripsi'] : ''; ?></textarea>
                    </div>
                    
                    <!-- Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="barang.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Barang
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
                <h5><i class="bi bi-info-circle"></i> Informasi</h5>
                <hr>
                <p><strong>Field Wajib:</strong></p>
                <ul>
                    <li>Kode Barang</li>
                    <li>Nama Barang</li>
                    <li>Satuan</li>
                </ul>
                <p><strong>Validasi:</strong></p>
                <ul>
                    <li>Stok tidak boleh minus</li>
                    <li>Harga tidak boleh minus</li>
                    <li>Kode barang harus unik</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
// JavaScript untuk validasi form
$inline_script = "
// Validasi form sebelum submit
document.getElementById('formBarang').addEventListener('submit', function(e) {
    const stok = parseInt(document.getElementById('stok').value);
    const harga = parseFloat(document.getElementById('harga').value);
    const kode = document.getElementById('kode_barang').value.trim();
    const nama = document.getElementById('nama_barang').value.trim();
    
    // Validasi: Field tidak boleh kosong
    if (kode === '' || nama === '') {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Kode barang dan nama barang harus diisi!'
        });
        return false;
    }
    
    // Validasi: Stok tidak boleh negatif
    if (stok < 0) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Stok Tidak Valid',
            text: 'Stok tidak boleh bernilai negatif!'
        });
        return false;
    }
    
    // Validasi: Harga tidak boleh negatif
    if (harga < 0) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Harga Tidak Valid',
            text: 'Harga tidak boleh bernilai negatif!'
        });
        return false;
    }
});
";

include 'includes/footer.php';
?>
