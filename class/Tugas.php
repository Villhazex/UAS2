<?php
class Tugas {

    // Encapsulation: property dibuat protected agar child class bisa memakai,
    // tetapi tidak bisa diakses langsung dari luar object.
    protected $namaTugas;
    protected $statusTugas;
    protected $dueDate;
    protected $prioritas;
    protected $kategori;
    protected $userId;

    // Constructor: membuat object tugas dengan nilai awal.
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

    // Setter: method untuk mengubah nilai property secara terkontrol.
    public function setNamaTugas($namaTugas) {
        $this->namaTugas = $namaTugas;
    }

    public function setStatusTugas($statusTugas) {
        $this->statusTugas = $statusTugas;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
    }

    public function setDueDate($dueDate) {
        $this->dueDate = $dueDate;
    }

    public function setPrioritas($prioritas) {
        $this->prioritas = $prioritas;
    }

    public function setKategori($kategori) {
        $this->kategori = $kategori;
    }

    // Getter: method untuk membaca property object.
    public function getNamaTugas() {
        return $this->namaTugas;
    }

    public function getStatusTugas() {
        return $this->statusTugas;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getDueDate() {
        return $this->dueDate;
    }

    public function getPrioritas() {
        return $this->prioritas;
    }

    public function getKategori() {
        return $this->kategori;
    }
}

?>
