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
    $mata_kuliah = $_POST['mata_kuliah'];
    $deskripsi = $_POST['deskripsi'];
    $due_date = $_POST['due_date'];

    $namaFile = $row['file_tugas'];

    if($_FILES['file']['name'] != ""){

        unlink("uploads/tugas/".$row['file_tugas']);

        $namaFile = $_FILES['file']['name'];
        $tmp = $_FILES['file']['tmp_name'];

        move_uploaded_file(
            $tmp,
            'uploads/tugas/'.$namaFile
        );

    }

    mysqli_query(
        $conn,
        "UPDATE tugas
         SET judul='$judul',
             mata_kuliah='$mata_kuliah',
             deskripsi='$deskripsi',
             due_date='$due_date',
             file_tugas='$namaFile'
         WHERE id='$id'"
    );

    header('location:dosen.php');

}

?>

<link rel="stylesheet" href="style.css">

<h2>Edit Tugas</h2>

<form method="POST" enctype="multipart/form-data">

<input
type="text"
name="judul"
value="<?= $row['judul']; ?>"
>

<br><br>

<input
type="text"
name="mata_kuliah"
value="<?= $row['mata_kuliah']; ?>"
>

<br><br>

<textarea name="deskripsi"><?= $row['deskripsi']; ?></textarea>

<br><br>

<input
type="date"
name="due_date"
value="<?= $row['due_date']; ?>"
>

<br><br>

File Lama:
<?= $row['file_tugas']; ?>

<br><br>

<input type="file" name="file">

<br><br>

<button name="update">
Update
</button>

</form>