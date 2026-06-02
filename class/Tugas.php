<?php

// Class dasar (parent) yang merepresentasikan satu tugas
class Tugas
{
    // Properti tugas
    protected $namaTugas;    // Judul tugas
    protected $statusTugas;  // 'Belum Selesai' atau 'Selesai'
    protected $dueDate;      // Tenggat waktu (YYYY-MM-DD)
    protected $prioritas;    // 'tinggi', 'sedang', 'rendah', atau ''
    protected $kategori;     // Slug kategori/list tempat tugas berada
    protected $userId;       // ID pemilik tugas

    // Constructor: mengisi properti saat objek dibuat
    public function __construct(
        $namaTugas = '',
        $statusTugas = 'Belum Selesai',
        $dueDate = null,
        $prioritas = '',
        $kategori = '',
        $userId = 0
    ) {
        $this->setNamaTugas($namaTugas);
        $this->setStatusTugas($statusTugas);
        $this->setDueDate($dueDate);
        $this->setPrioritas($prioritas);
        $this->setKategori($kategori);
        $this->setUserId($userId);
    }

    // Setter: mengubah nilai properti
    public function setNamaTugas($namaTugas)
    {
        $this->namaTugas = $namaTugas;
    }

    public function setStatusTugas($statusTugas)
    {
        $this->statusTugas = $statusTugas;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function setDueDate($dueDate)
    {
        $this->dueDate = $dueDate;
    }

    public function setPrioritas($prioritas)
    {
        $this->prioritas = $prioritas;
    }

    public function setKategori($kategori)
    {
        $this->kategori = $kategori;
    }

    // Getter: membaca nilai properti
    public function getNamaTugas()
    {
        return $this->namaTugas;
    }

    public function getStatusTugas()
    {
        return $this->statusTugas;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getDueDate()
    {
        return $this->dueDate;
    }

    public function getPrioritas()
    {
        return $this->prioritas;
    }

    public function getKategori()
    {
        return $this->kategori;
    }
}
