<?php

session_start();

require_once 'koneksi.php';

$db = new Database();
$conn = $db->conn;

$id = $_GET['id'];

$query = "SELECT jawaban.*,
          users.nama,
          tugas.due_date

          FROM jawaban

          JOIN users
          ON jawaban.mahasiswa_id = users.id

          JOIN tugas
          ON jawaban.tugas_id = tugas.id

          WHERE tugas_id='$id'

          ORDER BY jawaban.id DESC";

$data = mysqli_query($conn,$query);

?>

<link rel="stylesheet" href="style.css">

<h2>Daftar Jawaban Mahasiswa</h2>

<table border="1" cellpadding="10">

<tr>

<th>No</th>
<th>Mahasiswa</th>
<th>File</th>
<th>Waktu Upload</th>
<th>Status</th>
<th>Nilai</th>
<th>Komentar</th>
<th>Aksi</th>

</tr>

<?php

$no = 1;

while($row=mysqli_fetch_assoc($data)) {

?>

<tr>

<td><?= $no++; ?></td>

<td><?= $row['nama']; ?></td>

<td>

<a href="uploads/jawaban/<?= $row['file_jawaban']; ?>">
Download
</a>

</td>

<td><?= $row['waktu_upload']; ?></td>

<td>

<?php

if(strtotime($row['waktu_upload']) > strtotime($row['due_date'])){

    echo "<span class='red'>Terlambat</span>";

} else {

    echo "<span class='green'>Tepat Waktu</span>";

}

?>

</td>

<td><?= $row['nilai']; ?></td>

<td><?= $row['komentar']; ?></td>

<td>

<a href="beri_nilai.php?id=<?= $row['id']; ?>">
Beri Nilai
</a>

</td>

</tr>

<?php } ?>

</table>