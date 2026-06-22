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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f4f7fe; color: #2d3748; }
        .card { border: none; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); background: #ffffff; padding: 20px; }
        .table thead th { background: #f8fafc !important; color: #718096; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; border: none; padding: 20px; }
        .table tbody td { padding: 20px; border-bottom: 1px solid #f1f5f9; }
        .btn-modern { border-radius: 12px; font-weight: 600; padding: 10px 20px; transition: 0.3s; }
        .badge-kat { background: #eef2ff; color: #4f46e5; padding: 6px 14px; border-radius: 8px; font-weight: 600; font-size: 0.8rem; }
        .btn-edit { background: #fff3cd; color: #856404; border: none; }
        .btn-edit:hover { background: #ffeeba; }
        .btn-hapus { background: #fee2e2; color: #dc2626; border: none; }
        .btn-hapus:hover { background: #fecaca; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">📦 Manajemen Barang</h2>
                <p class="text-muted">Kelola inventaris produk Anda di sini.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="../dashboard.php" class="btn btn-outline-secondary btn-modern">⬅️ Kembali</a>
                <a href="tambah.php" class="btn btn-success btn-modern shadow-sm">➕ Tambah Produk</a>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th class="text-center">Stok</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while ($d = mysqli_fetch_assoc($data)) { ?>
                            <tr>
                                <td class="fw-bold text-muted"><?= $no++; ?></td>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($d['nama_barang']); ?></div>
                                    <small class="text-muted">ID: #<?= $d['id']; ?></small>
                                </td>
                                <td><span class="badge-kat"><?= htmlspecialchars($d['nama_kategori'] ?? 'Tanpa Kategori'); ?></span></td>
                                <td class="fw-bold text-success">Rp <?= number_format($d['harga'], 0, ',', '.'); ?></td>
                                <td class="text-center"><?= $d['stok']; ?> Pcs</td>
                                <td class="text-center">
                                    <a href="edit.php?id=<?= $d['id']; ?>" class="btn btn-edit btn-sm px-3 rounded-pill">Edit</a>
                                    <button onclick="konfirmasiHapus(<?= $d['id']; ?>, '<?= htmlspecialchars($d['nama_barang'], ENT_QUOTES); ?>')" class="btn btn-hapus btn-sm px-3 rounded-pill">Hapus</button>
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
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) { window.location.href = 'hapus.php?id=' + id; }
            });
        }
    </script>
</body>
</html>