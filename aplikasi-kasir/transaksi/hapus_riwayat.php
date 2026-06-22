<?php
session_start();
include "../config/koneksi.php";

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $filter = isset($_GET['filter']) ? mysqli_real_escape_string($conn, $_GET['filter']) : 'hari_ini';

    // 1. Hapus rincian barang terlebih dahulu pada tabel detail_transaksi
    $queryDetail = "DELETE FROM detail_transaksi WHERE no_nota = '$id'";
    mysqli_query($conn, $queryDetail);

    // 2. Bersihkan master record pada induk tabel transaksi
    $queryMaster = "DELETE FROM transaksi WHERE id = '$id'";
    
    if (mysqli_query($conn, $queryMaster)) {
        // Alihkan kembali dengan mempertahankan filter tab aktif terakhir Anda
        header("Location: riwayat.php?status=hapus_sukses&filter=" . $filter);
        exit;
    } else {
        echo "Gagal menghapus transaksi utama: " . mysqli_error($conn);
    }
} else {
    header("Location: riwayat.php");
    exit;
}
?>