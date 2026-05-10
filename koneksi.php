<?php
$host = "localhost";
$user = "root"; // Default username XAMPP
$pass = "";     // Default password XAMPP kosong
$db   = "racing_hub";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Gagal terhubung ke pit stop (database): " . mysqli_connect_error());
}
?>