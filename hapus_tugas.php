<?php

require_once 'koneksi.php';
require_once 'Tugas.php';

$db = new Database();
$conn = $db->conn;

$tugas = new Tugas($conn);

$id = $_GET['id'];

$tugas->hapusTugas($id);

header('location:dosen.php');

?>