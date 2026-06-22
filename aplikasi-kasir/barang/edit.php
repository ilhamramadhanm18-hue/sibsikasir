<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['nama']) || trim($_SESSION['nama']) == '') {
    header("Location: ../login.php");
    exit;
}

$username_aktif = $_SESSION['username'];
$id_barang = isset($_GET['id']) ? intval($_GET['id']) : 0;

$query_barang = "SELECT * FROM barang WHERE id = ? AND username = ?";
$stmt_brg = mysqli_prepare($conn, $query_barang);
mysqli_stmt_bind_param($stmt_brg, "is", $id_barang, $username_aktif);
mysqli_stmt_execute($stmt_brg);
$res_brg = mysqli_stmt_get_result($stmt_brg);
$barang = mysqli_fetch_assoc($res_brg);

if (!$barang) {
    header("Location: index.php");
    exit;
}

$kategori_query = "SELECT * FROM kategori WHERE username = ?";
$stmt_kat = mysqli_prepare($conn, $kategori_query);
mysqli_stmt_bind_param($stmt_kat, "s", $username_aktif);
mysqli_stmt_execute($stmt_kat);
$kategori = mysqli_stmt_get_result($stmt_kat);

$error_message = "";

if (isset($_POST['update'])) {
    $nama   = trim($_POST['nama_barang']);
    $id_kat = $_POST['id_kategori'];
    $harga  = preg_replace('/[^0-9]/', '', $_POST['harga']);
    $stok   = preg_replace('/[^0-9]/', '', $_POST['stok']);

    $query_update = "UPDATE barang SET nama_barang = ?, id_kategori = ?, harga = ?, stok = ? WHERE id = ? AND username = ?";
    $stmt_up = mysqli_prepare($conn, $query_update);
    mysqli_stmt_bind_param($stmt_up, "siiiis", $nama, $id_kat, $harga, $stok, $id_barang, $username_aktif);

    if (mysqli_stmt_execute($stmt_up)) {
        mysqli_stmt_close($stmt_up);
        $_SESSION['sukses'] = true;
        header("Location: index.php");
        exit;
    } else {
        $error_message = "Gagal memperbarui barang.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang - SIBSIKASIR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f4f7fe; color: #2d3748; }
        .card { border: none; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); padding: 30px; }
        .form-control, .form-select { border-radius: 12px; padding: 12px 15px; border: 1px solid #edf2f7; background: #f8fafc; }
        .form-control:focus { border-color: #198754; box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.15); }
        .label-custom { font-weight: 700; color: #718096; font-size: 0.8rem; text-transform: uppercase; margin-bottom: 8px; }
        .btn-update { background: #198754; color: #fff; border-radius: 12px; padding: 12px 30px; font-weight: 600; transition: 0.3s; }
        .btn-update:hover { background: #157347; transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <h3 class="fw-bold mb-1">Edit Produk</h3>
                    <p class="text-muted mb-4">Perbarui informasi barang yang Anda pilih.</p>
                    
                    <?php if(!empty($error_message)): ?>
                        <div class="alert alert-danger rounded-3"><?= $error_message ?></div>
                    <?php endif; ?>

                    <form method="POST" action="" autocomplete="off">
                        <div class="mb-3">
                            <label class="label-custom">Nama Produk</label>
                            <input type="text" name="nama_barang" class="form-control" value="<?= htmlspecialchars($barang['nama_barang']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="label-custom">Kategori</label>
                            <select name="id_kategori" class="form-select" required>
                                <?php while ($k = mysqli_fetch_assoc($kategori)) { ?>
                                    <option value="<?= $k['id']; ?>" <?= $k['id'] == $barang['id_kategori'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($k['nama_kategori']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="label-custom">Harga Jual (Rp)</label>
                                <input type="text" id="input_harga" name="harga" class="form-control" 
                                       value="<?= number_format($barang['harga'], 0, ',', '.'); ?>" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="label-custom">Stok</label>
                                <input type="number" name="stok" class="form-control" value="<?= $barang['stok']; ?>" required min="0">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <a href="index.php" class="text-secondary text-decoration-none fw-semibold">← Batal</a>
                            <button name="update" type="submit" class="btn btn-update">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const inputHarga = document.getElementById('input_harga');
        inputHarga.addEventListener('keyup', function(e) {
            let val = this.value.replace(/[^0-9]/g, ''); 
            this.value = val.replace(/\B(?=(\d{3})+(?!\d))/g, "."); 
        });
    </script>
</body>
</html>