<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['nama']) || trim($_SESSION['nama']) == '') {
    header("Location: ../login.php");
    exit;
}

// PERBAIKAN UTAMA: Menggunakan nama lengkap agar singkron dengan data di database & dashboard
$nama_aktif = $_SESSION['nama'];
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'hari_ini';
$tgl_mulai = isset($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : '';
$tgl_selesai = isset($_GET['tgl_selesai']) ? $_GET['tgl_selesai'] : '';

// Mengubah filter kriteria pencarian dari username menjadi nama_aktif
if ($filter == 'kustom' && !empty($tgl_mulai)) {
    if (!empty($tgl_selesai)) {
        $query = "SELECT * FROM transaksi WHERE DATE(tanggal) BETWEEN '$tgl_mulai' AND '$tgl_selesai' AND kasir = '$nama_aktif' ORDER BY id DESC";
        $judul_sub = "Periode: " . date('d/m/Y', strtotime($tgl_mulai)) . " - " . date('d/m/Y', strtotime($tgl_selesai));
    } else {
        $query = "SELECT * FROM transaksi WHERE DATE(tanggal) = '$tgl_mulai' AND kasir = '$nama_aktif' ORDER BY id DESC";
        $judul_sub = "Tanggal: " . date('d/m/Y', strtotime($tgl_mulai));
    }
} else if ($filter == '1_bulan') {
    $query = "SELECT * FROM transaksi WHERE tanggal >= NOW() - INTERVAL 1 MONTH AND kasir = '$nama_aktif' ORDER BY id DESC";
    $judul_sub = "Seluruh transaksi 30 hari terakhir";
} else {
    $filter = 'hari_ini';
    $query = "SELECT * FROM transaksi WHERE DATE(tanggal) = CURDATE() AND kasir = '$nama_aktif' ORDER BY id DESC";
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
            background: #ffffff;
        }

        .btn-filter {
            border-radius: 12px;
            font-weight: 600;
            padding: 10px 24px;
            transition: all 0.3s ease;
        }

        .table thead {
            background: #f1f5f9;
            color: #475569;
            font-weight: 700;
        }

        .table thead th {
            padding: 16px;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            border-bottom: none;
        }

        .table tbody td {
            padding: 18px 16px;
            border-bottom: 1px solid #f1f5f9;
        }

        .table-hover tbody tr:hover {
            background-color: #f8fafc;
        }

        .badge-kasir {
            background: #f0fdf4;
            color: #16a34a;
            font-weight: 700;
            font-size: 0.85rem;
            border: 1px solid rgba(22, 163, 74, 0.15);
        }

        .btn-action {
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 6px 14px;
        }

        .form-control {
            border-radius: 12px;
            padding: 10px 14px;
            border: 1px solid #e2e8f0;
        }

        .form-control:focus {
            border-color: #198754;
            box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.15);
        }

        .btn-cari {
            border-radius: 12px;
            padding: 10px;
        }

        .btn-kembali {
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #475569;
            transition: all 0.3s;
        }

        .btn-kembali:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        /* Gaya Badge Pembayaran */
        .badge-pembayaran {
            font-weight: 700;
            font-size: 0.8rem;
            padding: 6px 12px;
            border-radius: 8px;
        }

        .badge-qris {
            background-color: #e0f2fe;
            color: #0369a1;
            border: 1px solid rgba(3, 105, 161, 0.15);
        }

        .badge-tunai {
            background-color: #fef3c7;
            color: #b45309;
            border: 1px solid rgba(180, 83, 9, 0.15);
        }
    </style>
</head>

