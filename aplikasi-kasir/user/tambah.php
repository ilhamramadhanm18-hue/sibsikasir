<?php

include "../koneksi.php";

if(isset($_POST['simpan'])){

$nama = $_POST['nama'];
$username = $_POST['username'];
$password = md5($_POST['password']);
$level = $_POST['level'];

mysqli_query($conn,"
INSERT INTO users(
nama,
username,
password,
level
)
VALUES(
'$nama',
'$username',
'$password',
'$level'
)
");

header("location:index.php");

}

?>

<!DOCTYPE html>
<html>
<head>
<title>Tambah User</title>
</head>
<body>

<h2>Tambah User</h2>

<form method="POST">

Nama

<br>

<input type="text" name="nama" required>

<br><br>

Username

<br>

<input type="text" name="username" required>

<br><br>

Password

<br>

<input type="password" name="password" required>

<br><br>

Level

<br>

<select name="level">

<option value="pemilik">
Pemilik
</option>

<option value="kasir">
Kasir
</option>

</select>

<br><br>

<button name="simpan">

Simpan

</button>

</form>

<br>

<a href="index.php">
Kembali
</a>

</body>
</html>