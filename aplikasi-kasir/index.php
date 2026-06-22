<?php
session_start();

// Memastikan kode berhenti setelah header dipanggil agar tidak ada proses lain yang berjalan
if(isset($_SESSION['nama']) && !empty($_SESSION['nama'])) {
    header("Location: dashboard.php");
    exit; // Penting untuk menambah 'exit' setelah setiap redirect
} else {
    header("Location: login.php");
    exit; // Penting untuk menambah 'exit' setelah setiap redirect
}
?>