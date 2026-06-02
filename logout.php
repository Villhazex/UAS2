<?php

// Endpoint: logout — hapus session dan redirect ke halaman login

session_start();

session_unset();
session_destroy();

header("Location: login.php");
exit;