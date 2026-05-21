<?php
session_start(); // Membaca session login user

// Proteksi halaman: Kembali ke login jika belum terautentikasi
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

require '../koneksi.php'; // Hubungkan ke database utama

$user_id = $_SESSION['id_user']; 

// Ambil data profil user terbaru untuk form dan info sidebar
$user_query = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$user_id'");
$user_logged = mysqli_fetch_assoc($user_query);

// Menentukan fallback nama kolom jika ada perbedaan struktur di DB kamu
$nama_lengkap = $user_logged['nama_lengkap'] ?? $user_logged['nama'] ?? '';
$nomor_hp = $user_logged['nomor_hp'] ?? $user_logged['no_telepon'] ?? $user_logged['no_telpon'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoKart Racing - Profil Saya</title>
    <link rel="stylesheet" href="style.css"> </head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-top">
                <div class="brand">
                    <h2>GoKart Racing</h2>
                    <p>Booking System</p>
                </div>
                <ul class="nav-menu">
                    <li><a href="../dashboard/dashboard.php" class="nav-link"><span>Dashboard</span></a></li>
                    <li><a href="../booking/booking.php" class="nav-link"><span>Booking</span></a></li>
                    <li><a href="../riwayat booking/riwayat.php" class="nav-link"><span>Riwayat Booking</span></a></li>
                    <li><a href="../hasil_balapan/hasil_balapan.php" class="nav-link"><span>Hasil Balapan</span></a></li>
                    <li><a href="../leaderboard/leaderboard.php" class="nav-link"><span>Leaderboard</span></a></li>
                    <li><a href="../pembayaran/pembayaran.php" class="nav-link"><span>Pembayaran</span></a></li>
                    <li><a href="../Profil/profil.php" class="nav-link active"><span>Profil Saya</span></a></li>
                </ul>
            </div>
            <div class="sidebar-bottom">
                <div class="sidebar-user">
                    <p><strong><?= htmlspecialchars($nama_lengkap); ?></strong></p>
                    <small><?= htmlspecialchars($user_logged['email']); ?></small>
                </div>
                <button class="logout-btn" onclick="if(confirm('Apakah Anda yakin ingin keluar dari sistem GoKart Racing?')) window.location.href='../logout.php'">
                    <span>Logout</span>
                </button>
            </div>
        </aside>

        <main class="main">
            <header class="header">
                <h1>Profil Saya</h1>
                <p>Kelola informasi akun dan amankan kata sandi Anda</p>
            </header>

            <div class="profile-grid">
                <section class="card">
                    <h3 style="border-left: 4px solid var(--primary); padding-left: 10px; margin-bottom: 1.5rem; color: var(--dark);">Data Diri Pembalap</h3>
                    
                    <form action="update_profil.php" method="POST">
                        <input type="hidden" name="id" value="<?= $user_logged['id']; ?>">
                        <input type="hidden" name="aksi" value="ubah_profil">

                        <div class="form-group">
                            <label>Alamat Email (ID Akun)</label>
                            <input type="email" name="email" class="input-field input-disabled" value="<?= htmlspecialchars($user_logged['email']); ?>" readonly>
                            <small style="color: var(--gray); font-size: 0.75rem; display: block; margin-top: 0.2rem;">Email utama terkunci demi keamanan data transaksi.</small>
                        </div>

                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" class="input-field" value="<?= htmlspecialchars($nama_lengkap); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Nomor Telepon / WhatsApp</label>
                            <input type="text" name="no_telpon" class="input-field" value="<?= htmlspecialchars($nomor_hp); ?>" placeholder="Contoh: 08123456789" required>
                        </div>

                        <button type="submit" class="save-btn" style="margin-top: 1rem;">Simpan Perubahan Profil</button>
                    </form>
                </section>

                <section class="card">
                    <h3 style="border-left: 4px solid var(--primary); padding-left: 10px; margin-bottom: 1.5rem; color: var(--dark);">Keamanan & Kata Sandi</h3>
                    
                    <form action="update_profil.php" method="POST" id="passwordForm">
                        <input type="hidden" name="id" value="<?= $user_logged['id']; ?>">
                        <input type="hidden" name="aksi" value="ubah_password">

                        <div class="form-group">
                            <label>Password Saat Ini</label>
                            <input type="password" name="password_lama" class="input-field" placeholder="Masukkan password lama Anda" required>
                        </div>

                        <div class="form-group">
                            <label>Password Baru</label>
                            <input type="password" name="password_baru" id="passwordBaru" class="input-field" placeholder="Minimal 6 karakter" required>
                        </div>

                        <div class="form-group">
                            <label>Konfirmasi Password Baru</label>
                            <input type="password" name="konfirmasi_password" id="konfirmasiPassword" class="input-field" placeholder="Ulangi password baru Anda" required>
                        </div>

                        <button type="submit" class="save-btn" style="margin-top: 1rem; background-color: var(--dark);">Perbarui Kata Sandi</button>
                    </form>
                </section>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const passwordForm = document.getElementById("passwordForm");
            const passwordBaru = document.getElementById("passwordBaru");
            const konfirmasiPassword = document.getElementById("konfirmasiPassword");

            if (passwordForm) {
                passwordForm.addEventListener("submit", (e) => {
                    // Validasi panjang karakter password baru
                    if (passwordBaru.value.length < 6) {
                        e.preventDefault();
                        alert("Gagal! Password baru harus berukuran minimal 6 karakter.");
                        return;
                    }

                    // Validasi kecocokan konfirmasi password
                    if (passwordBaru.value !== konfirmasiPassword.value) {
                        e.preventDefault();
                        alert("Gagal! Input Password Baru dan Konfirmasi Password tidak cocok.");
                    }
                });
            }
        });
    </script>
</body>
</html>