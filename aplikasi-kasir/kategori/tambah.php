<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['nama']) || trim($_SESSION['nama']) == '') {
    header("Location: ../login.php");
    exit;
}

$username_aktif = $_SESSION['username'];
$error_message = "";

if (isset($_POST['simpan'])) {
    $nama_kategori = trim($_POST['nama_kategori']);

    if ($nama_kategori != '') {
        $query_simpan = "INSERT INTO kategori (nama_kategori, username) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $query_simpan);
        mysqli_stmt_bind_param($stmt, "ss", $nama_kategori, $username_aktif);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: index.php?status=sukses");
            exit;
        } else {
            $error_message = "Gagal menyimpan data kategori!";
            mysqli_stmt_close($stmt);
        }
    } else {
        $error_message = "Nama kategori tidak boleh kosong!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kategori - SIBSIKASIR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f4f7fe; color: #2d3748; }
        .card { border: none; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); padding: 20px; }
        .form-label { font-weight: 700; color: #718096; font-size: 0.8rem; text-transform: uppercase; }
        .form-control { border-radius: 12px; padding: 12px 15px; border: 1px solid #edf2f7; background: #f8fafc; }
        .form-control:focus { border-color: #0d6efd; box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15); }
        .btn-simpan { background: #0d6efd; color: #fff; border-radius: 12px; padding: 12px 30px; font-weight: 600; transition: 0.3s; }
        .btn-simpan:hover { background: #0b5ed7; transform: translateY(-2px); color: #fff; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4">
                    <h3 class="fw-bold mb-1">Tambah Kategori</h3>
                    <p class="text-muted mb-4">Buat kategori baru untuk mengelompokkan barang Anda.</p>

                    <?php if(!empty($error_message)): ?>
                        <div class="alert alert-danger rounded-3"><?= $error_message ?></div>
                    <?php endif; ?>

                    <form action="" method="POST" autocomplete="off">
                        <div class="mb-4">
                            <label class="form-label">Nama Kategori</label>
                            <input type="text" name="nama_kategori" class="form-control" placeholder="Contoh: Makanan, Minuman, Alat Tulis" required autofocus>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="index.php" class="text-secondary text-decoration-none fw-semibold">← Batal</a>
                            <button type="submit" name="simpan" class="btn btn-simpan">Simpan Kategori</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>