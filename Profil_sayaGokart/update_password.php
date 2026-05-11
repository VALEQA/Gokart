<?php
session_start();
require '../koneksi.php'; // Pastikan variabel koneksi kamu $koneksi atau $conn

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];

    // 1. Ambil data user dari database
    // Gunakan $koneksi atau $conn sesuai yang ada di file koneksi.php kamu
    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$id'");
    $data = mysqli_fetch_assoc($query);

    // 2. CEK PASSWORD LAMA (Menggunakan password_verify)
    // Karena di DB di-hash, kita tidak bisa pakai !=
    if (!password_verify($password_lama, $data['password'])) {
        echo "<script>
                alert('Password lama salah!');
                window.location='profil.php';
              </script>";
        exit;
    }

    // 3. Cek Konfirmasi Password Baru
    if ($password_baru !== $konfirmasi_password) {
        echo "<script>
                alert('Konfirmasi password baru tidak cocok!');
                window.location='profil.php';
              </script>";
        exit;
    }

    // 4. HASH PASSWORD BARU SEBELUM DISIMPAN
    $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);

    // 5. Update ke Database
    $update = mysqli_query($koneksi, "UPDATE users SET password='$password_hash' WHERE id='$id'");

    if ($update) {
        echo "<script>
                alert('Password berhasil diganti!');
                window.location='profil_saya.php';
              </script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>