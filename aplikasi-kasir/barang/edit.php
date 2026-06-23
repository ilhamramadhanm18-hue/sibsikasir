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
    
    // PERBAIKAN DI SINI: Memanggil fungsi secara langsung tanpa memasukkannya ke variabel
    mysqli_stmt_bind_param($stmt_up, "siiiis", $nama, $id_kat, $harga, $stok, $id_barang, $username_aktif);

    if (mysqli_stmt_execute($stmt_up)) {
        mysqli_stmt_close($stmt_up);
        $_SESSION['sukses'] = true;
        header("Location: index.php?status=sukses_edit");
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #f8fafc; 
            color: #1e293b; 
        }
        .card { 
            border: none; 
            border-radius: 24px; 
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03); 
            padding: 35px;
            border: 1px solid #e2e8f0;
        }
        .form-label-wrapper {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 8px;
        }
        .label-custom { 
            font-weight: 700; 
            color: #64748b; 
            font-size: 0.8rem; 
            text-transform: uppercase; 
            letter-spacing: 0.8px; 
            margin-bottom: 0;
        }
        .form-control, .form-select { 
            border-radius: 14px; 
            padding: 13px 18px; 
            border: 1px solid #cbd5e1; 
            background: #f8fafc;
            color: #1e293b;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }
        .form-control:focus, .form-select:focus { 
            border-color: #10b981; 
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); 
        }
        .input-group-text-custom {
            border-radius: 14px 0 0 14px;
            border: 1px solid #cbd5e1;
            border-right: none;
            background: #e2e8f0;
            color: #475569;
            font-weight: 700;
            padding-left: 18px;
            padding-right: 15px;
        }
        .input-group-custom .form-control {
            border-radius: 0 14px 14px 0;
        }
        .btn-update { 
            background: #10b981; 
            color: #fff;
            border: none;
            border-radius: 14px; 
            padding: 13px 32px; 
            font-weight: 600; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-update:hover { 
            background: #059669; 
            color: #fff;
            transform: translateY(-2px); 
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.25);
        }
        .btn-batal {
            color: #64748b;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 16px;
            border-radius: 10px;
        }
        .btn-batal:hover {
            color: #1e293b;
            background: #f1f5f9;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="p-3 bg-warning-subtle text-warning rounded-4 d-inline-flex">
                            <i class="fa-solid fa-pen-to-square fa-xl"></i>
                        </div>
                        <div>
                            <h3 class="fw-800 m-0 text-dark" style="letter-spacing: -0.5px;">Edit Produk</h3>
                            <p class="text-muted m-0 small">Perbarui informasi barang yang Anda pilih.</p>
                        </div>
                    </div>
                    <hr class="text-muted my-4 opacity-25">
                    
                    <?php if(!empty($error_message)): ?>
                        <div class="alert alert-danger rounded-3 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-circle-exclamation"></i> <?= $error_message ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" autocomplete="off">
                        <div class="mb-4">
                            <div class="form-label-wrapper">
                                <i class="fa-solid fa-tag text-muted small"></i>
                                <label class="label-custom">Nama Produk</label>
                            </div>
                            <input type="text" name="nama_barang" class="form-control" value="<?= htmlspecialchars($barang['nama_barang']); ?>" required>
                        </div>

                        <div class="mb-4">
                            <div class="form-label-wrapper">
                                <i class="fa-solid fa-layer-group text-muted small"></i>
                                <label class="label-custom">Kategori</label>
                            </div>
                            <select name="id_kategori" class="form-select" required>
                                <?php while ($k = mysqli_fetch_assoc($kategori)) { ?>
                                    <option value="<?= $k['id']; ?>" <?= $k['id'] == $barang['id_kategori'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($k['nama_kategori']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="form-label-wrapper">
                                    <i class="fa-solid fa-money-bill-wave text-muted small"></i>
                                    <label class="label-custom">Harga Jual</label>
                                </div>
                                <div class="input-group input-group-custom">
                                    <span class="input-group-text input-group-text-custom">Rp</span>
                                    <input type="text" id="input_harga" name="harga" class="form-control" 
                                           value="<?= number_format($barang['harga'], 0, ',', '.'); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="form-label-wrapper">
                                    <i class="fa-solid fa-cubes text-muted small"></i>
                                    <label class="label-custom">Stok</label>
                                </div>
                                <input type="number" name="stok" class="form-control" value="<?= $barang['stok']; ?>" required min="0">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <a href="index.php" class="btn-batal">
                                <i class="fa-solid fa-arrow-left small"></i> Batal
                            </a>
                            <button name="update" type="submit" class="btn btn-update">
                                <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                            </button>
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

        <?php if(!empty($error_message)): ?>
        Swal.fire({
            icon: 'error',
            title: 'Gagal Memperbarui!',
            text: '<?= $error_message ?>',
            confirmButtonColor: '#10b981'
        });
        <?php endif; ?>

        document.querySelector('.btn-batal').addEventListener('click', function(e) {
            e.preventDefault(); 
            const urlTujuan = this.getAttribute('href');

            Swal.fire({
                title: 'Batalkan Perubahan?',
                text: "Perubahan data produk yang baru Anda ketik tidak akan disimpan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#64748b',
                cancelButtonColor: '#10b981',
                confirmButtonText: 'Ya, Batal',
                cancelButtonText: 'Lanjutkan Edit'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = urlTujuan; 
                }
            });
        });
    </script>
</body>
</html>