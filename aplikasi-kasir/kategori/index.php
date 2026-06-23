<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['nama']) || trim($_SESSION['nama']) == '') {
    header("Location: ../login.php");
    exit;
}

$username_aktif = $_SESSION['username'];

$query = "SELECT * FROM kategori WHERE username = ? ORDER BY id DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $username_aktif);
mysqli_stmt_execute($stmt);
$data = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kategori - SIBSIKASIR</title>
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
        
        /* Glassmorphism Header & Card Styling */
        .page-header {
            background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%);
            padding: 30px;
            border-radius: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.7);
        }
        
        .card { 
            border: none; 
            border-radius: 24px; 
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03); 
            background: #ffffff; 
            padding: 24px;
            border: 1px solid #e2e8f0;
        }

        /* Modernized Table */
        .table-responsive {
            border-radius: 16px;
            overflow: hidden;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead th { 
            background: #f1f5f9 !important; 
            color: #64748b; 
            font-size: 0.8rem; 
            text-transform: uppercase; 
            letter-spacing: 1.2px; 
            border: none; 
            padding: 18px 20px; 
            font-weight: 700;
        }
        
        .table tbody td { 
            padding: 18px 20px; 
            border-bottom: 1px solid #f1f5f9; 
            color: #334155;
        }
        
        .table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .table tbody tr {
            transition: all 0.2s ease;
        }
        
        .table tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Button Customizations */
        .btn-modern { 
            border-radius: 14px; 
            font-weight: 600; 
            padding: 12px 24px; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }
        
        .btn-modern-sm {
            border-radius: 10px;
            font-weight: 600;
            padding: 7px 16px;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-kat { 
            background: #e0e7ff; 
            color: #4f46e5; 
            padding: 6px 14px; 
            border-radius: 10px; 
            font-weight: 700; 
            font-size: 0.85rem; 
            letter-spacing: 0.3px;
            display: inline-block;
        }
        
        .btn-edit { 
            background: #fef9c3; 
            color: #854d0e; 
            border: 1px solid #fef08a; 
        }
        
        .btn-edit:hover { 
            background: #fef08a; 
            color: #854d0e;
            transform: scale(1.05);
        }
        
        .btn-hapus { 
            background: #fee2e2; 
            color: #991b1b; 
            border: 1px solid #fecaca; 
        }
        
        .btn-hapus:hover { 
            background: #fecaca; 
            color: #991b1b;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-5">
            <div>
                <h2 class="fw-800 m-0 text-dark d-flex align-items-center gap-2">
                    <i class="fa-solid fa-layer-group text-primary"></i> Manajemen Kategori
                </h2>
                <p class="text-muted m-0 mt-1">Kelompokkan produk Anda agar sistem kasir lebih terorganisir.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="../dashboard.php" class="btn btn-outline-secondary btn-modern">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
                <a href="tambah.php" class="btn btn-primary btn-modern shadow-sm bg-gradient">
                    <i class="fa-solid fa-plus"></i> Tambah Kategori
                </a>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th width="10%">No</th>
                            <th>Nama Kategori</th>
                            <th class="text-center" width="250px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; if(mysqli_num_rows($data) > 0) { while ($d = mysqli_fetch_assoc($data)) { ?>
                            <tr>
                                <td class="fw-bold text-muted"><?= $no++; ?></td>
                                <td>
                                    <span class="badge-kat text-uppercase">
                                        <i class="fa-solid fa-folder-open me-1 opacity-70"></i> <?= htmlspecialchars($d['nama_kategori']); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-2">
                                        <a href="edit.php?id=<?= $d['id']; ?>" class="btn btn-edit btn-modern-sm">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </a>
                                        <button onclick="hapusKategori(<?= $d['id']; ?>, '<?= htmlspecialchars($d['nama_kategori'], ENT_QUOTES); ?>')" class="btn btn-hapus btn-modern-sm">
                                            <i class="fa-solid fa-trash-can"></i> Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php }} else { ?>
                            <tr>
                                <td colspan="3" class="text-center py-5 text-muted">
                                    <div class="py-4">
                                        <i class="fa-solid fa-folder-tree fa-3x mb-3 text-secondary opacity-30"></i>
                                        <p class="m-0 fw-semibold">Belum ada kategori terdaftar.</p>
                                        <small class="text-muted">Klik tombol 'Tambah Kategori' di atas untuk memulai.</small>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Logika konfirmasi hapus bawaan Anda tetap utuh
        function hapusKategori(id, nama) {
            Swal.fire({
                title: 'Hapus Kategori?',
                text: "Kategori '" + nama + "' akan dihapus.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) { window.location.href = 'hapus.php?id=' + id; }
            });
        }

        // =======================================================
        // UPGRADE SINKRONISASI NOTIFIKASI BERDASARKAN STATUS ACTION
        // =======================================================
        <?php if (isset($_GET['status'])): 
            $status = $_GET['status'];
            $title = "Berhasil!";
            $text = "Data kategori telah diperbarui.";
            $icon = "success"; // Ikon dasar diset sukses

            // Percabangan pesan dinamis agar sesuai dengan aksi yang dilakukan
            if ($status == 'tambah_sukses' || $status == 'sukses_tambah') {
                $title = "Kategori Ditambahkan!";
                $text = "Kategori baru berhasil disimpan ke sistem.";
            } elseif ($status == 'sukses_edit') {
                $title = "Perubahan Disimpan!";
                $text = "Nama kategori berhasil diperbarui dengan sukses.";
            } elseif ($status == 'sukses_hapus') {
                $title = "Berhasil Dihapus!";
                $text = "Kategori pilihan Anda telah dihapus.";
            } elseif ($status == 'gagal_hapus') { // TAMBAHAN LOGIKA BARU UNTUK GEJALA GAGAL HAPUS
                $title = "Gagal Menghapus!";
                $text = "Kategori gagal dihapus karena kendala sistem atau database.";
                $icon = "error"; // Mengubah warna pop-up menjadi merah tanda silang
            }
        ?>
            Swal.fire({ 
                icon: '<?= $icon ?>', 
                title: '<?= $title ?>', 
                text: '<?= $text ?>', 
                showConfirmButton: <?= ($icon == 'error') ? 'true' : 'false' ?>, // Munculkan tombol OK jika error agar bisa dibaca user
                confirmButtonColor: '#0d6efd',
                timer: <?= ($icon == 'error') ? 'null' : '2200' ?> // Jika terjadi error jangan ditutup otomatis sebelum diklik OK
            });
            // Membersihkan parameter status di URL tanpa me-refresh halaman
            window.history.replaceState({}, document.title, window.location.pathname);
        <?php endif; ?>
    </script>
</body>
</html>