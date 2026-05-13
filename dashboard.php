<?php

session_start();

/* cek login */
if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>

    <h1>
        Halo, <?= $_SESSION['nama'] ?>
    </h1>

    <p>Login berhasil ✓</p>

    <a href="logout.php">
        Logout
    </a>

</body>
</html>