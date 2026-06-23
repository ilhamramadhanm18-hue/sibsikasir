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
                                    WHERE detail_transaksi.no_nota='$id'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi #<?= $id ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Pengaturan Cetak Printer Thermal / Biasa */
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; padding: 0 !important; }
            .struk-card { box-shadow: none !important; border: none !important; margin: 0 auto !important; padding: 10px !important; }
        }
        
        body { 
            background-color: #f1f5f9; 
            font-family: 'JetBrains Mono', 'Courier New', Courier, monospace; 
            color: #1e293b;
        }
        
        .struk-card { 
            max-width: 380px; 
            margin: 50px auto; 
            background: #ffffff; 
            padding: 30px 25px; 
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.06);
            border: 1px solid #e2e8f0;
        }

        /* Dekorasi Header Struk */
        .shop-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #0f172a;
        }

        .shop-address {
            font-size: 0.75rem;
            color: #64748b;
            line-height: 1.4;
        }
        
        .divider { 
            border-top: 1px dashed #94a3b8; 
            margin: 15px 0; 
        }
        
        .info-details {
            font-size: 0.8rem;
            color: #475569;
            line-height: 1.6;
        }
        
        .table-items { 
            font-size: 0.82rem; 
        }
        
        .table-items td {
            padding: 6px 0;
            color: #1e293b;
        }
        
        .item-qty {
            color: #64748b;
            font-size: 0.78rem;
        }
        
        .total-box { 
            font-size: 1.15rem; 
            border-top: 2px dashed #0f172a; 
            border-bottom: 2px dashed #0f172a; 
            padding: 12px 0; 
            margin: 15px 0; 
            color: #0f172a;
        }

        .footer-thanks {
            font-size: 0.78rem;
            color: #64748b;
            line-height: 1.5;
        }

        /* Desain Tombol Kontrol */
        .btn-modern {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 600;
            border-radius: 12px;
            padding: 11px 16px;
            font-size: 0.88rem;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-modern:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
        }
    </style>
</head>
<body>

<div class="container px-3">
    <div class="struk-card">
        <div class="text-center mb-2">
            <h4 class="shop-title m-0">SIBSIKASIR</h4>
            <p class="shop-address mt-1 mb-0">Jambi, Indonesia</p>
        </div>
        
        <div class="divider"></div>
        
        <div class="info-details text-start">
            <div>ID   : #<?= $tx['id'] ?></div>
            <div>Tgl  : <?= $tx['tanggal'] ?></div>
        </div>
        
        <div class="divider"></div>

        <table class="table-items" width="100%">
            <tbody>
                <?php while($b = mysqli_fetch_assoc($queryBarang)) { ?>
                <tr>
                    <td class="align-top">
                        <span class="fw-semibold"><?= htmlspecialchars($b['nama_barang']) ?></span>
                        <span class="item-qty ms-1">(<?= $b['qty'] ?>x)</span>
                    </td>
                    <td class="text-end align-top fw-medium" width="35%">
                        <?= number_format($b['subtotal'], 0, ',', '.') ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="total-box">
            <table width="100%" class="m-0">
                <tr>
                    <td class="fw-bold">TOTAL</td>
                    <td class="text-end fw-bold">Rp <?= number_format($tx['total'], 0, ',', '.') ?></td>
                </tr>
            </table>
        </div>

        <div class="divider"></div>
        
        <div class="text-center footer-thanks mt-3">
            <p class="m-0 fw-medium">Terima Kasih</p>
            <p class="m-0 text-muted small">Silakan Datang Kembali</p>
        </div>

        <div class="d-grid gap-2 mt-4 no-print">
            <button onclick="window.print()" class="btn btn-dark btn-modern shadow-sm">
                <i class="fa-solid fa-print"></i> Cetak Struk Belanja
            </button>
        </div>
    </div>
</div>

</body>
</html>