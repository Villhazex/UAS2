<?php

require_once 'koneksi.php';

$db = new Database();
$conn = $db->conn;

$id = $_GET['id'];

$data = mysqli_query(
    $conn,
    "SELECT * FROM tugas
     WHERE id='$id'"
);

$row = mysqli_fetch_assoc($data);

unlink("uploads/tugas/".$row['file_tugas']);

mysqli_query(
    $conn,
    "DELETE FROM tugas
     WHERE id='$id'"
);

header('location:dosen.php');

?>