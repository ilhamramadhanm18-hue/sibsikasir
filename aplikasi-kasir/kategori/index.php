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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f4f7fe; color: #2d3748; }
        .card { border: none; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); padding: 20px; }
        .table thead th { background: #f8fafc !important; color: #718096; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; border: none; padding: 20px; }
        .table tbody td { padding: 20px; border-bottom: 1px solid #f1f5f9; }
        .btn-modern { border-radius: 12px; font-weight: 600; padding: 10px 20px; transition: 0.3s; }
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
                <h2 class="fw-bold">🗂️ Manajemen Kategori</h2>
                <p class="text-muted">Kelompokkan produk Anda agar lebih terorganisir.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="../dashboard.php" class="btn btn-outline-secondary btn-modern">⬅️ Kembali</a>
                <a href="tambah.php" class="btn btn-primary btn-modern shadow-sm">➕ Tambah Kategori</a>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th width="10%">No</th>
                            <th>Nama Kategori</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; if(mysqli_num_rows($data) > 0) { while ($d = mysqli_fetch_assoc($data)) { ?>
                            <tr>
                                <td class="fw-bold text-muted"><?= $no++; ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($d['nama_kategori']); ?></td>
                                <td class="text-center">
                                    <a href="edit.php?id=<?= $d['id']; ?>" class="btn btn-edit btn-sm px-3 rounded-pill">Edit</a>
                                    <button onclick="hapusKategori(<?= $d['id']; ?>, '<?= htmlspecialchars($d['nama_kategori'], ENT_QUOTES); ?>')" class="btn btn-hapus btn-sm px-3 rounded-pill">Hapus</button>
                                </td>
                            </tr>
                        <?php }} else { ?>
                            <tr><td colspan="3" class="text-center py-5 text-muted">Belum ada kategori.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function hapusKategori(id, nama) {
            Swal.fire({
                title: 'Hapus Kategori?',
                text: "Kategori '" + nama + "' akan dihapus.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) { window.location.href = 'hapus.php?id=' + id; }
            });
        }

        // Notifikasi SweetAlert
        <?php if (isset($_GET['status'])): ?>
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Data kategori telah diperbarui.', showConfirmButton: false, timer: 1500 });
            window.history.replaceState({}, document.title, window.location.pathname);
        <?php endif; ?>
    </script>
</body>
</html>