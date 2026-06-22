<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['nama']) || trim($_SESSION['nama']) == '') {
    header("Location: ../login.php");
    exit;
}

$username_aktif = $_SESSION['username'];
$id_barang = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_barang > 0) {
    // KEAMANAN: Hapus hanya jika barang tersebut adalah milik user yang sedang aktif login
    $query = "DELETE FROM barang WHERE id = ? AND username = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "is", $id_barang, $username_aktif);
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: index.php?status=sukses");
        exit;
    } else {
        echo "<script>alert('Gagal menghapus data!'); window.location.href='index.php';</script>";
        mysqli_stmt_close($stmt);
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
?>