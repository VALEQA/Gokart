<?php
session_start();

// Proteksi halaman admin: Pastikan user sudah login DAN memiliki role 'admin'
// Catatan: Jika di databasemu kolom role bernama lain (misal is_admin = 1), silakan sesuaikan kondisinya.
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require '../koneksi.php';

// 1. Ambil data statistik untuk Counter Cards
$total_users = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users WHERE role = 'user'"))['total'];
$booking_masuk = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM booking WHERE status = 'aktif'"))['total'];
$booking_selesai = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM booking WHERE status = 'selesai'"))['total'];
$total_pendapatan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(total_harga) as total FROM booking WHERE status = 'selesai'"))['total'];

// 2. Ambil daftar booking yang berstatus 'aktif' (Menunggu Validasi Pembayaran)
$pembayaran_query = mysqli_query($koneksi, "
    SELECT b.*, u.nama_lengkap, u.email, p.nama_paket 
    FROM booking b
    INNER JOIN users u ON b.user_id = u.id
    INNER JOIN paket_bermain p ON b.paket_id = p.id
    WHERE b.status = 'aktif'
    ORDER BY b.created_at ASC
");

// 3. Proses Validasi Admin (Setujui / Tolak)
$pesan_aksi = "";
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id_booking = intval($_GET['id']);
    $action = $_GET['action'];
    
    if ($action === 'setujui') {
        // Jika disetujui, status booking diubah menjadi selesai/lunas
        $update = mysqli_query($koneksi, "UPDATE booking SET status = 'selesai' WHERE id = '$id_booking'");
        if ($update) {
            $pesan_aksi = "<div class='alert alert-success'>Booking #BK-$id_booking berhasil disetujui!</div>";
        }
    } elseif ($action === 'tolak') {
        // Jika ditolak, status diubah menjadi batal atau dihapus (sesuai kebijakan bisnis)
        $update = mysqli_query($koneksi, "UPDATE booking SET status = 'batal' WHERE id = '$id_booking'");
        if ($update) {
            $pesan_aksi = "<div class='alert alert-danger'>Booking #BK-$id_booking telah ditolak/dibatalkan.</div>";
        }
    }
    // Refresh halaman instan setelah 1 detik untuk memperbarui tabel data
    header("Refresh: 1.5; URL=dashboard.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoKart Admin - Dashboard</title>
    <link rel="stylesheet" href="style-admin.css">
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-top">
                <div class="brand">
                    <h2>GoKart Admin</h2>
                    <p>Management System</p>
                </div>
            <ul class="nav-menu">
                <li><a href="dashboard.php" class="nav-link active"><span>Dashboard</span></a></li>
               <li><a href="riwayat_keuangan.php" class="nav-link"><span>Riwayat Keuangan</span></a></li>
               <li><a href="leaderboard.php" class="nav-link"><span>Lihat Leaderboard</span></a></li>
            </ul>
            </div>
            <div class="sidebar-bottom">
                <div class="sidebar-user">
                    <p><strong>Admin Panel</strong></p>
                    <small>Administrator Sirkuit</small>
                </div>
                <button class="logout-btn" onclick="if(confirm('Keluar dari panel admin?')) window.location.href='../logout.php'">
                    <span>Logout</span>
                </button>
            </div>
        </aside>

        <main class="main">
            <header class="header">
                <h1>Overview Dashboard</h1>
                <p>Pantau performa bisnis dan validasi transaksi masuk hari ini</p>
            </header>

            <?= $pesan_aksi; ?>

            <section class="stats-grid">
                <div class="stat-card">
                    <span class="stat-icon">👥</span>
                    <div>
                        <h3>Total Racers</h3>
                        <p class="stat-number"><?= $total_users; ?> <span class="unit">orang</span></p>
                    </div>
                </div>
                <div class="stat-card alert-card">
                    <span class="stat-icon">⏳</span>
                    <div>
                        <h3>Butuh Validasi</h3>
                        <p class="stat-number"><?= $booking_masuk; ?> <span class="unit">transaksi</span></p>
                    </div>
                </div>
                <div class="stat-card success-card">
                    <span class="stat-icon">🏁</span>
                    <div>
                        <h3>Sesi Selesai</h3>
                        <p class="stat-number"><?= $booking_selesai; ?> <span class="unit">sesi</span></p>
                    </div>
                </div>
                <div class="stat-card money-card">
                    <span class="stat-icon">💰</span>
                    <div>
                        <h3>Total Omset</h3>
                        <p class="stat-number">Rp <?= number_format($total_pendapatan ?? 0, 0, ',', '.'); ?></p>
                    </div>
                </div>
            </section>

            <section class="card table-container" style="margin-top: 2rem;">
                <h3 style="border-left: 4px solid var(--dark); padding-left: 10px; margin-bottom: 1.5rem;">Persetujuan Pembayaran Masuk</h3>
                
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Booking</th>
                                <th>Nama Pembalap</th>
                                <th>Paket & Sesi</th>
                                <th style="text-align: right;">Total Tagihan</th>
                                <th style="text-align: center;">Bukti</th>
                                <th style="text-align: center;">Aksi Kontrol</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($pembayaran_query) == 0): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--gray); padding: 3rem; font-style: italic;">
                                    Belum ada transaksi baru yang memerlukan validasi admin.
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php while ($row = mysqli_fetch_assoc($pembayaran_query)): ?>
                                <tr>
                                    <td><strong>#BK-<?= $row['id']; ?></strong></td>
                                    <td>
                                        <strong><?= htmlspecialchars($row['nama_lengkap']); ?></strong><br>
                                        <small style="color: var(--gray);"><?= htmlspecialchars($row['email']); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge-paket"><?= htmlspecialchars($row['nama_paket']); ?></span><br>
                                        <small><?= date('d M Y', strtotime($row['tanggal_booking'])); ?> - Sesi <?= date('H:i', strtotime($row['jam_booking'])); ?></small>
                                    </td>
                                    <td style="text-align: right; font-weight: bold; color: #1d3557;">
                                        Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="../pembayaran/uploads/bukti_contoh.png" target="_blank" class="view-proof-btn">Lihat Bukti</a>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="action-buttons">
                                            <a href="dashboard.php?action=setujui&id=<?= $row['id']; ?>" class="btn-approve" onclick="return confirm('Setujui pembayaran transaksi ini?')">Terima</a>
                                            <a href="dashboard.php?action=tolak&id=<?= $row['id']; ?>" class="btn-reject" onclick="return confirm('Tolak/Batalkan transaksi ini?')">Tolak</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>
</html>