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
    $mysqli_stmt_bind_param = mysqli_stmt_bind_param($stmt, "is", $id_barang, $username_aktif);
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        // SINKRONISASI: Diubah ke 'sukses_hapus' agar pop-up sukses muncul di halaman index.php
        header("Location: index.php?status=sukses_hapus");
        exit;
    } else {
        // UPGRADE: Mengubah alert jadul menjadi Pop-up SweetAlert2 yang estetik & sinkron secara UI
        echo "
        <!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <link href='https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap' rel='stylesheet'>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <style>
                body { 
                    font-family: 'Plus Jakarta Sans', sans-serif; 
                    background-color: #f8fafc; 
                }
                .swal2-popup {
                    border-radius: 20px !important;
                    padding: 2rem !important;
                }
                .swal2-title {
                    font-weight: 700 !important;
                    color: #1e293b !important;
                }
                .swal2-html-container {
                    color: #64748b !important;
                    font-size: 0.95rem !important;
                }
                .swal2-confirm {
                    border-radius: 12px !important;
                    padding: 12px 30px !important;
                    font-weight: 600 !important;
                    transition: all 0.2s ease !important;
                }
                .swal2-confirm:hover {
                    transform: translateY(-1px) !important;
                }
            </style>
        </head>
        <body>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menghapus!',
                    text: 'Terjadi kesalahan sistem atau masalah koneksi saat menghapus data.',
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'Kembali',
                    buttonsStyling: true
                }).then(() => {
                    window.location.href = 'index.php';
                });
            </script>
        </body>
        </html>";
        mysqli_stmt_close($stmt);
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
?>