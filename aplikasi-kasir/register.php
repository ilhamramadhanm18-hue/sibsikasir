<?php
session_start();
include "config/koneksi.php";

// Buat variabel bantuan untuk memicu SweetAlert2 setelah halaman dirender
$reg_status = ""; // Nilai bisa: "sukses", "username_kembar", "sistem_gagal"

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
        // UPGRADE: Tandai status jika username sudah terdaftar
        $reg_status = "username_kembar";
    } else {
        // Query Simpan Akun Baru ke Database
        $query_reg = mysqli_query($conn, "INSERT INTO users (nama, username, email, password, role) 
                                           VALUES ('$nama', '$username', '$email', '$password', '$role')");
        
        if ($query_reg) {
            // UPGRADE: Tandai status jika pendaftaran berhasil
            $reg_status = "sukses";
        } else {
            // UPGRADE: Tandai status jika query database gagal
            $reg_status = "sistem_gagal";
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: radial-gradient(circle at 10% 20%, rgba(240, 253, 244, 1) 0%, rgba(212, 247, 226, 1) 90%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            color: #1e293b;
            padding: 30px 0;
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

        .card-body h2 {
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
            padding: 12px 16px 12px 44px;
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

        .btn-register { 
            border-radius: 14px; 
            padding: 13px; 
            font-weight: 700; 
            font-size: 1rem;
            letter-spacing: -0.2px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%); 
            color: white;
            border: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 6px 18px rgba(16, 185, 129, 0.2);
        }
        
        .btn-register:hover { 
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            transform: translateY(-2px); 
            box-shadow: 0 8px 22px rgba(16, 185, 129, 0.3);
        }

        .btn-register:active {
            transform: translateY(0);
        }

        .links-wrapper {
            font-size: 0.88rem;
            font-weight: 500;
        }

        .link-login {
            color: #10b981;
            transition: color 0.2s;
        }

        .link-login:hover {
            color: #047857;
        }

        /* Styling kustom agar SweetAlert2 serasi dengan desain SIBSIKASIR */
        .swal2-popup {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            border-radius: 24px !important;
        }
        .swal2-styled.swal2-confirm {
            background: #10b981 !important;
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
            <div class="card shadow-lg p-3">
                <div class="card-body">
                    
                    <div class="text-center mb-4">
                        <div class="app-logo-icon">
                            <i class="fa-solid fa-user-plus fa-xl"></i>
                        </div>
                        <h2 class="m-0">DAFTAR AKUN</h2>
                        <p class="text-muted small mt-1 mb-0">Lengkapi data untuk membuat akun kasir baru</p>
                    </div>

                    <form method="POST" action="" autocomplete="off">
                        
                        <div class="mb-3">
                            <label class="form-label-custom mb-2">Nama Lengkap</label>
                            <div class="input-group-custom">
                                <input type="text" name="nama" class="form-control" placeholder="Masukkan Nama Lengkap" required autocomplete="off">
                                <i class="fa-solid fa-id-card input-icon-left"></i>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label-custom mb-2">Username</label>
                            <div class="input-group-custom">
                                <input type="text" name="username" class="form-control" placeholder="Buat username unik" required autocomplete="off">
                                <i class="fa-solid fa-user input-icon-left"></i>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label-custom mb-2">Alamat Email</label>
                            <div class="input-group-custom">
                                <input type="email" name="email" class="form-control" placeholder="nama@gmail.com" required autocomplete="off">
                                <i class="fa-solid fa-envelope input-icon-left"></i>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label-custom mb-2">Password</label>
                            <div class="input-group-custom">
                                <input type="password" name="password" class="form-control" placeholder="••••••••" required autocomplete="new-password">
                                <i class="fa-solid fa-lock input-icon-left"></i>
                            </div>
                        </div>

                        <button name="register" type="submit" class="btn btn-register w-100 shadow-sm">
                            Daftar Sekarang <i class="fa-solid fa-circle-check ms-1 small"></i>
                        </button>
                    </form>

                    <div class="text-center mt-4 links-wrapper">
                        <span class="text-muted">Sudah punya akun?</span> 
                        <a href="login.php" class="link-login text-decoration-none fw-bold ms-1">Login di sini</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php if ($reg_status === "sukses") : ?>
<script>
    Swal.fire({
        title: 'Pendaftaran Berhasil!',
        text: 'Akun Anda telah terdaftar. Mengalihkan ke halaman login...',
        icon: 'success',
        timer: 1700, // Otomatis hilang dalam 1.5 detik
        showConfirmButton: false, // Tanpa menekan tombol konfirmasi
        timerProgressBar: true,
        willClose: () => {
            window.location = 'login.php';
        }
    });
</script>
<?php elseif ($reg_status === "username_kembar") : ?>
<script>
    Swal.fire({
        title: 'Gagal Mendaftar!',
        text: 'Username sudah digunakan, silakan gunakan username lain.',
        icon: 'error',
        confirmButtonText: 'Coba Lagi'
    });
</script>
<?php elseif ($reg_status === "sistem_gagal") : ?>
<script>
    Swal.fire({
        title: 'Kesalahan Sistem!',
        text: 'Gagal menyimpan data akun baru ke database.',
        icon: 'error',
        confirmButtonText: 'Oke'
    });
</script>
<?php endif; ?>

</body>
</html>