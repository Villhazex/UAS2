<?php

class Tugas {

    private $conn;

    public function __construct($db){

        $this->conn = $db;

    }

    public function tambahTugas($judul,$deskripsi,$file,$dosen_id){

        $query = "INSERT INTO tugas(
                    judul,
                    deskripsi,
                    file_tugas,
                    dosen_id
                )
                VALUES(
                    '$judul',
                    '$deskripsi',
                    '$file',
                    '$dosen_id'
                )";

        return mysqli_query($this->conn,$query);

    }

    public function tampilTugas(){

        $query = "SELECT * FROM tugas";

        return mysqli_query($this->conn,$query);

    }

    public function editTugas($id,$judul,$deskripsi){

    $query = "UPDATE tugas
              SET judul='$judul',
                  deskripsi='$deskripsi'
              WHERE id='$id'";

    return mysqli_query($this->conn,$query);

    }

    public function hapusTugas($id){

        $query = "DELETE FROM tugas
                WHERE id='$id'";

        return mysqli_query($this->conn,$query);

    }

}

?>