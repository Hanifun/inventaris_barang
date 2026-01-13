/**
 * ============================================
 * FILE: assets/js/script.js
 * ============================================
 * Custom JavaScript untuk interaksi dan validasi
 * 
 * SKKNI Mapping:
 * - J.620100.023.01: Menggunakan Library Pre-Existing (jQuery, DataTables, SweetAlert2)
 * ============================================
 */

// ============================================
// DOCUMENT READY
// ============================================
$(document).ready(function() {
    
    // ========================================
    // INITIALIZE DATATABLES
    // ========================================
    /**
     * Inisialisasi DataTables untuk semua tabel dengan id yang dimulai dengan 'table'
     * DataTables menambahkan fitur:
     * - Search/Filter
     * - Sorting (klik header kolom)
     * - Pagination
     */
    if ($('#tableBarang').length) {
        $('#tableBarang').DataTable({
            language: {
                // Translasi ke Bahasa Indonesia
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data",
                infoFiltered: "(difilter dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                },
                zeroRecords: "Data tidak ditemukan"
            },
            pageLength: 10, // Tampilkan 10 data per halaman
            order: [[2, 'asc']], // Urutkan berdasarkan kolom nama barang (kolom ke-2)
            responsive: true
        });
    }
    
    if ($('#tableTransaksi').length) {
        $('#tableTransaksi').DataTable({
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data",
                infoFiltered: "(difilter dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                },
                zeroRecords: "Data tidak ditemukan"
            },
            pageLength: 10,
            order: [[1, 'desc']], // Urutkan berdasarkan tanggal (descending = terbaru dulu)
            responsive: true
        });
    }
    
    // ========================================
    // FORM VALIDATION - Number Only Input
    // ========================================
    /**
     * Memastikan input stok dan harga hanya menerima angka
     * Mencegah input karakter selain angka
     */
    $('input[type="number"]').on('keypress', function(e) {
        // Izinkan: backspace, delete, tab, escape, enter
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13]) !== -1 ||
            // Izinkan: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
            (e.keyCode === 65 && e.ctrlKey === true) ||
            (e.keyCode === 67 && e.ctrlKey === true) ||
            (e.keyCode === 86 && e.ctrlKey === true) ||
            (e.keyCode === 88 && e.ctrlKey === true) ||
            // Izinkan: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
            return;
        }
        
        // Pastikan hanya angka (0-9) dan titik desimal
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && 
            (e.keyCode < 96 || e.keyCode > 105) && 
            e.keyCode !== 190 && e.keyCode !== 110) {
            e.preventDefault();
        }
    });
    
    // ========================================
    // AUTO-DISMISS ALERTS
    // ========================================
    /**
     * Otomatis hilangkan alert setelah 5 detik
     */
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
    // ========================================
    // TOOLTIP INITIALIZATION (Bootstrap 5)
    // ========================================
    /**
     * Aktifkan tooltip untuk semua element dengan data-bs-toggle="tooltip"
     */
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // ========================================
    // UPPERCASE KODE BARANG
    // ========================================
    /**
     * Otomatis uppercase untuk input kode barang
     */
    $('#kode_barang').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });
    
});

// ============================================
// FUNGSI GLOBAL
// ============================================

/**
 * Format angka ke format Rupiah
 * @param {number} angka - Angka yang akan diformat
 * @returns {string} Format: Rp 1.000.000
 */
function formatRupiah(angka) {
    var number_string = angka.toString().replace(/[^,\d]/g, ''),
        split = number_string.split(','),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);
    
    if (ribuan) {
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    
    rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
    return 'Rp ' + rupiah;
}

/**
 * Konfirmasi hapus dengan SweetAlert2
 * Fungsi ini dipanggil dari tombol hapus di halaman barang.php
 * 
 * @param {number} id - ID barang yang akan dihapus
 * @param {string} nama - Nama barang
 */
function hapusBarang(id, nama) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        html: 'Barang <strong>' + nama + '</strong> akan dihapus permanen!<br>Semua transaksi terkait juga akan terhapus.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bi bi-trash"></i> Ya, Hapus!',
        cancelButtonText: '<i class="bi bi-x-circle"></i> Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirect ke halaman hapus
            window.location.href = 'hapus_barang.php?id=' + id;
        }
    });
}

/**
 * Validasi form sebelum submit
 * Mencegah submit form jika ada field yang tidak valid
 */
function validateForm(formId) {
    var form = document.getElementById(formId);
    
    if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
        
        Swal.fire({
            icon: 'error',
            title: 'Form Tidak Lengkap',
            text: 'Mohon isi semua field yang wajib diisi!',
            confirmButtonText: 'OK'
        });
        
        return false;
    }
    
    form.classList.add('was-validated');
    return true;
}

// ============================================
// CONSOLE INFO
// ============================================
console.log('%c Sistem Inventaris Gudang ', 'background: #0d6efd; color: white; font-size: 16px; font-weight: bold; padding: 10px;');
console.log('%c SKKNI Junior Web Developer - Level 3 ', 'background: #198754; color: white; font-size: 12px; padding: 5px;');
console.log('Sistem siap digunakan! ✓');
