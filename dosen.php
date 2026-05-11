<?php

session_start();

if(!isset($_SESSION['role'])){

    header('location:login.php');

}

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

<h2>Dashboard Dosen</h2>

<h3>Halo, <?= $_SESSION['nama']; ?></h3>

<form method="GET">

<input
type="text"
name="search"
placeholder="Cari tugas..."
>

<button>
Cari
</button>

</form>

<br>

<a href="tambah_tugas.php">Tambah Tugas</a>

|

<a href="logout.php">Logout</a>

<br><br>

<table border="1" cellpadding="10">

<tr>

<th>No</th>
<th>Judul</th>
<th>Mata Kuliah</th>
<th>Dosen</th>
<th>Due Date</th>
<th>File</th>
<th>Aksi</th>

</tr>

<?php

if(isset($_GET['search'])){

    $search = $_GET['search'];

    $data = mysqli_query(
        $conn,
        "SELECT tugas.*, users.nama
         FROM tugas
         JOIN users
         ON tugas.dosen_id = users.id
         WHERE judul LIKE '%$search%'
         OR mata_kuliah LIKE '%$search%'
         ORDER BY tugas.id DESC"
    );

}

$no = 1;

while($row=mysqli_fetch_assoc($data)) {

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

<a href="uploads/tugas/<?= $row['file_tugas']; ?>">
Download
</a>

</td>

<td>

<a href="lihat_jawaban.php?id=<?= $row['id']; ?>">
Jawaban
</a>

|

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