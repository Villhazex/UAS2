<?php

class Tugas {

    private $conn;

    public function __construct($db){

        $this->conn = $db;

    }

    public function tambahTugas(
        $judul,
        $mata_kuliah,
        $deskripsi,
        $due_date,
        $file,
        $dosen_id
    ){

        $query = "INSERT INTO tugas(
                    judul,
                    mata_kuliah,
                    deskripsi,
                    due_date,
                    file_tugas,
                    dosen_id
                  )
                  VALUES(
                    '$judul',
                    '$mata_kuliah',
                    '$deskripsi',
                    '$due_date',
                    '$file',
                    '$dosen_id'
                  )";

        return mysqli_query($this->conn,$query);

    }

    public function tampilTugas(){

        $query = "SELECT tugas.*, users.nama
                FROM tugas
                JOIN users
                ON tugas.dosen_id = users.id
                ORDER BY tugas.id DESC";

        return mysqli_query($this->conn,$query);

    }

    public function editTugas(
        $id,
        $judul,
        $mata_kuliah,
        $deskripsi,
        $due_date
    ){

        $query = "UPDATE tugas
                  SET judul='$judul',
                      mata_kuliah='$mata_kuliah',
                      deskripsi='$deskripsi',
                      due_date='$due_date'
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