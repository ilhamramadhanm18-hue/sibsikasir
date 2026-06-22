<?php
session_start();
include "config/koneksi.php";

// Jika user ternyata sudah login dan session-nya valid, langsung lempar ke dashboard
if (isset($_SESSION['nama']) && trim($_SESSION['nama']) != '') {
    echo "<script>window.location='dashboard.php';</script>";
    exit;
}

// Buat variabel awal untuk menampung pesan error
$error_message = "";

if (isset($_POST['login'])) {
    $username = trim(mysqli_real_escape_string($conn, $_POST['username']));
    $password = trim(mysqli_real_escape_string($conn, $_POST['password']));

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password'");

    if ($query && mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);

        $_SESSION['username'] = $data['username'];
        $_SESSION['nama']     = trim($data['nama']);
        $_SESSION['role']     = $data['role'];

        echo "<script>window.location='dashboard.php';</script>";
        exit;
    } else {
        // GANTI DI SINI: Alih-alih memanggil alert script, kita isi teks ke variabel error
        $error_message = "Login gagal! Username atau Password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN SIKASIR</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            /* Background Gradient Modern */
            background: linear-gradient(135deg, #e0f2f1 0%, #b2dfdb 100%);
            height: 100vh;
            display: flex;
            align-items: center;
        }

        .card {
            border: none;
            border-radius: 20px;
            /* Efek Kaca (Glassmorphism) */
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 20px;
        }

        .card-header h3 {
            color: #198754;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            background-color: #f8f9fa;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background-color: #fff;
            border-color: #198754;
            box-shadow: 0 0 0 4px rgba(25, 135, 84, 0.1);
        }

        .btn-success {
            border-radius: 12px;
            padding: 12px;
            background-color: #198754;
            border: none;
            transition: transform 0.2s;
        }

        .btn-success:hover {
            background-color: #157347;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow-lg p-4">
                    <div class="card-header text-center bg-transparent border-0 pt-2">
                        <h3 class="fw-bold m-0">SIBSIKASIR</h3>
                        <small class="text-muted">Masukkan kredensial Anda</small>
                    </div>

                    <div class="card-body">
                        <?php if (!empty($error_message)) : ?>
                            <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert" style="border-radius: 10px;">
                                <?= $error_message; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Username</label>
                                <input type="text" name="username" class="form-control form-control-lg" placeholder="Masukkan username" autocomplete="off" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-secondary">Password</label>
                                <input type="password" name="password" class="form-control form-control-lg" placeholder="••••••••" autocomplete="new-password" required>
                            </div>

                            <button name="login" type="submit" class="btn btn-success btn-lg w-100 fw-bold shadow-sm">Masuk Sistem</button>

                            <div class="mt-4 text-center" style="font-size: 14px;">
                                <a href="register.php" class="text-success text-decoration-none fw-bold">Daftar Akun</a>
                                <span class="text-muted mx-2">|</span>
                                <a href="lupa_password.php" class="text-secondary text-decoration-none">Lupa Password?</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>