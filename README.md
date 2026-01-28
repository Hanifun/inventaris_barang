# 📦 Sistem Inventaris Gudang - antigravity client

> Aplikasi web sederhana untuk mengelola inventaris barang gudang dengan PHP Native, MySQL, dan Bootstrap

[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange.svg)](https://www.mysql.com/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple.svg)](https://getbootstrap.com/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

## 📋 Deskripsi

Sistem Inventaris Gudang adalah aplikasi web untuk mengelola data barang dalam gudang. Aplikasi ini dibuat menggunakan **PHP Native** tanpa framework, dengan database **MySQL/MariaDB**, styling **Bootstrap 5**, dan validasi **JavaScript**.

Project ini dibuat untuk memenuhi kompetensi **SKKNI Level 3: Junior Web Developer**.

---

## ✨ Fitur Utama

### 🔐 Sistem Autentikasi
- Login dengan username dan password
- Password ter-enkripsi dengan **bcrypt**
- Session management dengan auto-logout
- Validasi form di client-side (JavaScript) dan server-side (PHP)

### 📊 Manajemen Barang
- **Create**: Tambah barang baru dengan validasi
- **Read**: Lihat daftar semua barang dengan tabel yang rapi
- **Update**: Edit data barang yang sudah ada
- **Delete**: Hapus barang dengan konfirmasi

### 🔍 Fitur Tambahan
- Search/Filter barang
- Validasi input (tidak boleh kosong, stok tidak boleh minus, format harga dll)
- Responsive design (mobile-friendly)
- Alert & notifikasi user-friendly dengan SweetAlert2
- Tracking transaksi barang masuk/keluar

---

## 🛠️ Teknologi yang Digunakan

| Teknologi | Versi | Fungsi |
|-----------|-------|--------|
| PHP | 7.4+ | Backend logic & server-side processing |
| MySQL/MariaDB | 5.7+ | Database management |
| Bootstrap | 5.3 | UI Framework & responsive design |
| JavaScript | ES6+ | Client-side validation & interactivity |
| SweetAlert2 | Latest | Modern alert & notification |
| Bootstrap Icons | 1.11+ | Icon library |

---

## 📁 Struktur Project

```
sistem-inventaris-gudang/
├── database/
│   └── database.sql          # SQL schema & sample data
├── assets/
│   ├── css/
│   │   └── style.css         # Custom CSS
│   └── js/
│       └── script.js         # Custom JavaScript
├── config.php                # Konfigurasi database
├── koneksi.php              # File koneksi database
├── functions.php            # Helper functions
├── index.php                # Halaman login
├── dashboard.php            # Dashboard utama
├── barang_list.php          # Daftar barang (CRUD)
├── barang_add.php           # Tambah barang
├── barang_edit.php          # Edit barang
├── barang_delete.php        # Hapus barang
├── transaksi_list.php       # Daftar transaksi
├── logout.php               # Logout handler
├── .gitignore               # Git ignore file
└── README.md                # Dokumentasi project
```

---

## 🚀 Cara Instalasi

### 1️⃣ Requirements

Pastikan sistem Anda sudah terinstall:
- **XAMPP** / **WAMP** / **LAMP** / **MAMP** (PHP 7.4+ & MySQL 5.7+)
- **Web Browser** (Chrome, Firefox, Edge, dll)
- **Git** (untuk clone repository)

### 2️⃣ Clone Repository

```bash
git clone https://github.com/USERNAME/sistem-inventaris-gudang.git
cd sistem-inventaris-gudang
```

> **Note:** Ganti `USERNAME` dengan username GitHub Anda

### 3️⃣ Setup Database

1. Buka **phpMyAdmin** (`http://localhost/phpmyadmin`)
2. Buat database baru bernama `inventaris_gudang`
3. Import file `database/database.sql`:
   - Klik tab **Import**
   - Pilih file `database/database.sql`
   - Klik **Go**

**Atau via Command Line:**

```bash
mysql -u root -p < database/database.sql
```

### 4️⃣ Konfigurasi Database

Edit file `config.php` sesuai dengan konfigurasi database Anda:

```php
define('DB_HOST', 'localhost');     // Host database
define('DB_USER', 'root');          // Username database
define('DB_PASS', '');              // Password database (kosongkan jika tidak ada)
define('DB_NAME', 'inventaris_gudang');  // Nama database
```

### 5️⃣ Jalankan Aplikasi

1. Pindahkan folder project ke directory web server:
   - **XAMPP**: `C:\xampp\htdocs\sistem-inventaris-gudang`
   - **WAMP**: `C:\wamp\www\sistem-inventaris-gudang`
   - **LAMP**: `/var/www/html/sistem-inventaris-gudang`

2. Start **Apache** dan **MySQL** dari XAMPP/WAMP Control Panel

3. Buka browser dan akses:
   ```
   http://localhost/sistem-inventaris-gudang
   ```

4. Login dengan kredensial default:
   - **Username**: `admin`
   - **Password**: `admin123`

---

## 👤 Default Login

Setelah import database, gunakan kredensial berikut untuk login:

| Username | Password | Role |
|----------|----------|------|
| admin | admin123 | Administrator |

> ⚠️ **PENTING**: Ganti password default setelah login pertama kali untuk keamanan!

---

## 📸 Screenshot

### Dashboard
![Dashboard](docs/screenshots/dashboard.png)

### Daftar Barang
![Daftar Barang](docs/screenshots/barang-list.png)

### Form Tambah Barang
![Form Tambah](docs/screenshots/barang-add.png)

> **Note**: Screenshot akan ditambahkan setelah aplikasi selesai

---

## 🎯 SKKNI Competency Mapping

Project ini memenuhi kompetensi berikut:

| Kode | Kompetensi | Implementasi |
|------|-----------|--------------|
| J.620100.004.01 | Mengimplementasikan User Interface | Bootstrap 5, responsive design |
| J.620100.016.01 | Menerapkan Perintah Eksekusi | If-else, loop, function, session |
| J.620100.017.02 | Menggunakan Pengelolaan Basis Data | MySQL CRUD operations |
| J.620100.018.02 | Menerapkan Bahasa Pemrograman | PHP Native |
| J.620100.025.02 | Mengakses Basis Data | Prepared statements, mysqli |

---

## 🔒 Fitur Keamanan

- ✅ Password encryption dengan **bcrypt** (`password_hash()`)
- ✅ SQL Injection prevention dengan **prepared statements**
- ✅ XSS prevention dengan **htmlspecialchars()**
- ✅ Session management dengan timeout otomatis
- ✅ Input validation di client-side dan server-side
- ✅ CSRF protection (bisa ditambahkan)

---

## 📝 Validasi Input

### Client-side (JavaScript)
- Field tidak boleh kosong
- Format input sesuai (angka, teks, dll)
- Panjang karakter minimum/maksimum
- Konfirmasi sebelum delete

### Server-side (PHP)
- Sanitasi input dengan `mysqli_real_escape_string()` dan `htmlspecialchars()`
- Validasi tipe data
- Validasi business logic (stok tidak boleh minus, dll)
- Error handling yang proper

---

## 🤝 Kontribusi

Kontribusi sangat diterima! Jika Anda ingin berkontribusi:

1. Fork repository ini
2. Buat branch baru (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

---

## 📄 License

Project ini menggunakan lisensi **MIT License**. Silakan gunakan, modifikasi, dan distribusikan sesuai kebutuhan Anda.

---

## 👨‍💻 Author

**Your Name**
- GitHub: [@yourusername](https://github.com/yourusername)
- Email: your.email@example.com

---

## 🙏 Acknowledgments

- Bootstrap Team untuk UI framework yang luar biasa
- SweetAlert2 untuk notification library
- SKKNI untuk standar kompetensi

---

## 📞 Support

Jika ada pertanyaan atau issue, silakan:
- Buka [GitHub Issues](https://github.com/Hanifun/inventaris_barang/issues)
- Email: dakhilullah0@gmail.com

---

<div align="center">
  <p>Dibuat dengan ❤️ untuk memenuhi SKKNI Level 3: Junior Web Developer</p>
  <p>© 2026 - Sistem Inventaris Gudang</p>
</div>
