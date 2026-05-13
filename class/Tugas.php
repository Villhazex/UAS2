<?php

class Tugas {

    // encapsulation
    protected $namaTugas;
    protected $statusTugas;
    protected $dueDate;
    protected $prioritas;
    protected $kategori;
    protected $userId;

    // constructor
    public function __construct(
        $namaTugas = "",
        $statusTugas = "Belum Selesai",
        $dueDate = null,
        $prioritas = "",
        $kategori = "",
        $userId = 0
    ) {

        $this->namaTugas   = $namaTugas;
        $this->statusTugas = $statusTugas;
        $this->dueDate     = $dueDate;
        $this->prioritas   = $prioritas;
        $this->kategori    = $kategori;
        $this->userId      = $userId;
    }

    // setter
    public function setNamaTugas($namaTugas) {
        $this->namaTugas = $namaTugas;
    }

    public function setStatusTugas($statusTugas) {
        $this->statusTugas = $statusTugas;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
    }

    // getter
    public function getNamaTugas() {
        return $this->namaTugas;
    }

    public function getStatusTugas() {
        return $this->statusTugas;
    }

    public function getUserId() {
        return $this->userId;
    }
}

?>