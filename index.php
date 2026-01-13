<?php
/**
 * ============================================
 * FILE: index.php
 * ============================================
 * Halaman Login - Pintu masuk aplikasi
 * User harus login sebelum mengakses fitur lainnya
 * 
 * SKKNI Mapping:
 * - J.620100.004.01: Mengimplementasikan User Interface
 * - J.620100.016.01: Menerapkan Perintah Eksekusi (if-else, session)
 * - J.620100.025.02: Mengakses Data dari Basis Data
 * ============================================
 */

// Start session untuk menyimpan data login
session_start();

// Load dependencies
require_once 'config.php';
require_once 'koneksi.php';
require_once 'functions.php';

// ============================================
// CEK: Jika sudah login, redirect ke dashboard
// ============================================
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// ============================================
// PROSES LOGIN
// ============================================
$error = '';
$success = '';

// Cek apakah ada notifikasi timeout
if (isset($_GET['timeout']) && $_GET['timeout'] == '1') {
    $error = 'Session Anda telah berakhir. Silakan login kembali.';
}

// Cek apakah form login di-submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password']; // Password tidak perlu di-sanitize karena akan di-hash
    
    // ============================================
    // VALIDASI INPUT
    // ============================================
    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi!';
    } else {
        // ============================================
        // CEK USERNAME DI DATABASE
        // ============================================
        // Gunakan prepared statement untuk keamanan (mencegah SQL Injection)
        $stmt = $koneksi->prepare("SELECT id, username, password, nama_lengkap FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Cek apakah user ditemukan
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // ============================================
            // VERIFIKASI PASSWORD
            // ============================================
            // Gunakan password_verify untuk mengecek password yang ter-hash
            if (password_verify($password, $user['password'])) {
                // Password benar! Set session dan redirect
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['last_activity'] = time();
                
                // Redirect ke dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                $error = 'Password salah!';
            }
        } else {
            $error = 'Username tidak ditemukan!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Inventaris Gudang</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Custom CSS untuk halaman login -->
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }
        
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .login-header i {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        
        .login-body {
            padding: 30px;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px;
            font-weight: 500;
            transition: transform 0.2s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="login-card">
                    <!-- Header -->
                    <div class="login-header">
                        <i class="bi bi-boxes"></i>
                        <h3 class="mb-0">Sistem Inventaris Gudang</h3>
                        <p class="mb-0 mt-2">Silakan login untuk melanjutkan</p>
                    </div>
                    
                    <!-- Body -->
                    <div class="login-body">
                        <!-- Tampilkan error jika ada -->
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Form Login -->
                        <form method="POST" action="" id="loginForm">
                            <!-- Username -->
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="bi bi-person"></i> Username
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="username" 
                                       name="username" 
                                       placeholder="Masukkan username"
                                       required
                                       autofocus>
                            </div>
                            
                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock"></i> Password
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Masukkan password"
                                       required>
                            </div>
                            
                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-login">
                                    <i class="bi bi-box-arrow-in-right"></i> Login
                                </button>
                            </div>
                        </form>
                        
                        <!-- Info default credentials -->
                        <div class="mt-4 p-3 bg-light rounded">
                            <small class="text-muted">
                                <strong>Default Login:</strong><br>
                                Username: <code>admin</code><br>
                                Password: <code>admin123</code>
                            </small>
                        </div>
                    </div>
                </div>
                
                <!-- Footer info -->
                <div class="text-center mt-3 text-white">
                    <small>&copy; <?php echo date('Y'); ?> - Level 3 SKKNI Junior Web Developer</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Simple form validation dengan JavaScript -->
    <script>
        // Validasi form sebelum submit
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            
            // Cek apakah field kosong
            if (username === '' || password === '') {
                e.preventDefault(); // Batalkan submit
                alert('Username dan password harus diisi!');
                return false;
            }
            
            // Cek panjang minimum
            if (username.length < 3) {
                e.preventDefault();
                alert('Username minimal 3 karakter!');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password minimal 6 karakter!');
                return false;
            }
        });
    </script>
</body>
</html>
