<?php
require 'koneksi.php';
$pesan = "";

if (isset($_POST['daftar'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $hp = mysqli_real_escape_string($koneksi, $_POST['nomor_hp']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    // Password tidak perlu di-escape karena akan di-hash
    $password = $_POST['password']; 

    // 1. Cek apakah email sudah terdaftar
    $cek_email = mysqli_query($koneksi, "SELECT email FROM users WHERE email = '$email'");
    
    if (mysqli_num_rows($cek_email) > 0) {
        $pesan = "<p style='color: #d32f2f; text-align: center;'>Email sudah terdaftar! Gunakan email lain.</p>";
    } else {
        // 2. Enkripsi password
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);
        
        // 3. Masukkan data (Default role adalah 'user')
        $query = "INSERT INTO users (nama_lengkap, nomor_hp, email, password, role) 
                  VALUES ('$nama', '$hp', '$email', '$password_hashed', 'user')";
        
        if (mysqli_query($koneksi, $query)) {
            $pesan = "<p style='color: #00e676; text-align: center;'>Pendaftaran berhasil! Silakan <a href='login.php' style='color:#ff5722; font-weight:bold;'>Login ke Pit Lane</a>.</p>";
        } else {
            $pesan = "<p style='color: #d32f2f; text-align: center;'>Terjadi kesalahan sistem: " . mysqli_error($koneksi) . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Go-Kart Racing Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="panel" style="max-width: 500px; margin: 50px auto;">
            <h2 class="panel-header" style="text-align: center;">🏁 RACER REGISTRATION</h2>
            <p class="panel-desc" style="text-align: center;">Daftarkan dirimu untuk mendapatkan Super License</p>
            
            <?= $pesan; ?>

            <form action="" method="POST" class="license-form">
                <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required autocomplete="off">
                <input type="number" name="nomor_hp" placeholder="Nomor HP (Contoh: 0812...)" required>
                <input type="email" name="email" placeholder="Alamat Email" required>
                <input type="password" name="password" placeholder="Password Baru" required>
                
                <button type="submit" name="daftar" style="margin-top: 10px;">START ENGINE (DAFTAR)</button>
            </form>
            
            <p style="text-align: center; color: #aaa; font-size: 14px; margin-top: 20px;">
                Sudah punya lisensi? <a href="login.php" style="color: #ff5722; text-decoration: none; font-weight: bold;">Login ke Pit Lane</a>
            </p>
        </div>
    </div>
</body>
</html>