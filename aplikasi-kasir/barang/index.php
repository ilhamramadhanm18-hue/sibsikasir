<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['nama']) || trim($_SESSION['nama']) == '') {
    header("Location: ../login.php");
    exit;
}

$username_aktif = $_SESSION['username'];

$query = "SELECT barang.*, kategori.nama_kategori 
          FROM barang 
          LEFT JOIN kategori ON barang.id_kategori = kategori.id
          WHERE barang.username = ? ORDER BY barang.id DESC";
          
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
    <title>Data Barang - SIBSIKASIR</title>
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

        /* Interactive Badges and Actions */
        .badge-kat { 
            background: #e0e7ff; 
            color: #4f46e5; 
            padding: 6px 14px; 
            border-radius: 10px; 
            font-weight: 700; 
            font-size: 0.75rem; 
            letter-spacing: 0.3px;
            display: inline-block;
        }
        
        .badge-stok {
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 10px;
            font-size: 0.85rem;
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
                    <i class="fa-solid fa-boxes-stacked text-primary"></i> Manajemen Barang
                </h2>
                <p class="text-muted m-0 mt-1">Kelola inventaris produk dan pantau ketersediaan stok Anda di sini.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="../dashboard.php" class="btn btn-outline-secondary btn-modern">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
                <a href="tambah.php" class="btn btn-primary btn-modern shadow-sm bg-gradient">
                    <i class="fa-solid fa-plus"></i> Tambah Produk
                </a>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th class="text-center" style="width: 120px;">Stok</th>
                            <th class="text-center" style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while ($d = mysqli_fetch_assoc($data)) { ?>
                            <tr>
                                <td class="fw-bold text-muted"><?= $no++; ?></td>
                                <td>
                                    <div class="fw-bold text-dark text-capitalize"><?= htmlspecialchars($d['nama_barang']); ?></div>
                                    <div class="text-muted mt-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                        <span class="badge bg-light text-secondary border">Kode: BRG<?= sprintf('%03d', $d['id']); ?></span>
                                    </div>
                                </td>
                                <td><span class="badge-kat text-uppercase"><?= htmlspecialchars($d['nama_kategori'] ?? 'Tanpa Kategori'); ?></span></td>
                                <td class="fw-bold text-success">Rp <?= number_format($d['harga'], 0, ',', '.'); ?></td>
                                <td class="text-center">
                                    <?php if($d['stok'] <= 5) { ?>
                                        <span class="badge-stok bg-danger-subtle text-danger"><i class="fa-solid fa-exclamation-triangle"></i> <?= $d['stok']; ?> Pcs</span>
                                    <?php } else { ?>
                                        <span class="badge-stok bg-success-subtle text-success"><?= $d['stok']; ?> Pcs</span>
                                    <?php } ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-2">
                                        <a href="edit.php?id=<?= $d['id']; ?>" class="btn btn-edit btn-modern-sm">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </a>
                                        <button onclick="konfirmasiHapus(<?= $d['id']; ?>, '<?= htmlspecialchars($d['nama_barang'], ENT_QUOTES); ?>')" class="btn btn-hapus btn-modern-sm">
                                            <i class="fa-solid fa-trash-can"></i> Hapus
                                        </button>
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
        function konfirmasiHapus(id, nama) {
            Swal.fire({
                title: 'Hapus Produk?',
                text: "Produk " + nama + " akan dihapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'border-radius-20'
                }
            }).then((result) => {
                if (result.isConfirmed) { window.location.href = 'hapus.php?id=' + id; }
            });
        }

        const urlParams = new URLSearchParams(window.location.search);
        const statusAction = urlParams.get('status');

        if (statusAction === 'tambah_sukses' || statusAction === 'sukses_tambah') {
            Swal.fire({
                icon: 'success',
                title: 'Produk Ditambahkan!',
                text: 'Data inventaris baru berhasil disimpan ke sistem.',
                timer: 2200,
                showConfirmButton: false
            });
        } else if (statusAction === 'sukses_edit') {
            Swal.fire({
                icon: 'success',
                title: 'Perubahan Disimpan!',
                text: 'Data produk berhasil diperbarui dengan sukses.',
                timer: 2200,
                showConfirmButton: false
            });
        } else if (statusAction === 'sukses_hapus') {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil Dihapus!',
                text: 'Produk pilihan Anda telah dihapus.',
                timer: 2200,
                showConfirmButton: false
            });
        }
    </script>
</body>
</html>