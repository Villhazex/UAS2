<?php

session_start();

require_once 'koneksi.php';

$db = new Database();
$conn = $db->conn;

if(isset($_POST['login'])){

    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users
              WHERE username='$username'
              AND password='$password'";

    $result = mysqli_query($conn,$query);

    if(mysqli_num_rows($result) > 0){

        $data = mysqli_fetch_assoc($result);

        $_SESSION['id'] = $data['id'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['role'] = $data['role'];

        if($data['role'] == 'dosen'){

            header('location:dosen.php');

        } else {

            header('location:mahasiswa.php');

        }

    }

}

?>

<form method="POST">

<h2>Login</h2>

<input type="text" name="username" placeholder="Username">
<br><br>

<input type="password" name="password" placeholder="Password">
<br><br>

<button name="login">
Login
</button>

</form>