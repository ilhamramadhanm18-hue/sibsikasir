<?php

include "../koneksi.php";

$id = $_GET['id'];

$data = mysqli_query($conn,"
SELECT *
FROM users
WHERE id_user='$id'
");

$d = mysqli_fetch_array($data);

if(isset($_POST['update'])){

$nama = $_POST['nama'];
$username = $_POST['username'];
$level = $_POST['level'];

mysqli_query($conn,"
UPDATE users
SET
nama='$nama',
username='$username',
level='$level'
WHERE id_user='$id'
");

header("location:index.php");

}

?>

<!DOCTYPE html>
<html>
<head>
<title>Edit User</title>
</head>
<body>

<h2>Edit User</h2>

<form method="POST">

Nama

<br>

<input
type="text"
name="nama"
value="<?= $d['nama'] ?>"
required>

<br><br>

Username

<br>

<input
type="text"
name="username"
value="<?= $d['username'] ?>"
required>

<br><br>

Level

<br>

<select name="level">

<option value="pemilik"
<?= ($d['level']=='pemilik')?'selected':'' ?>>
Pemilik
</option>

<option value="kasir"
<?= ($d['level']=='kasir')?'selected':'' ?>>
Kasir
</option>

</select>

<br><br>

<button name="update">

Update

</button>

</form>

<br>

<a href="index.php">
Kembali
</a>

</body>
</html>