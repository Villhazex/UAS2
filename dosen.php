<?php

session_start();

require_once 'koneksi.php';
require_once 'Tugas.php';

$db = new Database();
$conn = $db->conn;

$tugas = new Tugas($conn);

$data = $tugas->tampilTugas();

?>

<h2>Dashboard Dosen</h2>

<h3>Halo, <?= $_SESSION['nama']; ?></h3>

<a href="tambah_tugas.php">Tambah Tugas</a>

|

<a href="logout.php">Logout</a>

<br><br>

<table border="1" cellpadding="10">

<tr>

<th>No</th>
<th>Judul</th>
<th>Dosen</th>
<th>File</th>
<th>Aksi</th>

</tr>

<?php

$no = 1;

while($row=mysqli_fetch_assoc($data)) {

?>

<tr>

<td><?= $no++; ?></td>

<td><?= $row['judul']; ?></td>

<td><?= $row['nama']; ?></td>

<td>

<a href="uploads/tugas/<?= $row['file_tugas']; ?>">
Download
</a>

</td>

<td>

<a href="edit_tugas.php?id=<?= $row['id']; ?>">
Edit
</a>

|

<a href="hapus_tugas.php?id=<?= $row['id']; ?>">
Hapus
</a>

</td>

</tr>

<?php } ?>

</table>