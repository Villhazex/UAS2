<?php

class Tugas {

    // encapsulation
    protected $namaTugas;
    protected $statusTugas;

    // constructor
    public function __construct($namaTugas, $statusTugas = "Belum Selesai") {

        $this->namaTugas = $namaTugas;
        $this->statusTugas = $statusTugas;
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