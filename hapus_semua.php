<?php

require_once "class/TugasModel.php";

$tugas = new TugasModel();
$tugas->hapusSemua();

header("Location: index.php");