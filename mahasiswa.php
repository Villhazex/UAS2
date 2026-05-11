<?php

session_start();

require_once 'koneksi.php';
require_once 'Tugas.php';

$db = new Database();
$conn = $db->conn;

/*
|--------------------------------------------------------------------------
| Ambil semua tugas + nama dosen
|--------------------------------------------------------------------------
*/

$query = "SELECT tugas.*, users.nama
          FROM tugas
          JOIN users
          ON tugas.dosen_id = users.id";

$data = mysqli_query($conn, $query);

?>

<h2>Dashboard Mahasiswa</h2>

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

while($row = mysqli_fetch_assoc($data)) {

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
        <a href="upload_jawaban.php?id=<?= $row['id']; ?>">
            Upload Jawaban
        </a>
    </td>

</tr>

<?php } ?>

</table>