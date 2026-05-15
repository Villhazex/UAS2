<?php

class Database {

    // Encapsulation: detail koneksi disimpan sebagai property private.
    private $host;
    private $username;
    private $password;
    private $database;
    private $conn;

    // Constructor: object database langsung membawa konfigurasi koneksi.
    public function __construct(
        $host = "localhost",
        $username = "root",
        $password = "",
        $database = "todo_oop"
    ) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
    }

    // Class method untuk membuka koneksi database.
    public function connect() {
        $this->conn = new mysqli(
            $this->host,
            $this->username,
            $this->password,
            $this->database
        );

        if ($this->conn->connect_error) {
            die("Koneksi gagal: " . $this->conn->connect_error);
        }

        return $this->conn;
    }

    // Getter agar object lain bisa memakai koneksi yang sudah dibuat.
    public function getConnection() {
        return $this->conn;
    }

    // Setter sederhana jika nama database perlu diganti tanpa mengubah class.
    public function setDatabase($database) {
        $this->database = $database;
    }

    public function getDatabase() {
        return $this->database;
    }
}

?>
