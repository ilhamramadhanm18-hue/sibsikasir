<?php
session_start();
include "config/koneksi.php";

if (isset($_POST['register'])) {
    $nama     = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $email    = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = mysqli_real_escape_string($conn, trim($_POST['password']));
    
    // Set default role sebagai 'kasir' untuk akun baru yang mendaftar mandiri
    $role     = 'kasir'; 

    // Cek apakah username sudah pernah digunakan atau belum
    $cek_user = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    
    if (mysqli_num_rows($cek_user) > 0) {
        echo "<script>alert('Gagal! Username sudah terdaftar, silakan gunakan username lain.');</script>";
    } else {
        // Query Simpan Akun Baru ke Database
        $query_reg = mysqli_query($conn, "INSERT INTO users (nama, username, email, password, role) 
                                           VALUES ('$nama', '$username', '$email', '$password', '$role')");
        
        if ($query_reg) {
            echo "<script>
                alert('Pendaftaran Berhasil! Silakan login menggunakan akun Anda.');
                window.location='login.php';
            </script>";
            exit;
        } else {
            echo "<script>alert('Terjadi kesalahan sistem. Gagal mendaftar.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun | SIBSIKASIR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: linear-gradient(135deg, #e0f2f1 0%, #b2dfdb 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px 0;
        }
        .card {
            border: none;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            padding: 30px;
        }
        .form-control {
            border-radius: 12px;
            padding: 12px 18px;
            background: #f8fafc;
            border: 2px solid #edf2f7;
            transition: 0.3s;
        }
        .form-control:focus {
            border-color: #198754;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(25, 135, 84, 0.1);
        }
        .btn-register { 
            border-radius: 12px; 
            padding: 12px; 
            font-weight: 700; 
            background: #198754; 
            color: white;
            transition: 0.3s;
        }
        .btn-register:hover { background: #146c43; transform: translateY(-2px); }
    </style>
</head>
<body>
    
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow-lg p-3">
                <div class="card-body">
                    
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-success m-0">DAFTAR AKUN</h2>
                        <small class="text-muted">Lengkapi data untuk membuat akun kasir</small>
                    </div>

                    <form method="POST" action="" autocomplete="off">
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" placeholder="Masukkan Nama" required autocomplete="off">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Username</label>
                            <input type="text" name="username" class="form-control" placeholder="Username" required autocomplete="off">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Alamat Email</label>
                            <input type="email" name="email" class="form-control" placeholder="nama@gmail.com" required autocomplete="off">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••" required autocomplete="new-password">
                        </div>

                        <button name="register" type="submit" class="btn btn-success w-100 mt-2 shadow-sm">Daftar Sekarang</button>
                    </form>

                    <div class="text-center mt-4 small">
                        <span class="text-muted">Sudah punya akun?</span> 
                        <a href="login.php" class="text-success text-decoration-none fw-bold ms-1">Login di sini</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>