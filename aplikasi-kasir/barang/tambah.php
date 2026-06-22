<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['nama']) || trim($_SESSION['nama']) == '') {
    header("Location: ../login.php");
    exit;
}

$username_aktif = $_SESSION['username'];

$kategori_query = "SELECT * FROM kategori WHERE username = ?";
$stmt_kat = mysqli_prepare($conn, $kategori_query);
mysqli_stmt_bind_param($stmt_kat, "s", $username_aktif);
mysqli_stmt_execute($stmt_kat);
$kategori = mysqli_stmt_get_result($stmt_kat);

$error_message = "";

if (isset($_POST['simpan'])) {
    $nama   = trim($_POST['nama_barang']);
    $id_kat = $_POST['id_kategori'];
    $harga  = preg_replace('/[^0-9]/', '', $_POST['harga']);
    $stok   = preg_replace('/[^0-9]/', '', $_POST['stok']);

    $query = "INSERT INTO barang (nama_barang, id_kategori, username, harga, stok) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sissi", $nama, $id_kat, $username_aktif, $harga, $stok);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: index.php?status=tambah_sukses");
        exit;
    } else {
        $error_message = "Gagal menyimpan barang: " . mysqli_error($conn);
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang - SIBSIKASIR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #f4f7fe; 
            color: #2d3748;
        }
        .card { 
            border: none; 
            border-radius: 20px; 
            background: #ffffff;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05); 
            padding: 30px;
        }
        .form-control, .form-select { 
            border-radius: 12px; 
            padding: 12px 15px; 
            border: 1px solid #edf2f7; 
            background: #f8fafc;
        }
        .form-control:focus, .form-select:focus { 
            border-color: #198754; 
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.15); 
        }
        .btn-simpan { 
            background: #198754; 
            color: #fff;
            border-radius: 12px; 
            padding: 12px 30px; 
            font-weight: 600; 
            transition: 0.3s; 
        }
        .btn-simpan:hover { background: #157347; transform: translateY(-2px); }
        .label-custom { font-weight: 700; color: #718096; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <h3 class="fw-bold mb-1">Tambah Produk Baru</h3>
                <p class="text-muted mb-4">Lengkapi data produk untuk menambahkannya ke inventaris.</p>
                
                <?php if(!empty($error_message)): ?>
                    <div class="alert alert-danger rounded-3"><?= $error_message ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="label-custom mb-2">Nama Produk</label>
                        <input type="text" name="nama_barang" class="form-control" placeholder="Contoh: Kopi Arabika" required>
                    </div>

                    <div class="mb-3">
                        <label class="label-custom mb-2">Kategori</label>
                        <select name="id_kategori" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php while ($k = mysqli_fetch_assoc($kategori)) { ?>
                                <option value="<?= $k['id']; ?>"><?= htmlspecialchars($k['nama_kategori']); ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="label-custom mb-2">Harga Jual (Rp)</label>
                            <input type="text" id="input_harga" name="harga" class="form-control" placeholder="0" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="label-custom mb-2">Stok Awal</label>
                            <input type="number" name="stok" class="form-control" placeholder="0" required min="0">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <a href="index.php" class="text-secondary text-decoration-none fw-semibold">← Batal</a>
                        <button name="simpan" type="submit" class="btn btn-simpan px-4">Simpan Data</button>
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