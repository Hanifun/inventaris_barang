<?php
/**
 * ============================================
 * FILE: edit_barang.php
 * ============================================
 * Halaman Edit Barang - UPDATE Operation (CRUD)
 * Form untuk mengubah data barang yang sudah ada
 * 
 * SKKNI Mapping:
 * - J.620100.004.01: Mengimplementasikan User Interface (Form)
 * - J.620100.025.02: Mengakses Data dari Basis Data (UPDATE, SELECT)
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
$page_title = 'Edit Barang';

// ============================================
// CEK PARAMETER ID
// ============================================
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('barang.php', 'ID barang tidak valid!', 'danger');
}

$id = (int) $_GET['id'];

// ============================================
// AMBIL DATA BARANG BERDASARKAN ID
// ============================================
$query = "SELECT * FROM barang WHERE id = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Cek apakah barang ditemukan
if ($result->num_rows == 0) {
    redirect('barang.php', 'Barang tidak ditemukan!', 'danger');
}

$barang = $result->fetch_assoc();

// Variable untuk error dan success
$error = '';
$success = '';

// ============================================
// PROSES FORM SUBMIT (UPDATE)
// ============================================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil dan sanitize input
    $kode_barang = sanitize_input($_POST['kode_barang']);
    $nama_barang = sanitize_input($_POST['nama_barang']);
    $stok = (int) $_POST['stok'];
    $harga = (float) $_POST['harga'];
    $satuan = sanitize_input($_POST['satuan']);
    $deskripsi = sanitize_input($_POST['deskripsi']);
    
    // ============================================
    // VALIDASI INPUT
    // ============================================
    if (empty($kode_barang) || empty($nama_barang) || empty($satuan)) {
        $error = 'Kode barang, nama barang, dan satuan harus diisi!';
    }
    elseif ($stok < 0) {
        $error = 'Stok tidak boleh bernilai negatif!';
    }
    elseif ($harga < 0) {
        $error = 'Harga tidak boleh bernilai negatif!';
    }
    else {
        // Cek duplikasi kode barang (kecuali untuk barang ini sendiri)
        $check_query = "SELECT id FROM barang WHERE kode_barang = ? AND id != ?";
        $stmt_check = $koneksi->prepare($check_query);
        $stmt_check->bind_param("si", $kode_barang, $id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows > 0) {
            $error = 'Kode barang sudah digunakan oleh barang lain!';
        } else {
            // ============================================
            // UPDATE DATA KE DATABASE
            // ============================================
            $update_query = "UPDATE barang 
                            SET kode_barang = ?, 
                                nama_barang = ?, 
                                stok = ?, 
                                harga = ?, 
                                satuan = ?, 
                                deskripsi = ?
                            WHERE id = ?";
            
            $stmt_update = $koneksi->prepare($update_query);
            $stmt_update->bind_param("ssiissi", $kode_barang, $nama_barang, $stok, $harga, $satuan, $deskripsi, $id);
            
            if ($stmt_update->execute()) {
                // Update data barang di variable agar form menampilkan data terbaru
                $barang = [
                    'kode_barang' => $kode_barang,
                    'nama_barang' => $nama_barang,
                    'stok' => $stok,
                    'harga' => $harga,
                    'satuan' => $satuan,
                    'deskripsi' => $deskripsi
                ];
                
                $success = 'Data barang berhasil diupdate!';
            } else {
                $error = 'Gagal mengupdate data: ' . $koneksi->error;
            }
        }
    }
}

// Include header
include 'includes/header.php';
?>

<!-- ======================================== -->
<!-- EDIT BARANG CONTENT -->
<!-- ======================================== -->

<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="bi bi-pencil"></i> Edit Barang</h2>
        <p class="text-muted">Ubah data barang di formulir di bawah</p>
    </div>
</div>

<!-- Tampilkan pesan -->
<?php if ($error): ?>
    <?php echo alert($error, 'danger'); ?>
<?php endif; ?>

<?php if ($success): ?>
    <?php echo alert($success, 'success'); ?>
<?php endif; ?>

<!-- ======================================== -->
<!-- FORM EDIT BARANG -->
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
                               value="<?php echo $barang['kode_barang']; ?>"
                               required>
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
                               value="<?php echo $barang['nama_barang']; ?>"
                               required>
                    </div>
                    
                    <!-- Row untuk Stok, Harga, Satuan -->
                    <div class="row">
                        <!-- Stok -->
                        <div class="col-md-4 mb-3">
                            <label for="stok" class="form-label">
                                Stok <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control" 
                                   id="stok" 
                                   name="stok" 
                                   min="0"
                                   value="<?php echo $barang['stok']; ?>"
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
                                   value="<?php echo $barang['harga']; ?>"
                                   required>
                        </div>
                        
                        <!-- Satuan -->
                        <div class="col-md-4 mb-3">
                            <label for="satuan" class="form-label">
                                Satuan <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="satuan" name="satuan" required>
                                <option value="">Pilih Satuan</option>
                                <option value="pcs" <?php echo ($barang['satuan'] == 'pcs') ? 'selected' : ''; ?>>Pcs (Pieces)</option>
                                <option value="unit" <?php echo ($barang['satuan'] == 'unit') ? 'selected' : ''; ?>>Unit</option>
                                <option value="box" <?php echo ($barang['satuan'] == 'box') ? 'selected' : ''; ?>>Box</option>
                                <option value="kg" <?php echo ($barang['satuan'] == 'kg') ? 'selected' : ''; ?>>Kg (Kilogram)</option>
                                <option value="liter" <?php echo ($barang['satuan'] == 'liter') ? 'selected' : ''; ?>>Liter</option>
                                <option value="meter" <?php echo ($barang['satuan'] == 'meter') ? 'selected' : ''; ?>>Meter</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Deskripsi -->
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi (Opsional)</label>
                        <textarea class="form-control" 
                                  id="deskripsi" 
                                  name="deskripsi" 
                                  rows="3"><?php echo $barang['deskripsi']; ?></textarea>
                    </div>
                    
                    <!-- Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="barang.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update Barang
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
                <p><strong>ID Barang:</strong> #<?php echo $id; ?></p>
                <p><strong>Dibuat:</strong> <?php echo format_tanggal($barang['created_at']); ?></p>
                <p><strong>Update Terakhir:</strong> <?php echo format_tanggal($barang['updated_at']); ?></p>
                <hr>
                <p class="text-muted small">
                    <i class="bi bi-exclamation-circle"></i> 
                    Perubahan stok akan langsung mempengaruhi laporan inventaris.
                </p>
            </div>
        </div>
    </div>
</div>

<?php
// JavaScript validasi
$inline_script = "
document.getElementById('formBarang').addEventListener('submit', function(e) {
    const stok = parseInt(document.getElementById('stok').value);
    const harga = parseFloat(document.getElementById('harga').value);
    
    if (stok < 0) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Stok Tidak Valid',
            text: 'Stok tidak boleh bernilai negatif!'
        });
        return false;
    }
    
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
