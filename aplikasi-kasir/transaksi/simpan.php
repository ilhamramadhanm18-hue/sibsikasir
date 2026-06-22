<?php
session_start();
include "../config/koneksi.php";

// 1. SET TIMEZONE AGAR SESUAI DENGAN WAKTU LOKAL (WIB)
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['nama'])) {
    $kasir = $_SESSION['nama']; 
    
    // Sanitasi input
    $total_akhir = (int)($_POST['total_akhir'] ?? 0);
    $metode_pembayaran = mysqli_real_escape_string($conn, $_POST['metode_pembayaran'] ?? 'Tunai'); 
    $uang_bayar = (int)($_POST['uang_bayar'] ?? 0); 
    
    // Waktu sekarang sudah otomatis mengikuti Asia/Jakarta
    $tanggal = date('Y-m-d H:i:s');

    // Validasi dasar
    if (empty($_POST['arr_id_barang'])) {
        header("Location: index.php?error=empty");
        exit;
    }

    mysqli_begin_transaction($conn);

    try {
        // Masukkan ke tabel transaksi
        $stmt = mysqli_prepare($conn, "INSERT INTO transaksi (tanggal, kasir, total, metode_pembayaran, uang_bayar) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssisi", $tanggal, $kasir, $total_akhir, $metode_pembayaran, $uang_bayar);
        
        if (!mysqli_stmt_execute($stmt)) throw new Exception("Gagal menyimpan transaksi");
        
        $id_transaksi = mysqli_insert_id($conn);

        $arr_id_barang = $_POST['arr_id_barang'];
        $arr_qty = $_POST['arr_qty'];

        for ($i = 0; $i < count($arr_id_barang); $i++) {
            $id_barang = (int)$arr_id_barang[$i];
            $qty = (int)$arr_qty[$i];

            // Ambil harga saat ini dari database
            $queryHrg = mysqli_prepare($conn, "SELECT harga FROM barang WHERE id = ?");
            mysqli_stmt_bind_param($queryHrg, "i", $id_barang);
            mysqli_stmt_execute($queryHrg);
            $result = mysqli_stmt_get_result($queryHrg);
            $b = mysqli_fetch_assoc($result);
            
            if (!$b) throw new Exception("Barang tidak ditemukan");
            
            $subtotal = (int)$b['harga'] * $qty;

            // Masukkan detail
            $stmtDetail = mysqli_prepare($conn, "INSERT INTO detail_transaksi (no_nota, id_barang, qty, subtotal) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmtDetail, "iiii", $id_transaksi, $id_barang, $qty, $subtotal);
            mysqli_stmt_execute($stmtDetail);

            // Update Stok
            $stmtStok = mysqli_prepare($conn, "UPDATE barang SET stok = stok - ? WHERE id = ? AND stok >= ?");
            mysqli_stmt_bind_param($stmtStok, "iii", $qty, $id_barang, $qty);
            mysqli_stmt_execute($stmtStok);
            
            if (mysqli_stmt_affected_rows($stmtStok) == 0) {
                throw new Exception("Stok tidak mencukupi untuk ID Barang: $id_barang");
            }
        }

        mysqli_commit($conn);
        header("Location: detail.php?id=" . $id_transaksi);
        exit;

    } catch (Exception $e) {
        mysqli_rollback($conn);
        // Tampilkan pesan error yang lebih user-friendly
        echo "<script>alert('Transaksi gagal: " . $e->getMessage() . "'); window.location='index.php';</script>";
    }

} else {
    header("Location: index.php");
    exit;
}
?>