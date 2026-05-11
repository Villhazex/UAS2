<?php

session_start();

require_once 'koneksi.php';

$db = new Database();
$conn = $db->conn;

$id = $_GET['id'];

if(isset($_POST['upload'])){

    $namaFile = $_FILES['file']['name'];
    $tmp = $_FILES['file']['tmp_name'];

    $ext = pathinfo($namaFile, PATHINFO_EXTENSION);

    if($ext != "pdf" && $ext != "docx"){

        echo "File harus PDF atau DOCX";
        exit;

    }

    move_uploaded_file(
        $tmp,
        'uploads/jawaban/'.$namaFile
    );

    $query = "INSERT INTO jawaban(
                tugas_id,
                mahasiswa_id,
                file_jawaban,
                waktu_upload
              )
              VALUES(
                '$id',
                '".$_SESSION['id']."',
                '$namaFile',
                NOW()
              )";

    mysqli_query($conn,$query);

    header('location:mahasiswa.php');

}

?>

<link rel="stylesheet" href="style.css">

<h2>Upload Jawaban</h2>

<form method="POST" enctype="multipart/form-data">

<input type="file" name="file">
<br><br>

<button name="upload">
Upload
</button>

</form>