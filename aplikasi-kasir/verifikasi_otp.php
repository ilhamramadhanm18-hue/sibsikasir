<?php
session_start();
include 'config/koneksi.php';

// Proteksi halaman agar tidak bisa diakses langsung tanpa input email terlebih dahulu
if (!isset($_SESSION['reset_email'])) {
    header("Location: lupa_password.php");
    exit;
}

$email_user = $_SESSION['reset_email'];

if (isset($_POST['submit_verifikasi'])) {
    global $conn;
    $otp_input = mysqli_real_escape_string($conn, $_POST['otp']);
    $password_baru = mysqli_real_escape_string($conn, $_POST['password_baru']);
    
    // Menggunakan enkripsi md5 sesuai dengan sistem Anda
    $password_secure = md5($password_baru); 

    // Cari data token/otp di database sesuai email session
    $queryCek = mysqli_query($conn, "SELECT reset_token, token_expire FROM users WHERE email = '$email_user' LIMIT 1");
    $data = mysqli_fetch_assoc($queryCek);

    $waktu_sekarang = date('Y-m-d H:i:s');

    if ($data) {
        // Cocokkan OTP
        if ($data['reset_token'] === $otp_input) {
            // Cek apakah waktu OTP belum kadaluarsa
            if ($waktu_sekarang <= $data['token_expire']) {
                
                // Berhasil! Perbarui password dan hapus token/waktu dari database
                mysqli_query($conn, "UPDATE users SET password = '$password_secure', reset_token = NULL, token_expire = NULL WHERE email = '$email_user'");
                
                // Bersihkan session pemulihan
                unset($_SESSION['reset_email']);

                echo "<script>
                        alert('Password berhasil diperbarui! Silakan login kembali.');
                        window.location='login.php';
                      </script>";
                exit;
            } else {
                echo "<script>alert('Kode OTP sudah kadaluarsa! Silakan minta kode baru.'); window.location='lupa_password.php';</script>";
            }
        } else {
            echo "<script>alert('Kode OTP salah! Silakan periksa kembali email Anda.');</script>";
        }
    } else {
        echo "<script>alert('Terjadi kesalahan sistem.'); window.location='lupa_password.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP | SIBSIKASIR</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: radial-gradient(circle at 10% 20%, rgba(240, 253, 244, 1) 0%, rgba(212, 247, 226, 1) 90%);
            min-height: 100vh; display: flex; align-items: center; color: #1e293b;
        }
        .card {
            border: 1px solid rgba(255, 255, 255, 0.7); border-radius: 28px;
            background: rgba(255, 255, 255, 0.85); box-shadow: 0 20px 50px rgba(15, 23, 42, 0.04); padding: 15px;
        }
        .app-logo-icon {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white; width: 54px; height: 54px; display: flex; align-items: center; justify-content: center; border-radius: 16px; margin: 0 auto 15px auto;
        }
        .form-label-custom { font-weight: 700; color: #64748b; font-size: 0.78rem; text-transform: uppercase; }
        .form-control { border-radius: 14px; padding: 12px 16px; background-color: #f8fafc; border: 1px solid #cbd5e1; }
        .otp-input-style {
            text-align: center; font-size: 1.5rem; letter-spacing: 6px; font-weight: 800; color: #059669;
        }
        .btn-verifikasi { 
            border-radius: 14px; padding: 13px; font-weight: 700; 
            background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none;
        }
        .btn-verifikasi:hover { background: linear-gradient(135deg, #059669 0%, #047857 100%); color: white; transform: translateY(-2px); }
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
                            <i class="fa-solid fa-shield-check fa-xl"></i>
                        </div>
                        <h4 class="m-0 fw-bold">Verifikasi OTP</h4>
                        <p class="text-muted small mt-1 mb-0 px-2">Kode telah dikirim ke:<br><b class="text-dark"><?= htmlspecialchars($email_user); ?></b></p>
                    </div>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label-custom mb-2">6 Digit Kode OTP</label>
                            <input type="text" name="otp" class="form-control otp-input-style" placeholder="000000" maxlength="6" required autocomplete="off">
                        </div>

                        <div class="mb-4">
                            <label class="form-label-custom mb-2">Password Baru Anda</label>
                            <input type="password" name="password_baru" class="form-control" placeholder="Buat password baru" required>
                        </div>
                        
                        <button type="submit" name="submit_verifikasi" class="btn btn-verifikasi w-100 shadow-sm">
                            Perbarui Password <i class="fa-solid fa-check-to-slot ms-1 small"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>