<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['nama']) || trim($_SESSION['nama']) == '') {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $filter = isset($_GET['filter']) ? mysqli_real_escape_string($conn, $_GET['filter']) : 'hari_ini';
    $tgl_mulai = isset($_GET['tgl_mulai']) ? mysqli_real_escape_string($conn, $_GET['tgl_mulai']) : '';
    $tgl_selesai = isset($_GET['tgl_selesai']) ? mysqli_real_escape_string($conn, $_GET['tgl_selesai']) : '';
    
    // Ambil inputan password dari SweetAlert2 dan pengenal user yang sedang aktif
    $password_verifikasi = isset($_GET['password_verifikasi']) ? mysqli_real_escape_string($conn, $_GET['password_verifikasi']) : '';
    $nama_aktif = $_SESSION['nama']; 

    // 1. Ambil password asli user dari database
    $queryUser = mysqli_query($conn, "SELECT password FROM users WHERE nama = '$nama_aktif' OR username = '$nama_aktif' LIMIT 1");
    $dataUser = mysqli_fetch_assoc($queryUser);

    if (!$dataUser) {
        header("Location: riwayat.php?status=user_invalid&filter=" . $filter . "&tgl_mulai=" . $tgl_mulai . "&tgl_selesai=" . $tgl_selesai);
        exit;
    }

    // 2. Proses Validasi Password
    if ($password_verifikasi !== $dataUser['password'] && md5($password_verifikasi) !== $dataUser['password']) {
        // Jika password salah, alihkan dengan status=pw_salah
        header("Location: riwayat.php?status=pw_salah&filter=" . $filter . "&tgl_mulai=" . $tgl_mulai . "&tgl_selesai=" . $tgl_selesai);
        exit;
    }

    // 3. Hapus rincian barang terlebih dahulu pada tabel detail_transaksi
    $queryDetail = "DELETE FROM detail_transaksi WHERE no_nota = '$id'";
    mysqli_query($conn, $queryDetail);

    // 4. Bersihkan master record pada induk tabel transaksi
    $queryMaster = "DELETE FROM transaksi WHERE id = '$id'";
    
    if (mysqli_query($conn, $queryMaster)) {
        // Alihkan kembali dengan status=hapus_sukses
        header("Location: riwayat.php?status=hapus_sukses&filter=" . $filter . "&tgl_mulai=" . $tgl_mulai . "&tgl_selesai=" . $tgl_selesai);
        exit;
    } else {
        echo "Gagal menghapus transaksi utama: " . mysqli_error($conn);
    }
} else {
    header("Location: riwayat.php");
    exit;
}
?>