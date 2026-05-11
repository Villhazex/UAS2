<?php

class User {

    protected $nama;
    protected $username;

    private $password;

    public function __construct($nama,$username,$password){

        $this->nama = $nama;
        $this->username = $username;
        $this->password = $password;

    }

    public function getPassword(){

        return $this->password;

    }

    public function setPassword($password){

        $this->password = $password;

    }

}

class Dosen extends User {

    public function akses(){

        return "Halaman Dosen";

    }

}

class Mahasiswa extends User {

    public function akses(){

        return "Halaman Mahasiswa";

    }

}

?>