<?php

class Database {

    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "todo_oop";

    public $conn;

    public function connect() {

        $this->conn = new mysqli(
            $this->host,
            $this->username,
            $this->password,
            $this->database
        );

        if ($this->conn->connect_error) {
            die("Koneksi gagal");
        }

        return $this->conn;
    }
}

?>