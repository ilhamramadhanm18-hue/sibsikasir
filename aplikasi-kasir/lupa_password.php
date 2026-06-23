<?php
include 'config/koneksi.php';

// Inisialisasi variabel untuk pemicu SweetAlert2 di bagian bawah halaman
$status_reset = '';
$token_reset = '';

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
        
        // UPGRADE: Menyimpan status sukses & token untuk dipicu oleh SweetAlert2 tanpa memotong aliran dokumen asli
        $status_reset = 'sukses';
        $token_reset = $token;
    } else {
        // UPGRADE: Menyimpan status gagal untuk dipicu oleh SweetAlert2
        $status_reset = 'gagal';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password | SIBSIKASIR</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: radial-gradient(circle at 10% 20%, rgba(240, 253, 244, 1) 0%, rgba(212, 247, 226, 1) 90%);
            min-height: 100vh;
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

        .card-body h4 {
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

        .btn-reset { 
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
        
        .btn-reset:hover { 
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            transform: translateY(-2px); 
            box-shadow: 0 8px 22px rgba(16, 185, 129, 0.3);
        }

        .btn-reset:active {
            transform: translateY(0);
        }

        .link-back {
            color: #64748b;
            font-size: 0.88rem;
            font-weight: 600;
            transition: color 0.2s;
        }

        .link-back:hover {
            color: #1e293b;
        }

        /* Penyelarasan kosmetik font SweetAlert2 agar cocok dengan tema Plus Jakarta Sans */
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
            <div class="card shadow-lg p-3">
                <div class="card-body">
                    
                    <div class="text-center mb-4">
                        <div class="app-logo-icon">
                            <i class="fa-solid fa-key-skeleton fa-xl" style="transform: rotate(-45deg);"></i>
                        </div>
                        <h4 class="m-0">Lupa Password</h4>
                        <p class="text-muted small mt-1 mb-0 px-2">Masukkan email terdaftar untuk membuat tautan pemulihan</p>
                    </div>

                    <form action="" method="POST">
                        <div class="mb-4">
                            <label class="form-label-custom mb-2">Email Terdaftar</label>
                            <div class="input-group-custom">
                                <input type="email" name="email" class="form-control" placeholder="nama@email.com" required>
                                <i class="fa-solid fa-envelope input-icon-left"></i>
                            </div>
                        </div>
                        
                        <button type="submit" name="submit_email" class="btn btn-reset w-100 shadow-sm">
                            Kirim Link Reset <i class="fa-solid fa-paper-plane ms-1 small"></i>
                        </button>

                        <div class="mt-4 text-center">
                            <a href="login.php" class="link-back text-decoration-none">
                                <i class="fa-solid fa-arrow-left small me-1"></i> Kembali ke Login
                            </a>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Menangkap penanda aksi dari backend PHP untuk merender desain pop-up baru
    const statusReset = "<?= $status_reset; ?>";
    const tokenReset = "<?= $token_reset; ?>";

    if (statusReset === 'sukses') {
        Swal.fire({
            title: 'Berhasil!',
            text: 'Sistem simulasi: Link reset telah dibuat!',
            icon: 'success',
            showConfirmButton: false, // Menghilangkan tombol
            allowOutsideClick: false,
            didOpen: () => {
                // Biarkan animasi pop-up muncul sampai selesai, lalu pindah halaman setelah 1200ms (1.2 detik)
                setTimeout(() => {
                    window.location = 'ganti_password.php?token=' + tokenReset;
                }, 1200);
            }
        });
    } else if (statusReset === 'gagal') {
        Swal.fire({
            title: 'Gagal!',
            text: 'Email tidak terdaftar!',
            icon: 'error',
            confirmButtonText: 'Coba Lagi'
        });
    }
</script>
</body>
</html>