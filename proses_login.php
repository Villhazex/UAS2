<?php

session_start();

require_once "config/Database.php";

/* koneksi database */
$db = new Database();
$conn = $db->connect();

/* ambil data */
$username = trim($_POST['username']);
$password = $_POST['password'];

/* validasi kosong */
if (empty($username) || empty($password)) {

    $_SESSION['login_error'] =
    "Username dan password wajib diisi.";

    header("Location: login.php");
    exit;
}

/* cek user */
$query = "
SELECT * FROM users
WHERE username='$username'
LIMIT 1
";

$result = $conn->query($query);

/* kalau user ditemukan */
if ($result->num_rows > 0) {

    $user = $result->fetch_assoc();

    /* verifikasi password */
    if (password_verify($password, $user['password'])) {

        /* simpan session */
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama'] =
        $user['nama_depan'];

        /* redirect */
        header("Location: index.php");
        exit;

    } else {

        $_SESSION['login_error'] =
        "Password salah.";

        header("Location: login.php");
        exit;
    }

} else {

    $_SESSION['login_error'] =
    "Username tidak ditemukan.";

    header("Location: login.php");
    exit;
}