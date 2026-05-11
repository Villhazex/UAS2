<?php

require_once 'koneksi.php';
require_once 'Tugas.php';

?>

<link rel="stylesheet" href="style.css">

<?php

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

    $tugas->editTugas(
        $id,
        $judul,
        $mata_kuliah,
        $deskripsi,
        $due_date
    );

    header('location:dosen.php');

}

?>

<h2>Edit Tugas</h2>

<form method="POST">

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

<button name="update">
Update
</button>

</form>