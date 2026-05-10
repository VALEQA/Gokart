<?php
session_start(); // WAJIB ada di baris pertama untuk membaca data login

// Proteksi: Jika belum login, tendang balik ke halaman login
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

require '../koneksi.php'; // Pastikan nama file koneksi benar

// Ambil ID User dari session (Sesuai dengan $_SESSION['id_user'] di login.php)
$user_id = $_SESSION['id_user']; 

// Ambil data user yang sedang login dari database
$user_query = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($user_query);

// Ambil semua paket dari database
$paket_query = mysqli_query($koneksi, "SELECT * FROM paket_bermain");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoKart Racing - Booking</title>
    <link rel="stylesheet" href="style1.css">
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
                <li><a href="../dashboard/dashboard.php" class="nav-link">Dashboard</a></li>
                <li><a href="#" class="nav-link active">Booking</a></li>
                <li><a href="../riwayat booking/riwayat.php" class="nav-link">Riwayat Booking</a></li>
                <li><a href="#" class="nav-link">Hasil Balapan</a></li>
                <li><a href="#" class="nav-link">Leaderboard</a></li>
            </ul>
        </div>
        <div class="sidebar-bottom">
            <div class="sidebar-user">
                <p><strong><?= htmlspecialchars($user['nama_lengkap']); ?></strong></p>
                <small><?= htmlspecialchars($user['email']); ?></small>
            </div>
            <button class="logout-btn">Logout</button>
        </div>
    </aside>

    <main class="main">
        <header class="page-header">
            <h1>Booking Sekarang</h1>
            <p>Pilih paket balapan yang sesuai untuk Anda</p>
        </header>

        <div class="booking-grid">
            <div class="booking-form">
                <section class="card">
                    <h3>Pilih Tanggal</h3>
                    <input type="date" id="inputDate" class="input-field" value="<?= date('Y-m-d'); ?>">
                </section>

                <section class="card">
                    <h3>Pilih Jam</h3>
                    <div class="time-grid">
                        <?php 
                        $times = ['10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00' ];
                        foreach ($times as $idx => $t): 
                        ?>
                            <button type="button" class="time-btn <?= $idx === 0 ? 'active' : ''; ?>">
                                <?= $t; ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="card">
                    <h3>Pilih Paket</h3>
                    <div class="paket-container">
                        <?php 
                        $first = true;
                        while($paket = mysqli_fetch_assoc($paket_query)): 
                        ?>
                            <!-- CRITICAL: Atribut data-players harus sesuai dengan isi database -->
                            <div class="paket-card <?= $first ? 'active' : ''; ?>" 
                                 data-price="<?= $paket['harga']; ?>" 
                                 data-name="<?= $paket['nama_paket']; ?>" 
                                 data-duration="<?= $paket['durasi_menit']; ?> menit" 
                                 data-players="<?= $paket['maksimal_orang']; ?>">
                                
                                <h4><?= htmlspecialchars($paket['nama_paket']); ?></h4>
                                <p><?= htmlspecialchars($paket['deskripsi']); ?></p>
                                <div class="paket-info">
                                    <span>⏱ <?= $paket['durasi_menit']; ?> mnt</span><br>
                                    <span>👤 Max <?= $paket['maksimal_orang']; ?> orang</span>
                                </div>
                                <strong>Rp <?= number_format($paket['harga'], 0, ',', '.'); ?></strong>
                            </div>
                        <?php 
                        $first = false;
                        endwhile; 
                        ?>
                    </div>
                </section>
            </div>

            <aside class="booking-summary">
                <div class="card summary-card">
                    <h3>Ringkasan Booking</h3>
                    <hr>
                    <div class="summary-item">
                        <span>Tanggal</span>
                        <strong id="sumDate">-</strong>
                    </div>
                    <div class="summary-item">
                        <span>Jam</span>
                        <strong id="sumTime">-</strong>
                    </div>
                    <div class="summary-item">
                        <span>Paket</span>
                        <strong id="sumPaket">-</strong>
                    </div>
                    <div class="summary-item">
                        <span>Durasi</span>
                        <strong id="sumDuration">-</strong>
                    </div>
                    <div class="summary-item">
                        <span>Kapasitas</span>
                        <strong id="sumPlayers">-</strong>
                    </div>
                    <hr>
                    <div class="total-section">
                        <p>Total Harga</p>
                        <h2 id="sumTotal">Rp 0</h2>
                    </div>
                    <button class="confirm-btn">Konfirmasi Booking</button>
                </div>
            </aside>
        </div>
    </main>
</div>

<script src="booking.js"></script>
</body>
</html>