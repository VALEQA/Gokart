<?php
include '../koneksi.php';

$id = $_POST['id'];
$nama = $_POST['nama'];
$email = $_POST['email'];
$no_telpon = $_POST['no_telpon'];

// SESUAIKAN NAMA KOLOM DI SINI
mysqli_query($koneksi, "UPDATE users SET nama_lengkap='$nama', email='$email', nomor_hp='$no_telpon' WHERE id='$id'");

header("Location:profil_saya.php");
?>