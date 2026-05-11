<?php

session_start();

?>

<link rel="stylesheet" href="style.css">

<?php

require_once 'koneksi.php';
require_once 'Tugas.php';

$db = new Database();
$conn = $db->conn;

$tugas = new Tugas($conn);

$data = $tugas->tampilTugas();

?>

<h2>Dashboard Mahasiswa</h2>

<h3>Halo, <?= $_SESSION['nama']; ?></h3>

<a href="logout.php">Logout</a>

<br><br>

<table border="1" cellpadding="10">

<tr>

<th>No</th>
<th>Judul</th>
<th>Mata Kuliah</th>
<th>Dosen</th>
<th>Due Date</th>
<th>Status</th>
<th>File</th>
<th>Aksi</th>

</tr>

<?php

$no = 1;

while($row=mysqli_fetch_assoc($data)) {

$id_tugas = $row['id'];

$cek = mysqli_query(
    $conn,
    "SELECT * FROM jawaban
     WHERE tugas_id='$id_tugas'
     AND mahasiswa_id='".$_SESSION['id']."'"
);

$sudah = mysqli_num_rows($cek);

?>

<tr>

<td><?= $no++; ?></td>

<td><?= $row['judul']; ?></td>

<td><?= $row['mata_kuliah']; ?></td>

<td><?= $row['nama']; ?></td>

<td>

<?php

if(strtotime($row['due_date']) < strtotime(date('Y-m-d'))) {

    echo "<span class='red'>";
    echo $row['due_date'];
    echo "</span>";

} else {

    echo "<span class='green'>";
    echo $row['due_date'];
    echo "</span>";

}

?>

</td>

<td>

<?php

if($sudah > 0){

    echo "Sudah Mengumpulkan";

} else {

    echo "Belum Mengumpulkan";

}

?>

</td>

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