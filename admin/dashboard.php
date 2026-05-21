<?php
session_start();

// 1. PROTEKSI HALAMAN ADMIN
// Memastikan user wajib login dan harus memiliki role 'admin'
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require '../koneksi.php';

// 2. PROSES VALIDASI TOMBOL (TERIMA / TOLAK) DENGAN UPDATE DOUBLE TABEL
$pesan_aksi = "";
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id_booking = intval($_GET['id']);
    $action = $_GET['action'];
    
    if ($action === 'setujui') {
        // A. Ambil data user_id terlebih dahulu dari data booking ini
        $booking_info = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT user_id FROM booking WHERE id = '$id_booking'"));
        $user_id_pembalap = $booking_info['user_id'];

        // B. Update status booking menjadi 'selesai' (Lunas sesuai ENUM database racinghub)
        $update = mysqli_query($koneksi, "UPDATE booking SET status = 'selesai' WHERE id = '$id_booking'");
        
        if ($update) {
            // C. UPDATE KE TABEL USERS (Menambah +1 total bermain langsung ke database XAMPP)
            // SINKRONISASI: Mengubah nilai fisik di database agar sinkron dengan counter dashboard
            mysqli_query($koneksi, "UPDATE users SET total_bermain = total_bermain + 1 WHERE id = '$user_id_pembalap'");

            $pesan_aksi = "<div class='alert alert-success'>Konfirmasi Berhasil! Booking #BK-$id_booking LUNAS & Data Total Bermain di Database XAMPP Berhasil Ditambahkan (+1).</div>";
        }
    } elseif ($action === 'tolak') {
        // Jika ditolak, kembalikan status ke 'aktif' dan kosongkan kolom bukti_transfer (NULL)
        // Hal ini memicu form upload di halaman user terbuka kembali untuk kirim ulang struk asli
        $update = mysqli_query($koneksi, "UPDATE booking SET status = 'aktif', bukti_transfer = NULL WHERE id = '$id_booking'");
        if ($update) {
            $pesan_aksi = "<div class='alert alert-danger'>Bukti Transfer #BK-$id_booking Ditolak! File dibersihkan dan user diminta unggah ulang.</div>";
        }
    }
    // Refresh halaman otomatis setelah 1.5 detik agar data angka counter & tabel langsung diperbarui
    header("Refresh: 1.5; URL=dashboard.php");
}

// 3. HITUNG STATISTIK COUNTER CARDS
// Menghitung jumlah user terdaftar dengan role pendaftar biasa
$total_users = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users WHERE role = 'user'"))['total'];

// Menghitung antrean konfirmasi (Hanya yang berstatus aktif DAN benar-benar sudah upload file gambar)
$booking_masuk = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM booking WHERE status = 'aktif' AND bukti_transfer IS NOT NULL AND bukti_transfer != ''"))['total'];

// Menghitung total seluruh sesi booking yang sukses divalidasi/lunas
$booking_selesai = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM booking WHERE status = 'selesai'"))['total'];

// Akumulasi total pendapatan uang masuk dari transaksi bernilai 'selesai'
$total_pendapatan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(total_harga) as total FROM booking WHERE status = 'selesai'"))['total'];

// 4. AMBIL DAFTAR TRANSAKSI YANG BUTUH ACC (Hanya menampilkan data yang sudah upload bukti)
$pembayaran_query = mysqli_query($koneksi, "
    SELECT b.*, u.nama_lengkap, u.email, u.nomor_hp, p.nama_paket 
    FROM booking b
    INNER JOIN users u ON b.user_id = u.id
    INNER JOIN paket_bermain p ON b.paket_id = p.id
    WHERE b.status = 'aktif' AND b.bukti_transfer IS NOT NULL AND b.bukti_transfer != ''
    ORDER BY b.created_at ASC
");
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
                    <li><a href="input_waktu.php" class="nav-link"><span>Input Waktu Balap</span></a></li>
                    <li><a href="leaderboard.php" class="nav-link"><span>Lihat Leaderboard</span></a></li>
                    <li><a href="kelola_paket.php" class="nav-link active"><span>Kelola Paket</span></a></li>
                    <li><a href="kelola_users.php" class="nav-link"><span>Kelola Users</span></a></li>
                </ul>
            </div>
            <div class="sidebar-bottom">
                <div class="sidebar-user">
                    <p><strong>Admin Panel</strong></p>
                    <small>Race Director</small>
                </div>
                <button class="logout-btn" onclick="if(confirm('Keluar dari panel admin?')) window.location.href='../logout.php'">
                    <span>Logout</span>
                </button>
            </div>
        </aside>

        <main class="main">
            <header class="header">
                <h1>Overview Dashboard</h1>
                <p>Verifikasi pembayaran sirkuit racing hub secara real-time</p>
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
                <h3 style="border-left: 4px solid var(--dark); padding-left: 10px; margin-bottom: 1.5rem;">Persetujuan Transaksi Masuk</h3>
                
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID Booking</th>
                                <th>Nama Pembalap</th>
                                <th>Paket Pilihan</th>
                                <th style="text-align: right;">Total Tagihan</th>
                                <th style="text-align: center;">Bukti Upload</th>
                                <th style="text-align: center;">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($pembayaran_query) == 0): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--gray); padding: 3rem; font-style: italic;">
                                    Belum ada kiriman bukti transfer baru dari pembalap yang perlu divalidasi.
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php while ($row = mysqli_fetch_assoc($pembayaran_query)): ?>
                                <tr>
                                    <td><strong>#BK-<?= $row['id']; ?></strong></td>
                                    <td>
                                        <strong><?= htmlspecialchars($row['nama_lengkap']); ?></strong><br>
                                        <small style="color: var(--gray);">HP: <?= htmlspecialchars($row['nomor_hp']); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge-paket"><?= htmlspecialchars($row['nama_paket']); ?></span><br>
                                        <small><?= date('d M Y', strtotime($row['tanggal_booking'])); ?> - Pukul <?= date('H:i', strtotime($row['jam_booking'])); ?></small>
                                    </td>
                                    <td style="text-align: right; font-weight: bold; color: #1d3557;">
                                        Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="../pembayaran/uploads/<?= $row['bukti_transfer']; ?>" target="_blank" class="view-proof-btn">Lihat Bukti</a>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="action-buttons">
                                            <a href="dashboard.php?action=setujui&id=<?= $row['id']; ?>" class="btn-approve" onclick="return confirm('Apakah uang transferan user ini benar-benar sudah masuk ke rekening?')">Terima</a>
                                            <a href="dashboard.php?action=tolak&id=<?= $row['id']; ?>" class="btn-reject" onclick="return confirm('Tolak bukti transfer ini dan minta user kirim ulang?')">Tolak</a>
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

    <script src="script.js"></script>
</body>
</html>