<?php
include 'config/koneksi.php';

if (isset($_POST['submit_email'])) {
    global $conn; // Menggunakan variabel $conn dari config
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Cek apakah email terdaftar
    $cek_email = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    
    if (mysqli_num_rows($cek_email) > 0) {
        // Generate Token acak dan set kedaluwarsa 1 jam
        $token = bin2hex(random_bytes(32));
        $expire = date("Y-m-d H:i:s", strtotime("+1 hour"));
        
        // Simpan token ke database
        mysqli_query($conn, "UPDATE users SET reset_token='$token', token_expire='$expire' WHERE email='$email'");
        
        // Pengalihan otomatis ke halaman ganti password baru (Simulasi)
        echo "<script>
                alert('Sistem simulasi: Link reset telah dibuat!');
                window.location='ganti_password.php?token=$token';
              </script>";
    } else {
        echo "<script>alert('Email tidak terdaftar!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password | SIBSIKASIR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: linear-gradient(135deg, #e0f2f1 0%, #b2dfdb 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
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
        .btn-reset { 
            border-radius: 12px; 
            padding: 12px; 
            font-weight: 700; 
            background: #198754; 
            color: white;
            transition: 0.3s;
        }
        .btn-reset:hover { background: #146c43; color: white; transform: translateY(-2px); }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="text-center mb-4">
                    <h4 class="fw-bold">Lupa Password</h4>
                    <p class="text-muted small px-3">Masukkan email Anda.</p>
                </div>

                <form action="" method="POST">
                    <div class="mb-4">
                        <label class="small fw-bold text-secondary mb-1">Email Terdaftar</label>
                        <input type="email" name="email" class="form-control" placeholder="nama@email.com" required>
                    </div>
                    
                    <button type="submit" name="submit_email" class="btn btn-reset w-100 shadow-sm">Kirim Link Reset</button>

                    <div class="mt-4 text-center">
                        <a href="login.php" class="text-secondary text-decoration-none small fw-semibold">← Kembali ke Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>