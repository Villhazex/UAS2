<?php

class Tugas {

    // encapsulation
    protected $namaTugas;
    protected $statusTugas;
    protected $dueDate;
    protected $prioritas;
    protected $kategori;

    // constructor
    public function __construct(
    $namaTugas = "",
    $statusTugas = "Belum Selesai",
    $dueDate = null,
    $prioritas = "",
    $kategori = ""
) {
    $this->namaTugas   = $namaTugas;
    $this->statusTugas = $statusTugas;
    $this->dueDate     = $dueDate;
    $this->prioritas   = $prioritas;
    $this->kategori    = $kategori;
}

    // setter
    public function setNamaTugas($namaTugas) {
        $this->namaTugas = $namaTugas;
    }

    public function setStatusTugas($statusTugas) {
        $this->statusTugas = $statusTugas;
    }

    // getter
    public function getNamaTugas() {
        return $this->namaTugas;
    }

    public function getStatusTugas() {
        return $this->statusTugas;
    }
}

?>