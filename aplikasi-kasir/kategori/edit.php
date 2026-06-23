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
            // SINKRONISASI: Diubah ke 'sukses_edit' agar memicu pop-up di index kategori
            header("Location: index.php?status=sukses_edit");
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
        .form-label { 
            font-weight: 700; 
            color: #64748b; 
            font-size: 0.8rem; 
            text-transform: uppercase; 
            letter-spacing: 0.8px;
            margin-bottom: 0;
        }
        .form-control { 
            border-radius: 14px; 
            padding: 13px 18px; 
            border: 1px solid #cbd5e1; 
            background: #f8fafc; 
            color: #1e293b;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }
        .form-control:focus { 
            border-color: #f59e0b; 
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.15); 
        }
        .btn-update { 
            background: #f59e0b; 
            color: #ffffff; 
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
            background: #d97706; 
            color: #ffffff;
            transform: translateY(-2px); 
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.25);
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
                <div class="card p-4">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="p-3 bg-warning-subtle text-warning rounded-4 d-inline-flex">
                            <i class="fa-solid fa-folder-open fa-xl"></i>
                        </div>
                        <div>
                            <h3 class="fw-800 m-0 text-dark" style="letter-spacing: -0.5px;">Edit Kategori</h3>
                            <p class="text-muted m-0 small">Ubah nama kategori sesuai kebutuhan Anda.</p>
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
                                <i class="fa-solid fa-pen-nib text-muted small"></i>
                                <label class="form-label">Nama Kategori</label>
                            </div>
                            <input type="text" name="nama_kategori" class="form-control" value="<?= htmlspecialchars($d['nama_kategori']); ?>" required autofocus>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
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
        // ==========================================
        // TAMBAHAN FITUR INTERAKTIF POP-UP
        // ==========================================

        // 1. Pop-up Error Otomatis jika validasi database mendeteksi kegagalan
        <?php if(!empty($error_message)): ?>
        Swal.fire({
            icon: 'error',
            title: 'Gagal Memperbarui!',
            text: '<?= $error_message ?>',
            confirmButtonColor: '#f59e0b'
        });
        <?php endif; ?>

        // 2. Pop-up Konfirmasi saat Kasir mengklik tombol "Batal"
        document.querySelector('.btn-batal').addEventListener('click', function(e) {
            e.preventDefault(); // Menahan link asal agar tidak langsung pindah halaman
            const urlTujuan = this.getAttribute('href');

            Swal.fire({
                title: 'Batalkan Perubahan?',
                text: "Perubahan nama kategori yang Anda ketik tidak akan disimpan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#64748b',
                cancelButtonColor: '#f59e0b',
                confirmButtonText: 'Ya, Batal',
                cancelButtonText: 'Lanjutkan Edit'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = urlTujuan; // Pindah ke index.php jika konfirmasi batal
                }
            });
        });
    </script>
</body>
</html>