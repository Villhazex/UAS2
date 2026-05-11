<?php

require_once 'koneksi.php';

$db = new Database();
$conn = $db->conn;

$id = $_GET['id'];

if(isset($_POST['simpan'])){

    $nilai = $_POST['nilai'];
    $komentar = $_POST['komentar'];

    mysqli_query(
        $conn,
        "UPDATE jawaban
         SET nilai='$nilai',
             komentar='$komentar'
         WHERE id='$id'"
    );

    header('location:dosen.php');

}

?>

<link rel="stylesheet" href="style.css">

<h2>Beri Nilai</h2>

<form method="POST">

<input
type="number"
name="nilai"
placeholder="Nilai"
>

<br><br>

<textarea
name="komentar"
placeholder="Komentar"
></textarea>

<br><br>

<button name="simpan">
Simpan
</button>

</form>