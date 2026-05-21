<?php
session_start(); // Memulai session untuk membaca data user yang login

// 1. PROTEKSI HALAMAN: Jika belum login, tendang kembali ke login.php
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

require '../koneksi.php'; // Hubungkan ke file koneksi utama database Anda

// 2. AMBIL DATA USER DARI SESSION
$user_id = $_SESSION['id_user']; 

/**
 * ==========================================================================
 * DATA FETCHING & STATISTIK REAL-TIME (JALUR OTOMATIS)
 * ==========================================================================
 */

// A. Ambil Data Profil User dari Tabel 'users'
$user_query = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$user_id'");
$user       = mysqli_fetch_assoc($user_query);

// Antisipasi jika data user tiba-tiba tidak ada di database (misal setelah dibersihkan)
if (!$user) {
    session_destroy();
    header("Location: ../login.php");
    exit;
}

// Ambil kata pertama dari nama lengkap untuk sapaan hangat di header
$first_name = explode(' ', $user['nama_lengkap'])[0];

// B. STATISTIK 1: Total Selesai Bermain (Dihitung otomatis dari tabel booking)
$total_query = mysqli_query($koneksi, "
    SELECT COUNT(*) as total 
    FROM booking 
    WHERE user_id = '$user_id' AND status = 'selesai'
");
$total = mysqli_fetch_assoc($total_query)['total'];

// C. STATISTIK 2: Waktu Terbaik 1 Lap (Mencari nilai terkecil/MIN dari hasil_balapan)
$best_query = mysqli_query($koneksi, "
    SELECT MIN(total_lap) as best_time 
    FROM hasil_balapan 
    WHERE user_id = '$user_id'
");
$best_data = mysqli_fetch_assoc($best_query);
$best_time = ($best_data && $best_data['best_time'] > 0) ? number_format($best_data['best_time'], 3) . ' detik' : 'Belum Ada';

// D. STATISTIK 3: Booking Aktif (Menghitung booking yang statusnya masih 'aktif')
$aktif_query = mysqli_query($koneksi, "
    SELECT COUNT(*) as total 
    FROM booking 
    WHERE user_id = '$user_id' AND status = 'aktif'
");
$aktif_count = mysqli_fetch_assoc($aktif_query)['total'];

// E. LISTING: Daftar Semua Jadwal Booking Aktif Milik User Terkait
$next_query = mysqli_query($koneksi, "
    SELECT b.*, p.nama_paket, p.durasi_menit
    FROM booking b
    JOIN paket_bermain p ON b.paket_id = p.id
    WHERE b.user_id = '$user_id' AND b.status = 'aktif'
    ORDER BY b.tanggal_booking ASC, b.jam_booking ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoKart Racing - Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Mengubah container aksi cepat agar otomatis muat 4 kolom sebaris di PC */
        .aksi-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
        }

        /* Responsif untuk layar tablet dan HP agar tidak sempit */
        @media (max-width: 1200px) {
            .aksi-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 576px) {
            .aksi-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <aside class="sidebar">
        <div class="sidebar-top">
            <div class="brand">
                <h2>GoKart Racing</h2>
                <p>Booking System</p>
            </div>
            <ul class="nav-menu">
                <li><a href="../dashboard/dashboard.php" class="nav-link active">Dashboard</a></li>
                <li><a href="../booking/booking.php" class="nav-link">Booking</a></li>
                <li><a href="../riwayat booking/riwayat.php" class="nav-link">Riwayat Booking</a></li>
                <li><a href="../hasil_balapan/hasil_balapan.php" class="nav-link">Hasil Balapan</a></li>
                <li><a href="../leaderboard/leaderboard.php" class="nav-link">Leaderboard</a></li>
                <li><a href="../pembayaran/pembayaran.php" class="nav-link">Pembayaran</a></li>
                <li><a href="../Profil/profil.php" class="nav-link">Profil Saya</a></li>
            </ul>       
        </div>

        <div class="sidebar-bottom">
            <div class="sidebar-user">
                <p><strong><?= htmlspecialchars($user['nama_lengkap']); ?></strong></p>
                <small><?= htmlspecialchars($user['email']); ?></small>
            </div>
            <button class="logout-btn" onclick="if(confirm('Keluar dari sistem?')) window.location.href='../logout.php'">Logout</button>
        </div>
    </aside>

    <main class="main">
        <header class="header">
            <h1>Halo, <?= htmlspecialchars($first_name); ?>!</h1>
            <p>Siap untuk memecahkan rekor waktu hari ini? 🏁</p>
        </header>

        <section class="stats">
            <div class="card stat-card">
                <p>Total Bermain</p>
                <h3 class="stat-number"><?= $total; ?></h3>
            </div>

            <div class="card stat-card">
                <p>Waktu Terbaik (1 Lap)</p>
                <h3 class="stat-number"><?= $best_time; ?></h3>
            </div>

            <div class="card stat-card">
                <p>Booking Aktif</p>
                <h3 class="stat-number"><?= $aktif_count; ?></h3>
            </div>
        </section>

        <section class="booking">
            <h2>Booking Berikutnya</h2>
            <div class="booking-list">
                <?php if (mysqli_num_rows($next_query) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($next_query)): ?>
                        <div class="card booking-box" style="margin-bottom: 1rem;">
                            <div class="booking-left">
                                <p class="booking-date">
                                    📅 <?= date('d M Y', strtotime($row['tanggal_booking'])); ?> | 
                                    ⏰ <?= date('H:i', strtotime($row['jam_booking'])); ?>
                                </p>
                                <div class="booking-detail">
                                    <span><strong>Paket:</strong> <?= htmlspecialchars($row['nama_paket']); ?></span>
                                    <span><strong>Durasi:</strong> <?= $row['durasi_menit']; ?> Menit</span>
                                    <span><strong>Pemain:</strong> <?= $row['jumlah_orang']; ?> Orang</span>
                                </div>
                            </div>
                            <div class="booking-right">
                                <button class="btn btn-secondary" onclick="window.location.href='../riwayat booking/riwayat.php'">
                                    Detail
                                </button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="card booking-box">
                        <p class="empty-state">Belum ada jadwal balapan aktif. Yuk, booking sekarang!</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="aksi">
            <h2>Aksi Cepat</h2>
            <div class="aksi-container">
                <div class="card aksi-box clickable" onclick="window.location.href='../booking/booking.php'">
                    <h3>Booking Sekarang</h3>
                    <p>Pilih jadwal dan mulai balapan</p>
                </div>

                <div class="card aksi-box clickable" onclick="window.location.href='../leaderboard/leaderboard.php'">
                    <h3>Lihat Leaderboard</h3>
                    <p>Cek ranking pemain terbaik</p>
                </div>

                <div class="card aksi-box clickable" onclick="window.location.href='../Profil/profil.php'">
                    <h3>Profil Saya</h3>
                    <p>Kelola data diri dan keamanan sandi</p>
                </div>

                <div class="card aksi-box clickable" onclick="window.location.href='../lisensi/lisensi.php'">
                    <h3>Lisensi Pembalap</h3>
                    <p>Lihat status nomor lisensi balap Anda</p>
                </div>
            </div>
        </section>

    </main>
</div>

<script src="script.js"></script>
</body>
</html>