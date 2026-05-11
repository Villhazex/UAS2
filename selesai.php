<?php

require_once "class/TugasModel.php";

if(isset($_GET['id'])) {

    $id = $_GET['id'];

    $tugas = new TugasModel();

    $tugas->selesaiTugas($id);

    header("Location: index.php");
}

?>