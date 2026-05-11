<?php

require_once "class/TugasModel.php";

$tugas = new TugasModel();
$tugas->hapusSelesai();

header("Location: index.php");