<body>
    <div class="container py-5">

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h3 class="fw-bold m-0 d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-bar-graph text-success"></i> Laporan Riwayat Transaksi
                </h3>
                <p class="text-muted small m-0 mt-1 bg-white px-3 py-1.5 rounded-pill shadow-sm d-inline-block">
                    <i class="bi bi-funnel me-1 text-success"></i> <?= $judul_sub ?>
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="../dashboard.php" class="btn btn-kembali rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                </a>
                <a href="index.php" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-shop" style="color: #000000"></i> Buka Mesin Kasir
                </a>
            </div>
        </div>

        <div class="card p-4 mb-4 shadow-sm">
            <div class="row g-4 align-items-end">
                <div class="col-lg-4">
                    <label class="form-label text-secondary small fw-bold mb-2"><i class="bi bi-lightning-charge"></i> Pilihan Cepat</label>
                    <div class="d-flex gap-2">
                        <a class="btn <?= $filter == 'hari_ini' ? 'btn-success text-white shadow-sm' : 'btn-outline-secondary' ?> btn-filter w-50" href="riwayat.php?filter=hari_ini">Hari Ini</a>
                        <a class="btn <?= $filter == '1_bulan' ? 'btn-success text-white shadow-sm' : 'btn-outline-secondary' ?> btn-filter w-50" href="riwayat.php?filter=1_bulan">1 Bulan</a>
                    </div>
                </div>

                <div class="col-lg-8">
                    <form method="GET" action="riwayat.php" class="row g-2 align-items-end">
                        <input type="hidden" name="filter" value="kustom">
                        <div class="col-sm-5">
                            <label class="form-label text-secondary small fw-bold mb-2"><i class="bi bi-calendar-event"></i> Mulai</label>
                            <input type="date" name="tgl_mulai" class="form-control" value="<?= $tgl_mulai ?>" required>
                        </div>
                        <div class="col-sm-5">
                            <label class="form-label text-secondary small fw-bold mb-2"><i class="bi bi-calendar-check"></i> Sampai</label>
                            <input type="date" name="tgl_selesai" class="form-control" value="<?= $tgl_selesai ?>">
                        </div>
                        <div class="col-sm-2">
                            <button type="submit" class="btn btn-warning btn-cari w-100 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-search"></i> Cari
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table align-middle table-hover m-0">
                    <thead>
                        <tr>
                            <th class="ps-4" width="90">ID NOTA</th>
                            <th width="180">Waktu Transaksi</th>
                            <th width="180">Nama Kasir</th>
                            <th width="160">Metode</th>
                            <th>Total Pembayaran</th>
                            <th class="text-center pe-4" width="180">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($data) > 0) {
                            while ($d = mysqli_fetch_assoc($data)) { ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">#<?= $d['id'] ?></td>
                                    <td class="text-secondary fw-semibold small">
                                        <i class="bi bi-clock text-muted me-1"></i> <?= date('d/m/Y H:i', strtotime($d['tanggal'])) ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-kasir px-3 py-2 rounded-pill d-inline-flex align-items-center gap-1.5">
                                            <i class="bi bi-person-fill fs-6"></i> <?= htmlspecialchars($d['kasir']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (strtoupper($d['metode_pembayaran'] ?? 'TUNAI') == 'QRIS') { ?>
                                            <span class="badge badge-pembayaran badge-qris d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-qr-code-scan"></i> QRIS
                                            </span>
                                        <?php } else { ?>
                                            <span class="badge badge-pembayaran badge-tunai d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-cash-stack"></i> TUNAI
                                            </span>
                                        <?php } ?>
                                    </td>
                                    <td class="fw-bold text-success fs-5">Rp <?= number_format($d['total'], 0, ',', '.') ?></td>
                                    <td class="text-center pe-4">
                                        <div class="d-flex justify-content-center gap-1.5">
                                            <a href="detail.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-outline-primary btn-action">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                            <a href="hapus_riwayat.php?id=<?= $d['id'] ?>&filter=<?= $filter ?>&tgl_mulai=<?= $tgl_mulai ?>&tgl_selesai=<?= $tgl_selesai ?>" class="btn btn-sm btn-outline-danger btn-action btn-hapus">
                                                <i class="bi bi-trash"></i> Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-exclamation-circle text-warning fs-1 d-block mb-2"></i>
                                    <span class="fw-semibold">Data riwayat transaksi tidak ditemukan.</span>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // 1. Ambil parameter status dari URL browser
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');

        // 2. Munculkan popup SweetAlert2 berdasarkan status tanggapan server
        if (status === 'hapus_sukses') {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Riwayat transaksi telah dihapus dari sistem.',
                icon: 'success',
                confirmButtonColor: '#198754'
            }).then(() => {
                // Bersihkan parameter status di URL agar popup tidak muncul lagi saat di-refresh
                urlParams.delete('status');
                window.history.replaceState({}, '', `${window.location.pathname}?${urlParams}`);
            });
        } else if (status === 'pw_salah') {
            Swal.fire({
                title: 'Gagal Menghapus!',
                text: 'Password konfirmasi yang Anda masukkan salah.',
                icon: 'error',
                confirmButtonColor: '#dc3545'
            }).then(() => {
                urlParams.delete('status');
                window.history.replaceState({}, '', `${window.location.pathname}?${urlParams}`);
            });
        }

        // 3. Trigger Konfirmasi Hapus + Input Password (Codingan Anda sebelumnya)
        $('.btn-hapus').on('click', function(e) {
            e.preventDefault();
            const urlHapus = $(this).attr('href');

            Swal.fire({
                title: 'Hapus Transaksi?',
                text: "Data rincian nota akan dihapus permanen. Masukkan password Anda untuk konfirmasi:",
                icon: 'warning',
                input: 'password',
                inputAttributes: {
                    autocapitalize: 'off',
                    autocorrect: 'off',
                    autocomplete: 'new-password', // Ini trik untuk mencegah browser membaca sebagai form login
                    placeholder: 'Masukkan password konfirmasi'
                },
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Verifikasi & Hapus!',
                cancelButtonText: 'Batal',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Password wajib diisi!'
                    }
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    window.location.href = urlHapus + "&password_verifikasi=" + encodeURIComponent(result.value);
                }
            });
        });
    </script>
</body>

</html>