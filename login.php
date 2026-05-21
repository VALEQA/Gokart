<?php
session_start();
require 'koneksi.php';
$pesan = "";

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    // Cari user berdasarkan email
    $cek_user = mysqli_query($koneksi, "SELECT * FROM users WHERE email = '$email'");
    
    if (mysqli_num_rows($cek_user) === 1) {
        $row = mysqli_fetch_assoc($cek_user);
        
        // Verifikasi password yang sudah dienkripsi
        if (password_verify($password, $row['password'])) {
            // Set session agar user tetap login
            $_SESSION['login'] = true;
            $_SESSION['id_user'] = $row['id'];
            $_SESSION['nama_lengkap'] = $row['nama_lengkap'];
            $_SESSION['role'] = $row['role']; // MENYIMPAN ROLE KE SESSION
            
            // LOGIKA REDIRECT BERDASARKAN ROLE
            if ($row['role'] === 'admin') {
                header("Location: admin/dashboard.php"); 
            } else {
                header("Location: booking/booking.PHP"); 
            }
            exit;
        } else {
            $pesan = "<p style='color: #d32f2f; text-align: center;'>Password salah! Rem mendadak.</p>";
        }
    } else {
        $pesan = "<p style='color: #d32f2f; text-align: center;'>Email tidak ditemukan di sirkuit.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Go-Kart Racing Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="panel" style="max-width: 500px; margin: 100px auto;">
            <h2 class="panel-header" style="text-align: center;">🏁 PIT LANE LOGIN</h2>
            <p class="panel-desc" style="text-align: center;">Masukkan kredensialmu untuk mengakses sirkuit</p>

            <?= $pesan; ?>

            <form action="" method="POST" class="license-form">
                <input type="email" name="email" placeholder="Alamat Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">ENTER TRACK</button>
            </form>

            <p style="text-align: center; color: #aaa; font-size: 14px; margin-top: 20px;">
                Rookie baru? <a href="register.php" style="color: #ff5722; text-decoration: none; font-weight: bold;">Daftar Lisensi di sini</a>
            </p>
        </div>
    </div>
</body>
</html>