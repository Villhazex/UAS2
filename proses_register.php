<?php

session_start();

require_once "config/database.php";

/* koneksi database */
$db = new Database();
$conn = $db->connect();

/* ambil data */
$nama_depan    = trim($_POST['nama_depan']);
$nama_belakang = trim($_POST['nama_belakang']);
$username      = trim($_POST['username']);
$email         = trim($_POST['email']);
$password      = $_POST['password'];
$confirm       = $_POST['confirm_password'];

/* validasi kosong */
if (
    empty($nama_depan) ||
    empty($username) ||
    empty($email) ||
    empty($password) ||
    empty($confirm)
) {

    $_SESSION['register_error'] =
    "Semua field wajib diisi.";

    header("Location: register.php");
    exit;
}

/* cek password */
if ($password !== $confirm) {

    $_SESSION['register_error'] =
    "Konfirmasi password tidak cocok.";

    header("Location: register.php");
    exit;
}

/* cek username */
$cekUsername = $conn->query(
    "SELECT id FROM users WHERE username='$username'"
);

if ($cekUsername->num_rows > 0) {

    $_SESSION['register_error'] =
    "Username sudah digunakan.";

    header("Location: register.php");
    exit;
}

/* cek email */
$cekEmail = $conn->query(
    "SELECT id FROM users WHERE email='$email'"
);

if ($cekEmail->num_rows > 0) {

    $_SESSION['register_error'] =
    "Email sudah digunakan.";

    header("Location: register.php");
    exit;
}

/* hash password */
$passwordHash = password_hash(
    $password,
    PASSWORD_DEFAULT
);

/* query insert */
$query = "
INSERT INTO users
(
    nama_depan,
    nama_belakang,
    username,
    email,
    password
)

VALUES
(
    '$nama_depan',
    '$nama_belakang',
    '$username',
    '$email',
    '$passwordHash'
)
";

/* jalankan query */
$insert = $conn->query($query);

/* hasil */
if ($insert) {

    $_SESSION['register_success'] =
    "Registrasi berhasil.";

} else {

    $_SESSION['register_error'] =
    "Registrasi gagal.";
}

header("Location: register.php");
exit;