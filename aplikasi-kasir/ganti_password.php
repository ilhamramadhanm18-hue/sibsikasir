<?php
include 'config/koneksi.php';

// Inisialisasi variabel status untuk pemicu SweetAlert2 di bagian bawah
$status_action = '';

// Validasi Token
if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($conn, $_GET['token']);
    $waktu_sekarang = date("Y-m-d H:i:s");
    $cek_token = mysqli_query($conn, "SELECT * FROM users WHERE reset_token='$token' AND token_expire > '$waktu_sekarang'");

    if (mysqli_num_rows($cek_token) == 0) {
        // UPGRADE: Menandai token tidak valid untuk dipicu SweetAlert2
        $status_action = 'token_invalid';
    }
} else {
    header("Location: login.php");
    exit;
}

// Proses Update
if (isset($_POST['change_password'])) {
    $token = mysqli_real_escape_string($conn, $_GET['token']);
    $password_baru = $_POST['password'];
    $konfirmasi = $_POST['konfirmasi'];

    if ($password_baru !== $konfirmasi) {
        $error = "Password tidak cocok!";
    } else {
        $password_fix = mysqli_real_escape_string($conn, $password_baru);
        $update = mysqli_query($conn, "UPDATE users SET password='$password_fix', reset_token=NULL, token_expire=NULL WHERE reset_token='$token'");

        if ($update) {
            // UPGRADE: Menandai ganti password sukses untuk dipicu SweetAlert2
            $status_action = 'sukses_ganti';
        } else {
            $error = "Terjadi kesalahan sistem.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Set Password Baru | SIBSIKASIR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: linear-gradient(135deg, #e0f2f1 0%, #b2dfdb 100%);
            height: 100vh;
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
        }
        .btn-success { border-radius: 12px; padding: 12px; font-weight: 700; background: #198754; }
        
        /* Penyelarasan kosmetik font SweetAlert2 agar cocok dengan tema Plus Jakarta Sans */
        .swal2-popup {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            border-radius: 24px !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <div class="text-center mb-4">
                        <h4 class="fw-bold">Buat Password Baru</h4>
                        <p class="text-muted small">Masukkan password baru untuk akun Anda.</p>
                    </div>
                    
                    <?php if (isset($error)) echo "<div class='alert alert-danger p-2 text-center small'>$error</div>"; ?>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="small fw-bold text-secondary mb-1">Password Baru</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label class="small fw-bold text-secondary mb-1">Konfirmasi Password</label>
                            <input type="password" name="konfirmasi" class="form-control" required>
                        </div>
                        <button type="submit" name="change_password" class="btn btn-success w-100">Simpan Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Menangkap penanda aksi dari backend PHP untuk merender desain pop-up baru
        const statusAction = "<?= $status_action; ?>";

        if (statusAction === 'sukses_ganti') {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Password berhasil diperbarui!',
                icon: 'success',
                showConfirmButton: false, // Menghilangkan tombol agar langsung fokus ke perpindahan halaman
                allowOutsideClick: false,
                didOpen: () => {
                    // Memberikan waktu jeda agar animasi centang sukses selesai bergulir indah, lalu pindah halaman
                    setTimeout(() => {
                        window.location = 'login.php';
                    }, 1200);
                }
            });
        } else if (statusAction === 'token_invalid') {
            Swal.fire({
                title: 'Gagal!',
                text: 'Token tidak valid atau sudah kedaluwarsa!',
                icon: 'error',
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    setTimeout(() => {
                        window.location = 'login.php';
                    }, 1500);
                }
            });
        }
    </script>
</body>
</html>