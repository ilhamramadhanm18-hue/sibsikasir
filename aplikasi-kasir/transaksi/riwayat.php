<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['nama']) || trim($_SESSION['nama']) == '') {
    header("Location: ../login.php");
    exit;
}

$username_aktif = $_SESSION['username'];
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'hari_ini';
$tgl_mulai = isset($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : '';
$tgl_selesai = isset($_GET['tgl_selesai']) ? $_GET['tgl_selesai'] : '';

if ($filter == 'kustom' && !empty($tgl_mulai)) {
    if (!empty($tgl_selesai)) {
        $query = "SELECT * FROM transaksi WHERE DATE(tanggal) BETWEEN '$tgl_mulai' AND '$tgl_selesai' ORDER BY id DESC";
        $judul_sub = "Periode: " . date('d/m/Y', strtotime($tgl_mulai)) . " - " . date('d/m/Y', strtotime($tgl_selesai));
    } else {
        $query = "SELECT * FROM transaksi WHERE DATE(tanggal) = '$tgl_mulai' ORDER BY id DESC";
        $judul_sub = "Tanggal: " . date('d/m/Y', strtotime($tgl_mulai));
    }
} else if ($filter == '1_bulan') {
    $query = "SELECT * FROM transaksi WHERE tanggal >= NOW() - INTERVAL 1 MONTH ORDER BY id DESC";
    $judul_sub = "Seluruh transaksi 30 hari terakhir";
} else {
    $filter = 'hari_ini';
    $query = "SELECT * FROM transaksi WHERE DATE(tanggal) = CURDATE() ORDER BY id DESC";
    $judul_sub = "Transaksi hari ini";
}

$data = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - SIBSIKASIR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; color: #334155; }
        .card { border: none; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .card-header { background: white; border-bottom: 1px solid #f1f5f9; padding: 20px; font-weight: 700; border-radius: 16px 16px 0 0 !important; }
        .btn-filter { border-radius: 10px; font-weight: 600; padding: 8px 20px; transition: all 0.3s; }
        .table thead { background: #f8fafc; color: #64748b; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; }
        .badge-kasir { background: #f1f5f9; color: #475569; font-weight: 600; }
        .btn-action { border-radius: 8px; font-weight: 600; font-size: 0.85rem; }
    </style>
</head>
<body>
<div class="container py-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold m-0">Riwayat Transaksi</h3>
            <p class="text-muted small"><?= $judul_sub ?></p>
        </div>
        <a href="../dashboard.php" class="btn btn-light rounded-pill px-4 fw-bold">⬅️ Kembali</a>
    </div>

    <div class="card p-4 mb-4">
        <div class="row g-4 align-items-end">
            <div class="col-lg-4">
                <label class="form-label text-muted small">Pilihan Cepat</label>
                <div class="d-flex gap-2">
                    <a class="btn <?= $filter == 'hari_ini' ? 'btn-success' : 'btn-outline-secondary' ?> btn-filter" href="riwayat.php?filter=hari_ini">Hari Ini</a>
                    <a class="btn <?= $filter == '1_bulan' ? 'btn-success' : 'btn-outline-secondary' ?> btn-filter" href="riwayat.php?filter=1_bulan">1 Bulan</a>
                </div>
            </div>
            
            <div class="col-lg-8">
                <form method="GET" action="riwayat.php" class="row g-2 align-items-end">
                    <input type="hidden" name="filter" value="kustom">
                    <div class="col-sm-5">
                        <label class="form-label text-muted small">Mulai</label>
                        <input type="date" name="tgl_mulai" class="form-control" value="<?= $tgl_mulai ?>" required>
                    </div>
                    <div class="col-sm-5">
                        <label class="form-label text-muted small">Sampai</label>
                        <input type="date" name="tgl_selesai" class="form-control" value="<?= $tgl_selesai ?>">
                    </div>
                    <div class="col-sm-2">
                        <button type="submit" class="btn btn-warning w-100 fw-bold">Cari</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle m-0">
                <thead>
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Waktu</th>
                        <th>Kasir</th>
                        <th>Total</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($data) > 0) {
                        while($d = mysqli_fetch_assoc($data)){ ?>
                        <tr>
                            <td class="ps-4 fw-bold text-secondary">#<?= $d['id'] ?></td>
                            <td class="text-muted"><?= $d['tanggal'] ?></td>
                            <td><span class="badge badge-kasir px-3 py-2 rounded-pill">👤 <?= htmlspecialchars($d['kasir']) ?></span></td>
                            <td class="fw-bold text-success">Rp <?= number_format($d['total'], 0, ',', '.') ?></td>
                            <td class="text-center pe-4">
                                <a href="detail.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-outline-primary btn-action">Detail</a>
                                <a href="hapus_riwayat.php?id=<?= $d['id'] ?>&filter=<?= $filter ?>&tgl_mulai=<?= $tgl_mulai ?>&tgl_selesai=<?= $tgl_selesai ?>" class="btn btn-sm btn-outline-danger btn-action btn-hapus">Hapus</a>
                            </td>
                        </tr>
                    <?php } } else { ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">Data tidak ditemukan.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $('.btn-hapus').on('click', function(e) {
        e.preventDefault();
        const urlHapus = $(this).attr('href');
        Swal.fire({
            title: 'Hapus Transaksi?',
            text: "Data akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Ya, Hapus'
        }).then((result) => { if (result.isConfirmed) window.location.href = urlHapus; });
    });
</script>
</body>
</html>