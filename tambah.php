<?php

require_once "class/TugasModel.php";

if(isset($_POST['nama_tugas'])) {

    $nama = $_POST['nama_tugas'];

    $tugas = new TugasModel($nama);

    $tugas->tambahTugas();

    header("Location: index.php");
}

?>