<?php
session_start();
include "../config/koneksi.php";

$id = mysqli_real_escape_string($conn, $_GET['id']);
$queryTx = mysqli_query($conn, "SELECT * FROM transaksi WHERE id='$id'");
$tx = mysqli_fetch_assoc($queryTx);

$queryBarang = mysqli_query($conn, "SELECT detail_transaksi.*, barang.nama_barang 
                                    FROM detail_transaksi 
                                    JOIN barang ON detail_transaksi.id_barang = barang.id 
                                    WHERE detail_transaksi.no_nota='$id'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk #<?= $id ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print { 
            .no-print { display: none !important; }
            body { background: #fff !important; padding: 0 !important; }
            .struk-card { box-shadow: none !important; border: 1px dashed #ccc !important; }
        }
        body { background-color: #f1f5f9; font-family: 'Courier New', Courier, monospace; }
        .struk-card { max-width: 350px; margin: 40px auto; background: #fff; border-radius: 0; padding: 20px; border: 1px solid #e2e8f0; }
        .divider { border-top: 1px dashed #333; margin: 10px 0; }
        .table-items { font-size: 0.85rem; }
        .total-box { font-size: 1.2rem; border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 10px 0; margin: 15px 0; }
    </style>
</head>
<body>
<div class="container">
    <div class="struk-card">
        <div class="text-center mb-3">
            <h5 class="fw-bold m-0">TOKO KASTURI</h5>
            <small class="text-muted">Jln.Dr Tazar Rt.12 Kec.Telanai Pura Kel.Buluran Kenali</small>
        </div>
        <div class="divider"></div>
        <div class="small">
            <div>ID: #<?= $tx['id'] ?></div>
            <div>Waktu: <?= $tx['tanggal'] ?></div>
            <div>Kasir: <?= htmlspecialchars($tx['kasir']) ?></div>
        </div>
        <div class="divider"></div>
        
        <table class="table table-sm table-borderless table-items">
            <tbody>
                <?php while($b = mysqli_fetch_assoc($queryBarang)) { ?>
                <tr>
                    <td><?= htmlspecialchars($b['nama_barang']) ?><br><small class="text-muted"><?= $b['qty'] ?> x</small></td>
                    <td class="text-end align-middle">Rp <?= number_format($b['subtotal'], 0, ',', '.') ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="total-box d-flex justify-content-between">
            <span class="fw-bold">TOTAL</span>
            <span class="fw-bold">Rp <?= number_format($tx['total'], 0, ',', '.') ?></span>
        </div>

        <div class="small">
            <div class="d-flex justify-content-between">
                <span>Metode:</span>
                <span><?= strtoupper($tx['metode_pembayaran'] ?? 'TUNAI') ?></span>
            </div>
            <?php if (strtolower($tx['metode_pembayaran'] ?? '') !== 'qris') { 
                $kembalian = (int)$tx['uang_bayar'] - (int)$tx['total']; ?>
                <div class="d-flex justify-content-between">
                    <span>Bayar:</span>
                    <span>Rp <?= number_format((int)$tx['uang_bayar'], 0, ',', '.') ?></span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Kembali:</span>
                    <span>Rp <?= number_format(max(0, $kembalian), 0, ',', '.') ?></span>
                </div>
            <?php } ?>
        </div>

        <div class="divider"></div>
        <div class="text-center small mt-3">
            <p>Terima Kasih!<br>Barang yang dibeli tidak dapat ditukar.</p>
        </div>

        <div class="d-grid gap-2 mt-4 no-print">
            <button onclick="window.print()" class="btn btn-dark btn-sm">🖨️ Cetak Struk</button>
            <div class="row g-2">
                <div class="col"><a href="index.php" class="btn btn-outline-secondary btn-sm w-100">🛒 Transaksi Baru</a></div>
                <div class="col"><a href="../dashboard.php" class="btn btn-outline-secondary btn-sm w-100">⬅️ Menu</a></div>
            </div>
        </div>
    </div>
</div>
</body>
</html>