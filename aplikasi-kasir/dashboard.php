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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #f8fafc; 
            color: #1e293b;
        }

        /* UPGRADE: Desain Navbar Premium dengan Efek Glassmorphism */
        .navbar {
            background: rgba(25, 135, 84, 0.95) !important;
            backdrop-filter: blur(10px);
            padding: 0.85rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .navbar-brand {
            font-size: 1.4rem;
            letter-spacing: 0.5px;
        }

        .live-clock { 
            font-weight: 700; 
            color: #198754; 
            background: #ffffff; 
            padding: 6px 18px; 
            border-radius: 50px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            font-size: 0.95rem;
        }

        /* UPGRADE: Card Grid UI Baru dengan Gradasi Halus & Efek Hover Pop-Up */
        .card { 
            border: none; 
            border-radius: 24px; 
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.02); 
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }

        .card:hover { 
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        }

        /* UPGRADE: Ikon mengambang di pojok kanan atas background card */
        .card-icon-bg {
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 2.8rem;
            opacity: 0.12;
            transition: all 0.4s;
        }
        .card:hover .card-icon-bg {
            transform: scale(1.15) rotate(10deg);
            opacity: 0.2;
        }

        .card-body h5 { 
            font-size: 0.8rem; 
            text-transform: uppercase; 
            letter-spacing: 1.5px; 
            color: #64748b; 
            font-weight: 700; 
            margin-bottom: 4px;
        }

        .card h1 { font-weight: 800; color: #0f172a; }

        .btn { 
            border-radius: 14px; 
            font-weight: 600; 
            padding: 10px 20px; 
            transition: all 0.3s ease; 
        }

        .btn-action-card {
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #475569;
        }
        .btn-action-card:hover {
            background: #0f172a;
            color: #ffffff;
            border-color: #0f172a;
        }

        .btn-lg { padding: 16px 36px; font-size: 1.1rem; }
        
        .card-header {
            background: #ffffff !important;
            border-bottom: 1px solid #f1f5f9;
            padding: 22px 24px !important;
            font-size: 1.05rem;
        }

        /* Khusus highlight untuk widget Omzet agar tampak dominan */
        .card-omzet {
            background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%);
            border: 1px solid rgba(25, 135, 84, 0.1);
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="dashboard.php">
               <i class="bi bi-shop" style="color: #000000; font-size: 2.5rem;"></i> SIBSIKASIR
            </a>
            <div class="text-white d-flex align-items-center gap-3">
                <div class="live-clock" id="clock"><i class="bi bi-clock-fill me-2"></i>00:00:00</div>
                <div class="d-flex align-items-center gap-2">
                    <span class="d-none d-sm-inline opacity-75">Pemilik:</span>
                    <span class="badge bg-light text-dark px-3 py-2 rounded-pill fw-bold">
                        <i class="bi bi-person-circle text-success me-1"></i> <?= htmlspecialchars($_SESSION['nama']); ?>
                    </span>
                    <a href="logout.php" class="btn btn-danger btn-sm px-3 rounded-pill fw-bold ms-2 shadow-sm">
                        <i class="bi bi-box-arrow-right"></i> Keluar
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row mb-4">
            <div class="col-12">
                <div class="p-4 bg-white rounded-4 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h4 class="fw-bold mb-1">Selamat Datang Kembali, 👋</h4>
                    </div>
                    <span class="text-muted small fw-semibold bg-light px-3 py-2 rounded-3">
                        <i class="bi bi-calendar3 me-2"></i><?= date('d M Y'); ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-3">
                <div class="card p-2 h-100">
                    <i class="bi bi-box-seam card-icon-bg text-success"></i>
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5>Barang</h5>
                            <h1 class="my-3"><?= $jBarang['total']; ?></h1>
                        </div>
                        <a href="barang/index.php" class="btn btn-action-card w-100 mt-2">
                            <i class="bi bi-boxes me-2"></i>Kelola Barang
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card p-2 h-100">
                    <i class="bi bi-tags card-icon-bg text-primary"></i>
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5>Kategori</h5>
                            <h1 class="my-3"><?= $jKategori['total']; ?></h1>
                        </div>
                        <a href="kategori/index.php" class="btn btn-action-card w-100 mt-2">
                            <i class="bi bi-grid-1x2 me-2"></i>Kelola Kategori
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card p-2 h-100">
                    <i class="bi bi-receipt-cutoff card-icon-bg text-warning"></i>
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5>Transaksi</h5>
                            <h1 class="my-3"><?= $jTransaksi['total']; ?></h1>
                        </div>
                        <a href="transaksi/riwayat.php" class="btn btn-action-card w-100 mt-2">
                            <i class="bi bi-clock-history me-2"></i>Lihat Riwayat
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-omzet p-2 h-100">
                    <i class="bi bi-wallet2 card-icon-bg text-success"></i>
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5>Omzet Anda</h5>
                            <h2 class="my-3 text-success fw-bold">Rp <?= number_format($omzet_tampil, 0, ',', '.'); ?></h2>
                        </div>
                        <div class="text-muted small border-top pt-2 mt-2">
                            <i class="bi bi-graph-up-arrow text-success me-1"></i> Total pendapatan akun ini
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mt-5 mb-5">
            <div class="card-header fw-bold text-secondary d-flex align-items-center gap-2">
                <i class="bi bi-lightning-charge-fill text-warning"></i> Navigasi Menu Cepat
            </div>
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-4 p-4">
                <div class="d-flex gap-2 flex-wrap">
                    <a href="barang/index.php" class="btn btn-success px-4 py-2">
                        <i class="bi bi-box-seam me-2"></i>Menu Barang
                    </a>
                    <a href="kategori/index.php" class="btn btn-primary px-4 py-2">
                        <i class="bi bi-tags me-2"></i>Menu Kategori
                    </a>
                    <a href="transaksi/riwayat.php" class="btn btn-info px-4 py-2 text-white">
                        <i class="bi bi-file-earmark-bar-graph me-2"></i>Laporan Riwayat
                    </a>
                </div>
                <a href="transaksi/index.php" class="btn btn-warning btn-lg fw-bold text-dark shadow d-flex align-items-center gap-2">
                    <i class="bi bi-cart-dash-fill fs-5"></i> Buka Mesin Kasir
                </a>
            </div>
        </div>
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            
            // 1. Update Jam Real-Time
            document.getElementById('clock').innerHTML = '<i class="bi bi-clock-fill me-2"></i>' + now.toLocaleTimeString('id-ID');
            
            // 2. Update Tanggal Real-Time secara Otomatis (Sinkron dengan Jam Laptop)
            const opsiTanggal = { day: '2-digit', month: 'short', year: 'numeric' };
            const tanggalSekarang = now.toLocaleDateString('id-ID', opsiTanggal).replace(/\./g, ''); 
            
            // Cari elemen badge tanggal di banner lalu timpa teksnya langsung lewat JavaScript
            const elemenTanggal = document.querySelector('.welcome-banner .badge, .bg-white .rounded-3');
            if (elemenTanggal) {
                elemenTanggal.innerHTML = '<i class="bi bi-calendar3 text-success me-2"></i>' + tanggalSekarang;
            }
        }
        
        // Jalankan setiap 1 detik
        setInterval(updateClock, 1000);
        updateClock();
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>