<?php

session_start();

require_once 'koneksi.php';

$db = new Database();
$conn = $db->conn;

$id = $_GET['id'];

if(isset($_POST['upload'])){

    $namaFile = $_FILES['file']['name'];
    $tmp = $_FILES['file']['tmp_name'];

    move_uploaded_file(
        $tmp,
        'uploads/jawaban/'.$namaFile
    );

    $query = "INSERT INTO jawaban(
                tugas_id,
                mahasiswa_id,
                file_jawaban
              )
              VALUES(
                '$id',
                '".$_SESSION['id']."',
                '$namaFile'
              )";

    mysqli_query($conn,$query);

    header('location:mahasiswa.php');

}

?>

<form method="POST" enctype="multipart/form-data">

<input type="file" name="file">
<br><br>

<button name="upload">
Upload
</button>

</form>