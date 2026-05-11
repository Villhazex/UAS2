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

if(isset($_POST['simpan'])){

    $judul = $_POST['judul'];
    $mata_kuliah = $_POST['mata_kuliah'];
    $deskripsi = $_POST['deskripsi'];
    $due_date = $_POST['due_date'];

    $namaFile = $_FILES['file']['name'];
    $tmp = $_FILES['file']['tmp_name'];

    move_uploaded_file(
        $tmp,
        'uploads/tugas/'.$namaFile
    );

    $tugas->tambahTugas(
        $judul,
        $mata_kuliah,
        $deskripsi,
        $due_date,
        $namaFile,
        $_SESSION['id']
    );

    header('location:dosen.php');

}

?>

<h2>Tambah Tugas</h2>

<form method="POST" enctype="multipart/form-data">

<input type="text" name="judul" placeholder="Judul">
<br><br>

<input type="text" name="mata_kuliah" placeholder="Mata Kuliah">
<br><br>

<textarea name="deskripsi"></textarea>
<br><br>

<input type="date" name="due_date">
<br><br>

<input type="file" name="file">
<br><br>

<button name="simpan">
Simpan
</button>

</form>