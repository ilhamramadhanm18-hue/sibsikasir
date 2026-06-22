<?php
include "../config/koneksi.php";

// Sanitasi input ID untuk keamanan
$id = mysqli_real_escape_string($conn, $_GET['id']);

// Mengambil data utama transaksi
$queryTx = mysqli_query($conn, "SELECT * FROM transaksi WHERE id='$id'");
$tx = mysqli_fetch_assoc($queryTx);

// Mengambil semua detail barang untuk transaksi tersebut
$queryBarang = mysqli_query($conn, "SELECT detail_transaksi.*, barang.nama_barang 
                                    FROM detail_transaksi 
                                    JOIN barang ON detail_transaksi.id_barang = barang.id 
                                    WHERE detail_transaksi.id_transaksi='$id'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Transaksi #<?= $id ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; font-size: 12px; line-height: 1.2; }
        .struk { width: 250px; margin: 0 auto; }
        .divider { border-bottom: 1px dashed #000; margin: 5px 0; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="struk">
    <div class="text-center">
        <strong>SIBSIKASIR</strong><br>
        Jambi, Indonesia<br>
        <div class="divider"></div>
    </div>
    
    <div>
        ID: #<?= $tx['id'] ?><br>
        Tgl: <?= $tx['tanggal'] ?>
    </div>
    <div class="divider"></div>

    <table width="100%">
        <?php while($b = mysqli_fetch_assoc($queryBarang)) { ?>
        <tr>
            <td><?= $b['nama_barang'] ?> (<?= $b['qty'] ?>x)</td>
            <td class="text-right"><?= number_format($b['subtotal'], 0, ',', '.') ?></td>
        </tr>
        <?php } ?>
    </table>

    <div class="divider"></div>
    <table width="100%">
        <tr>
            <td><strong>TOTAL</strong></td>
            <td class="text-right"><strong>Rp <?= number_format($tx['total'], 0, ',', '.') ?></strong></td>
        </tr>
    </table>
    <div class="divider"></div>

    <div class="text-center">
        Terima Kasih<br>
        Silakan Datang Kembali
    </div>

    <div class="no-print" style="margin-top: 20px;">
        <button onclick="window.print()" style="width: 100%; padding: 10px; cursor: pointer;">🖨️ Cetak Struk</button>
    </div>
</div>

</body>
</html>