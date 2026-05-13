<?php

require_once __DIR__ . "/Tugas.php";
require_once __DIR__ . "/../config/database.php";

class TugasModel extends Tugas {

    private $conn;

public function __construct(
    $namaTugas = "",
    $statusTugas = "Belum Selesai",
    $dueDate = null,
    $prioritas = "",
    $kategori = "",
    $userId = 0
)

{

parent::__construct(
    $namaTugas,
    $statusTugas,
    $dueDate,
    $prioritas,
    $kategori,
    $userId
);

    $db = new Database();
    $this->conn = $db->connect();
}

    // tambah tugas
public function tambahTugas() {

    $query = "INSERT INTO tugas
    (
        user_id,
        nama_tugas,
        status_tugas,
        due_date,
        prioritas,
        kategori
    )
    VALUES
    (
        '$this->userId',
        '$this->namaTugas',
        '$this->statusTugas',
        " . ($this->dueDate ? "'$this->dueDate'" : "NULL") . ",
        '$this->prioritas',
        '$this->kategori'
    )";

    return $this->conn->query($query);
}

    // tampil tugas
public function tampilTugas($user_id) {

    $sql = "SELECT * FROM tugas
            WHERE user_id = '$user_id'
            ORDER BY id DESC";

    return $this->conn->query($sql);
}

    // hapus tugas
    public function hapusTugas($id, $user_id) {

    $query = "DELETE FROM tugas
              WHERE id = $id
              AND user_id = $user_id";

    return $this->conn->query($query);
}

    // selesai tugas
    public function selesaiTugas($id) {

        $query = "UPDATE tugas
                  SET status_tugas = 'Selesai'
                  WHERE id = $id";

        return $this->conn->query($query);
    }

    // hapus semua tugas
public function hapusSemua($user_id) {

    $query = "DELETE FROM tugas
              WHERE user_id = $user_id";

    return $this->conn->query($query);
}

    // hapus semua tugas selesai
    public function hapusSelesai($user_id) {

    $query = "DELETE FROM tugas
              WHERE status_tugas = 'Selesai'
              AND user_id = $user_id";

    return $this->conn->query($query);
}
}

?>