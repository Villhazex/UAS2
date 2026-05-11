<?php

class Database {

    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $db = "uas_oop";

    public $conn;

    public function __construct(){

        $this->conn = mysqli_connect(
            $this->host,
            $this->user,
            $this->pass,
            $this->db
        );

    }

}

?>