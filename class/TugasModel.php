<?php

require_once __DIR__ . "/Tugas.php";
require_once __DIR__ . "/../config/database.php";

class TugasModel extends Tugas {

    private $conn;

    public function __construct($namaTugas = "", $statusTugas = "Belum Selesai") {

        parent::__construct($namaTugas, $statusTugas);

        $database = new Database();
        $this->conn = $database->connect();
    }

    // tambah tugas
    public function tambahTugas() {

        $nama = $this->getNamaTugas();
        $status = $this->getStatusTugas();

        $query = "INSERT INTO tugas (nama_tugas, status_tugas)
                  VALUES ('$nama', '$status')";

        return $this->conn->query($query);
    }

    // tampil tugas
    public function tampilTugas() {

        $query = "SELECT * FROM tugas ORDER BY id DESC";

        return $this->conn->query($query);
    }

    // hapus tugas
    public function hapusTugas($id) {

        $query = "DELETE FROM tugas WHERE id = $id";

        return $this->conn->query($query);
    }

    // selesai tugas
    public function selesaiTugas($id) {

        $query = "UPDATE tugas
                  SET status_tugas = 'Selesai'
                  WHERE id = $id";

        return $this->conn->query($query);
    }
}

?>