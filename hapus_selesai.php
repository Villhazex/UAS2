<?php

// Endpoint: menghapus semua tugas yang sudah selesai — dipanggil dari tombol "Hapus Selesai"

session_start();

require_once "class/TugasModel.php";

$user_id = $_SESSION['user_id'];

$tugas = new TugasModel();

$tugas->hapusSelesai($user_id);

header("Location: index.php");

exit;
?>
