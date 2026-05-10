<?php
session_start(); // Wajib di baris pertama

// 1. Proteksi: Jika belum login, tendang ke login.php
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

require '../koneksi.php'; // Pastikan path ke koneksi.php benar

/**
 * ==========================================
 * DATA USER DARI SESSION
 * ==========================================
 */
$user_id = $_SESSION['id_user']; 

// Ambil data user untuk sidebar (Gunakan nama_lengkap)
$user_query = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$user_id'");
$user       = mysqli_fetch_assoc($user_query);

/**
 * ==========================================
 * FETCH SEMUA RIWAYAT BOOKING (Urutan Terbaru)
 * ==========================================
 */
$query_riwayat = mysqli_query($koneksi, "
    SELECT b.*, p.nama_paket, p.durasi_menit, b.total_harga
    FROM booking b
    JOIN paket_bermain p ON b.paket_id = p.id
    WHERE b.user_id = '$user_id'
    ORDER BY b.tanggal_booking DESC, b.jam_booking DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Booking - GoKart Racing</title>
    <!-- Memanggil CSS Global -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="brand">
                <h2>GOKART RACING</h2>
                <p>Management System</p>
            </div>
                <nav class="nav-menu">
                    <a href="../dashboard/dashboard.php" class="nav-link">Dashboard</a>
                    <a href="../booking/booking.php" class="nav-link">Booking</a>
                    <a href="../riwayat booking/riwayat.php" class="nav-link">Riwayat Booking</a>
                    <a href="#" class="nav-link">Hasil Balapan</a>
                    <a href="#" class="nav-link">Leaderboard</a>
                    <li><a href="../Profil_sayaGokart/profil_saya.php" class="nav-link">Profil Saya</a></li>
                </nav>
            <div class="sidebar-bottom">
                <div class="sidebar-user">
                    <p><strong><?= htmlspecialchars($user['nama_lengkap']); ?></strong></p>
                    <small><?= htmlspecialchars($user['email']); ?></small>
                </div>
                <button class="logout-btn" id="btnLogout">Logout</button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main">
            <header class="page-header">
                <h1>Riwayat Booking</h1>
                <p>Pantau status dan detail pesanan Anda</p>
            </header>

            <div class="riwayat-list">
                <?php if (mysqli_num_rows($query_riwayat) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($query_riwayat)): ?>
                        <div class="riwayat-card">
                            <div class="booking-info">
                                <h3>
                                    #BK<?= str_pad($row['id'], 3, '0', STR_PAD_LEFT); ?> 
                                    <span class="status <?= strtolower($row['status']); ?>">
                                        <?= ucfirst($row['status']); ?>
                                    </span>
                                </h3>
                                <div class="details">
                                    <span>📅 <?= date('d M Y', strtotime($row['tanggal_booking'])); ?></span>
                                    <span>⏰ <?= date('H:i', strtotime($row['jam_booking'])); ?> WIB</span>
                                    <span>🏎️ <?= htmlspecialchars($row['nama_paket']); ?> (<?= $row['durasi_menit']; ?> Menit)</span>
                                </div>
                            </div>
                            <div class="action">
                                <p class="price">Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></p>
                                
                                <!-- Semua tombol diarahkan ke detail_booking.php dengan ID spesifik -->
                                <?php if ($row['status'] == 'pending'): ?>
                                    <a href="detail_booking.php?id=<?= $row['id']; ?>" class="btn-action primary" style="text-decoration: none;">
                                        Bayar Sekarang
                                    </a>
                                <?php elseif ($row['status'] == 'selesai'): ?>
                                    <a href="detail_booking.php?id=<?= $row['id']; ?>" class="btn-action secondary" style="text-decoration: none;">
                                        Lihat Invoice
                                    </a>
                                <?php else: ?>
                                    <a href="detail_booking.php?id=<?= $row['id']; ?>" class="btn-action secondary" style="text-decoration: none;">
                                        Lihat Detail
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="card" style="text-align: center; padding: 50px;">
                        <p style="color: var(--text-gray);">Belum ada riwayat booking apapun.</p>
                        <a href="../booking/booking.php" class="btn-action primary" style="text-decoration: none; display: inline-block; margin-top: 15px;">
                            Booking Sekarang
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script src="script.js"></script>
</body>
</html>