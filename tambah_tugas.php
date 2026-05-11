<?php

session_start();

require_once 'koneksi.php';
require_once 'Tugas.php';

$db = new Database();
$conn = $db->conn;

$tugas = new Tugas($conn);

if(isset($_POST['simpan'])){

    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];

    $namaFile = $_FILES['file']['name'];
    $tmp = $_FILES['file']['tmp_name'];

    move_uploaded_file(
        $tmp,
        'uploads/tugas/'.$namaFile
    );

    $tugas->tambahTugas(
        $judul,
        $deskripsi,
        $namaFile,
        $_SESSION['id']
    );

    header('location:dosen.php');

}

?>

<form method="POST" enctype="multipart/form-data">

<input type="text" name="judul" placeholder="Judul">
<br><br>

<textarea name="deskripsi"></textarea>
<br><br>

<input type="file" name="file">
<br><br>

<button name="simpan">
Simpan
</button>

</form>