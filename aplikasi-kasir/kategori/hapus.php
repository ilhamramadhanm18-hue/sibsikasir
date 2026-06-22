<?php
session_start();
include "../config/koneksi.php";

// Proteksi halaman: Wajib login
if (!isset($_SESSION['nama']) || trim($_SESSION['nama']) == '') {
    header("Location: ../login.php");
    exit;
}

$id = isset($_GET['id']) ? $_GET['id'] : '';
$username_aktif = $_SESSION['username'];

// 1. Putus hubungan barang-barang yang memakai kategori ini (Prepared Statement)
$query_update_barang = "UPDATE barang SET id_kategori = NULL WHERE id_kategori = ? AND username = ?";
$stmt_update = mysqli_prepare($conn, $query_update_barang);
mysqli_stmt_bind_param($stmt_update, "is", $id, $username_aktif);
mysqli_stmt_execute($stmt_update);
mysqli_stmt_close($stmt_update);

// 2. Hapus data kategori (Prepared Statement)
$query_hapus_kategori = "DELETE FROM kategori WHERE id = ? AND username = ?";
$stmt_hapus = mysqli_prepare($conn, $query_hapus_kategori);
mysqli_stmt_bind_param($stmt_hapus, "is", $id, $username_aktif);

if (mysqli_stmt_execute($stmt_hapus)) {
    mysqli_stmt_close($stmt_hapus);
    header("Location: index.php?status=berhasil");
} else {
    mysqli_stmt_close($stmt_hapus);
    header("Location: index.php?status=gagal");
}
exit;
?>