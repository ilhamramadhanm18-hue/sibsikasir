<?php
session_start();
include "config/koneksi.php";

if (!isset($_SESSION['nama']) || trim($_SESSION['nama']) == '') {
    header("Location: login.php");
    exit;
}

$username_aktif = $_SESSION['username'];
$nama_aktif     = $_SESSION['nama'];

$queryBarang    = mysqli_query($conn, "SELECT COUNT(*) as total FROM barang WHERE username='$username_aktif'");
$jBarang        = mysqli_fetch_assoc($queryBarang);

$queryKategori  = mysqli_query($conn, "SELECT COUNT(*) as total FROM kategori WHERE username='$username_aktif'");
$jKategori      = mysqli_fetch_assoc($queryKategori);

$queryTransaksi = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE kasir='$nama_aktif'");
$jTransaksi     = mysqli_fetch_assoc($queryTransaksi);

$queryOmzet     = mysqli_query($conn, "SELECT SUM(total) as total FROM transaksi WHERE kasir='$nama_aktif'");
$omzet          = mysqli_fetch_assoc($queryOmzet);

$omzet_tampil   = (int)($omzet['total'] ?? 0);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard SIBSIKASIR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #f4f7fe; 
            color: #2d3748;
        }

        .navbar {
            background: linear-gradient(90deg, #198754 0%, #157347 100%) !important;
            backdrop-filter: blur(10px);
            padding: 1rem 0;
        }

        .live-clock { 
            font-weight: 700; 
            color: #198754; 
            background: #ffffff; 
            padding: 5px 15px; 
            border-radius: 50px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .card { 
            border: none; 
            border-radius: 20px; 
            background: #ffffff;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05); 
            transition: all 0.4s ease;
        }

        .card:hover { 
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        .card-body h5 { 
            font-size: 0.85rem; 
            text-transform: uppercase; 
            letter-spacing: 1.5px; 
            color: #a0aec0; 
            font-weight: 700; 
        }

        .card h1 { font-weight: 800; color: #2d3748; }

        .btn { border-radius: 12px; font-weight: 600; padding: 10px 20px; transition: all 0.3s; }
        .btn-lg { padding: 15px 35px; font-size: 1.1rem; }
        
        .card-header {
            background: #ffffff !important;
            border-bottom: 2px solid #edf2f7;
            padding: 20px !important;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">SIBSIKASIR</a>
            <div class="text-white d-flex align-items-center gap-3">
                <div class="live-clock" id="clock">00:00:00</div>
                <div>
                    <span>Halo, <strong class="text-warning"><?= htmlspecialchars($_SESSION['nama']); ?></strong></span>
                    <a href="logout.php" class="btn btn-outline-light btn-sm fw-bold ms-2">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row g-4">
            <div class="col-md-3"><div class="card text-center p-2"><div class="card-body"><h5>Barang</h5><h1 class="my-3"><?= $jBarang['total']; ?></h1><a href="barang/index.php" class="btn btn-outline-success w-100">Kelola Barang</a></div></div></div>
            <div class="col-md-3"><div class="card text-center p-2"><div class="card-body"><h5>Kategori</h5><h1 class="my-3"><?= $jKategori['total']; ?></h1><a href="kategori/index.php" class="btn btn-outline-primary w-100">Kelola Kategori</a></div></div></div>
            <div class="col-md-3"><div class="card text-center p-2"><div class="card-body"><h5>Transaksi</h5><h1 class="my-3"><?= $jTransaksi['total']; ?></h1><a href="transaksi/riwayat.php" class="btn btn-outline-warning w-100">Lihat Riwayat</a></div></div></div>
            <div class="col-md-3"><div class="card text-center p-2"><div class="card-body"><h5>Omzet</h5><h3 class="my-3 text-success">Rp <?= number_format($omzet_tampil, 0, ',', '.'); ?></h3><div class="text-muted small">Total Pendapatan</div></div></div></div>
        </div>

        <div class="card shadow-sm mt-5">
            <div class="card-header fw-bold text-secondary">⚡ Navigasi Menu Cepat</div>
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex gap-2">
                    <a href="barang/index.php" class="btn btn-success px-4">Menu Barang</a>
                    <a href="kategori/index.php" class="btn btn-primary px-4">Menu Kategori</a>
                    <a href="transaksi/riwayat.php" class="btn btn-info px-4 text-white">Laporan Riwayat</a>
                </div>
                <a href="transaksi/index.php" class="btn btn-warning btn-lg fw-bold text-dark shadow">🛒 Buka Mesin Kasir</a>
            </div>
        </div>
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            document.getElementById('clock').textContent = now.toLocaleTimeString('id-ID');
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>