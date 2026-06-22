<?php
include "../koneksi.php";

$data = mysqli_query($conn,"SELECT * FROM users");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data User</title>
</head>
<body>

<h2>Data User</h2>

<a href="tambah.php">Tambah User</a>

<br><br>

<table border="1" cellpadding="10">

<tr>
    <th>No</th>
    <th>Nama</th>
    <th>Username</th>
    <th>Level</th>
    <th>Aksi</th>
</tr>

<?php
$no=1;

while($d=mysqli_fetch_array($data)){
?>

<tr>

<td><?= $no++ ?></td>

<td><?= $d['nama'] ?></td>

<td><?= $d['username'] ?></td>

<td><?= $d['level'] ?></td>

<td>

<a href="edit.php?id=<?= $d['id_user'] ?>">
Edit
</a>

|

<a href="hapus.php?id=<?= $d['id_user'] ?>"
onclick="return confirm('Yakin hapus user?')">

Hapus

</a>

</td>

</tr>

<?php } ?>

</table>

</body>
</html>