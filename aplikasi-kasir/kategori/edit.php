<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['nama']) || trim($_SESSION['nama']) == '') {
    header("Location: ../login.php");
    exit;
}

$username_aktif = $_SESSION['username'];
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$query = "SELECT * FROM kategori WHERE id = ? AND username = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "is", $id, $username_aktif);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$d = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$d) {
    header("Location: index.php");
    exit;
}

$error_message = "";

if (isset($_POST['update'])) {
    $nama = trim($_POST['nama_kategori']);

    if ($nama != '') {
        $query_update = "UPDATE kategori SET nama_kategori = ? WHERE id = ? AND username = ?";
        $stmt_update = mysqli_prepare($conn, $query_update);
        mysqli_stmt_bind_param($stmt_update, "sis", $nama, $id, $username_aktif);

        if (mysqli_stmt_execute($stmt_update)) {
            mysqli_stmt_close($stmt_update);
            header("Location: index.php?status=diubah");
            exit;
        } else {
            $error_message = "Gagal memperbarui kategori.";
            mysqli_stmt_close($stmt_update);
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
    <title>Edit Kategori - SIBSIKASIR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f4f7fe; color: #2d3748; }
        .card { border: none; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); padding: 20px; }
        .form-label { font-weight: 700; color: #718096; font-size: 0.8rem; text-transform: uppercase; }
        .form-control { border-radius: 12px; padding: 12px 15px; border: 1px solid #edf2f7; background: #f8fafc; }
        .form-control:focus { border-color: #ffc107; box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.15); }
        .btn-update { background: #ffc107; color: #000; border-radius: 12px; padding: 12px 30px; font-weight: 600; transition: 0.3s; }
        .btn-update:hover { background: #e0a800; transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4">
                    <h3 class="fw-bold mb-1">Edit Kategori</h3>
                    <p class="text-muted mb-4">Ubah nama kategori sesuai kebutuhan Anda.</p>

                    <?php if(!empty($error_message)): ?>
                        <div class="alert alert-danger rounded-3"><?= $error_message ?></div>
                    <?php endif; ?>

                    <form method="POST" action="" autocomplete="off">
                        <div class="mb-4">
                            <label class="form-label">Nama Kategori</label>
                            <input type="text" name="nama_kategori" class="form-control" value="<?= htmlspecialchars($d['nama_kategori']); ?>" required autofocus>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="index.php" class="text-secondary text-decoration-none fw-semibold">← Batal</a>
                            <button name="update" type="submit" class="btn btn-update">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>