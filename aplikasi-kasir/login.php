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

// Buat variabel bantuan untuk memicu SweetAlert2 setelah page reload
$login_sukses = false;
$nama_user = "";

if (isset($_POST['login'])) {
    $username = trim(mysqli_real_escape_string($conn, $_POST['username']));
    $password = trim(mysqli_real_escape_string($conn, $_POST['password']));

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password'");

    if ($query && mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);

        $_SESSION['username'] = $data['username'];
        $_SESSION['nama']     = trim($data['nama']);
        $_SESSION['role']     = $data['role'];

        // Set variabel true agar SweetAlert muncul saat HTML selesai dirender
        $login_sukses = true;
        $nama_user = $_SESSION['nama'];
    } else {
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at 10% 20%, rgba(240, 253, 244, 1) 0%, rgba(212, 247, 226, 1) 90%);
            height: 100vh;
            display: flex;
            align-items: center;
            color: #1e293b;
        }

        .card {
            border: 1px solid rgba(255, 255, 255, 0.7);
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.04);
            padding: 15px;
        }

        .app-logo-icon {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            width: 54px;
            height: 54px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            margin: 0 auto 15px auto;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        }

        .card-header h3 {
            color: #0f172a;
            font-weight: 800;
            letter-spacing: -0.75px;
        }

        .form-label-custom {
            font-weight: 700;
            color: #64748b;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .input-group-custom {
            position: relative;
        }

        .input-icon-left {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            z-index: 5;
            transition: color 0.3s ease;
        }

        .form-control {
            border-radius: 14px;
            padding: 13px 16px 13px 44px;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            color: #1e293b;
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-control:focus {
            background-color: #fff;
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.12);
        }

        .form-control:focus + .input-icon-left {
            color: #10b981;
        }

        .btn-success {
            border-radius: 14px;
            padding: 13px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: -0.2px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 6px 18px rgba(16, 185, 129, 0.2);
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(16, 185, 129, 0.3);
        }

        .btn-success:active {
            transform: translateY(0);
        }

        .links-wrapper {
            font-size: 0.88rem;
            font-weight: 500;
        }

        .link-register {
            color: #10b981;
            transition: color 0.2s;
        }

        .link-register:hover {
            color: #047857;
        }

        .link-forgot {
            color: #64748b;
            transition: color 0.2s;
        }

        .link-forgot:hover {
            color: #334155;
        }

        /* Styling kustom untuk mencocokkan SweetAlert dengan font Plus Jakarta Sans */
        .swal2-popup {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            border-radius: 24px !important;
        }
        .swal2-styled.swal2-confirm {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            border-radius: 12px !important;
            padding: 10px 24px !important;
            font-weight: 600 !important;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4 px-3">
                <div class="card shadow-lg p-4">
                    <div class="card-header text-center bg-transparent border-0 pt-3 pb-2">
                        <div class="app-logo-icon">
                            <i class="fa-solid fa-cash-register fa-xl"></i>
                        </div>
                        <h3 class="m-0">SIBSIKASIR</h3>
                        <p class="text-muted small mt-1 mb-0">Masuk sistem kasir</p>
                    </div>

                    <div class="card-body pt-3">
                        <?php if (!empty($error_message)) : ?>
                            <div class="alert alert-danger border-0 d-flex align-items-center gap-2 mb-4" role="alert" style="border-radius: 12px; font-size: 0.88rem; background-color: #fef2f2; color: #991b1b;">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <span><?= $error_message; ?></span>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label-custom mb-2">
                                    Username
                                </label>
                                <div class="input-group-custom">
                                    <input type="text" name="username" class="form-control" placeholder="Masukkan username" autocomplete="off" required>
                                    <i class="fa-solid fa-user input-icon-left"></i>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label-custom mb-2">
                                    Password
                                </label>
                                <div class="input-group-custom">
                                    <input type="password" name="password" class="form-control" placeholder="••••••••" autocomplete="new-password" required>
                                    <i class="fa-solid fa-lock input-icon-left"></i>
                                </div>
                            </div>

                            <button name="login" type="submit" class="btn btn-success w-100">
                                Masuk Sistem <i class="fa-solid fa-arrow-right-to-bracket ms-1 small"></i>
                            </button>

                            <div class="mt-4 text-center links-wrapper">
                                <a href="register.php" class="link-register text-decoration-none fw-bold">Daftar Akun</a>
                                <span class="text-muted opacity-25 mx-2">|</span>
                                <a href="lupa_password.php" class="link-forgot text-decoration-none">Lupa Password?</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <?php if ($login_sukses) : ?>
    <script>
        Swal.fire({
            title: 'Login Berhasil!',
            text: 'Selamat datang kembali, <?= htmlspecialchars($nama_user); ?>.',
            icon: 'success',
            timer: 1600, // Pop-up akan otomatis hilang dalam 1,5 detik
            showConfirmButton: false, // Menghilangkan tombol konfirmasi/button
            timerProgressBar: true, // Menampilkan garis durasi berjalan di bawah pop-up
            didOpen: () => {
                // Menghilangkan fokus atau efek kursor loading jika ada
            },
            willClose: () => {
                // Tepat saat pop-up akan tertutup, langsung pindah halaman
                window.location = 'dashboard.php';
            }
        });
    </script>
    <?php endif; ?>

</body>
</html>