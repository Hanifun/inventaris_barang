<?php
/**
 * ============================================
 * FILE: functions.php
 * ============================================
 * Kumpulan fungsi helper yang digunakan di seluruh aplikasi
 * Memisahkan logika reusable ke dalam fungsi-fungsi terpisah
 * 
 * SKKNI Mapping:
 * - J.620100.019.01: Menyusun Fungsi/File/Sumber Daya Pemrograman
 * - J.620100.016.01: Menerapkan Perintah Eksekusi Bahasa Pemrograman
 * ============================================
 */

/**
 * ============================================
 * FUNGSI: cek_login()
 * ============================================
 * Mengecek apakah user sudah login atau belum
 * Jika belum login, redirect ke halaman login
 * 
 * @return void
 */
function cek_login() {
    // Start session jika belum dimulai
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Cek apakah session user_id ada
    // Jika tidak ada, berarti user belum login
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        // Redirect ke halaman login
        header("Location: index.php");
        exit();
    }
    
    // Cek session timeout (opsional, untuk keamanan lebih)
    if (isset($_SESSION['last_activity'])) {
        $inactive_time = time() - $_SESSION['last_activity'];
        
        // Jika lebih dari SESSION_TIMEOUT detik, logout otomatis
        if ($inactive_time > SESSION_TIMEOUT) {
            session_unset();
            session_destroy();
            header("Location: index.php?timeout=1");
            exit();
        }
    }
    
    // Update last activity time
    $_SESSION['last_activity'] = time();
}

/**
 * ============================================
 * FUNGSI: sanitize_input()
 * ============================================
 * Membersihkan input dari user untuk mencegah XSS Attack
 * Menghapus tag HTML dan karakter berbahaya
 * 
 * KEAMANAN: Mencegah input berbahaya seperti <script>alert('XSS')</script>
 * 
 * @param string $data Input dari user
 * @return string Data yang sudah dibersihkan
 */
function sanitize_input($data) {
    // Trim whitespace dari awal dan akhir
    $data = trim($data);
    
    // Hapus backslashes
    $data = stripslashes($data);
    
    // Convert karakter khusus HTML menjadi HTML entities
    // Contoh: < menjadi &lt;, > menjadi &gt;
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    
    return $data;
}

/**
 * ============================================
 * FUNGSI: format_rupiah()
 * ============================================
 * Format angka menjadi format Rupiah Indonesia
 * 
 * @param int|float $angka Angka yang akan diformat
 * @return string Format: Rp 1.000.000
 */
function format_rupiah($angka) {
    // number_format(angka, desimal, pemisah_desimal, pemisah_ribuan)
    return "Rp " . number_format($angka, 0, ',', '.');
}

/**
 * ============================================
 * FUNGSI: format_tanggal()
 * ============================================
 * Format tanggal ke format Indonesia
 * 
 * @param string $tanggal Tanggal dalam format database (Y-m-d H:i:s)
 * @return string Format: 12 Januari 2026, 10:30
 */
function format_tanggal($tanggal) {
    // Array nama bulan dalam Bahasa Indonesia
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    
    // Pecah tanggal
    $pecah = explode(' ', $tanggal);
    $tanggal_pecah = explode('-', $pecah[0]);
    $waktu = isset($pecah[1]) ? $pecah[1] : '';
    
    // Format: tanggal bulan tahun, jam:menit
    $format = $tanggal_pecah[2] . ' ' . $bulan[(int)$tanggal_pecah[1]] . ' ' . $tanggal_pecah[0];
    
    if ($waktu) {
        $waktu_pecah = explode(':', $waktu);
        $format .= ', ' . $waktu_pecah[0] . ':' . $waktu_pecah[1];
    }
    
    return $format;
}

/**
 * ============================================
 * FUNGSI: alert()
 * ============================================
 * Generate alert Bootstrap untuk menampilkan pesan
 * 
 * @param string $pesan Pesan yang akan ditampilkan
 * @param string $tipe Tipe alert: success, danger, warning, info
 * @return string HTML alert Bootstrap
 */
function alert($pesan, $tipe = 'info') {
    $icon = '';
    switch ($tipe) {
        case 'success':
            $icon = '✓';
            break;
        case 'danger':
            $icon = '✗';
            break;
        case 'warning':
            $icon = '⚠';
            break;
        default:
            $icon = 'ℹ';
    }
    
    return '<div class="alert alert-' . $tipe . ' alert-dismissible fade show" role="alert">
                <strong>' . $icon . '</strong> ' . $pesan . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
}

/**
 * ============================================
 * FUNGSI: redirect()
 * ============================================
 * Redirect ke halaman lain dengan pesan optional
 * 
 * @param string $url URL tujuan
 * @param string $pesan Pesan yang akan ditampilkan (optional)
 * @param string $tipe Tipe pesan: success, error, warning, info
 */
function redirect($url, $pesan = '', $tipe = 'info') {
    if ($pesan) {
        $_SESSION['message'] = $pesan;
        $_SESSION['message_type'] = $tipe;
    }
    header("Location: $url");
    exit();
}

/**
 * ============================================
 * FUNGSI: get_flash_message()
 * ============================================
 * Mengambil dan menghapus flash message dari session
 * Flash message = pesan yang hanya tampil sekali
 * 
 * @return string HTML alert jika ada pesan
 */
function get_flash_message() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $html = '';
    if (isset($_SESSION['message'])) {
        $tipe = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : 'info';
        $html = alert($_SESSION['message'], $tipe);
        
        // Hapus message dari session setelah ditampilkan
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
    
    return $html;
}

/**
 * ============================================
 * FUNGSI: generate_kode_barang()
 * ============================================
 * Generate kode barang otomatis (BRG001, BRG002, dst)
 * 
 * @return string Kode barang baru
 */
function generate_kode_barang() {
    global $koneksi;
    
    // Ambil kode barang terakhir
    $query = "SELECT kode_barang FROM barang ORDER BY id DESC LIMIT 1";
    $result = $koneksi->query($query);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_code = $row['kode_barang'];
        
        // Ekstrak angka dari kode (BRG001 -> 001)
        $number = (int) substr($last_code, 3);
        $new_number = $number + 1;
        
        // Format dengan leading zeros (1 -> 001)
        return 'BRG' . str_pad($new_number, 3, '0', STR_PAD_LEFT);
    } else {
        // Jika belum ada data, mulai dari BRG001
        return 'BRG001';
    }
}

/**
 * ============================================
 * FUNGSI: is_ajax_request()
 * ============================================
 * Cek apakah request berasal dari AJAX
 * 
 * @return bool True jika AJAX request
 */
function is_ajax_request() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
?>
