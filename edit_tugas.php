<?php

require_once 'koneksi.php';
require_once 'Tugas.php';

$db = new Database();
$conn = $db->conn;

$tugas = new Tugas($conn);

$id = $_GET['id'];

$data = mysqli_query(
    $conn,
    "SELECT * FROM tugas WHERE id='$id'"
);

$row = mysqli_fetch_assoc($data);

if(isset($_POST['update'])){

    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];

    $tugas->editTugas(
        $id,
        $judul,
        $deskripsi
    );

    header('location:dosen.php');

}

?>

<form method="POST">

<input
type="text"
name="judul"
value="<?= $row['judul']; ?>"
>

<br><br>

<textarea name="deskripsi"><?= $row['deskripsi']; ?></textarea>

<br><br>

<button name="update">
Update
</button>

</form>