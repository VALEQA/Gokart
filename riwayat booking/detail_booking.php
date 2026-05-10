<?php
session_start(); // Baris 1: Wajib untuk membaca session login

// 1. Proteksi Halaman
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

require '../koneksi.php'; // Sesuaikan path koneksi

/**
 * ==========================================
 * KONFIGURASI DATA DARI SESSION
 * ==========================================
 */
$user_id = $_SESSION['id_user']; 
// Ambil ID booking dari URL (misal: detail.php?id=5)
$booking_id = isset($_GET['id']) ? mysqli_real_escape_string($koneksi, $_GET['id']) : 0;

// 2. Ambil data user untuk sidebar (Gunakan nama_lengkap sesuai DB)
$user_query = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$user_id'");
$user       = mysqli_fetch_assoc($user_query);

// 3. Fetch Data Booking Detail
// Note: Saya sesuaikan u.nama menjadi u.nama_lengkap
$query = mysqli_query($koneksi, "
    SELECT b.*, p.nama_paket, p.durasi_menit, p.harga as harga_paket, u.nama_lengkap as nama_user
    FROM booking b
    JOIN paket_bermain p ON b.paket_id = p.id
    JOIN users u ON b.user_id = u.id
    WHERE b.id = '$booking_id' AND b.user_id = '$user_id'
");

$data = mysqli_fetch_assoc($query);

// Jika data tidak ditemukan atau bukan milik user yang login, kembalikan ke riwayat
if (!$data) {
    header("Location: riwayat.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Booking #<?= $booking_id; ?> - GoKart Racing</title>
    <!-- Memanggil CSS Global -->
    <link rel="stylesheet" href="style.css">
    <style>
        /* Tambahan CSS Khusus Detail agar sesuai dengan UI sebelumnya */
        .detail-container { 
            max-width: 800px; 
            margin: 0 auto; 
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .info-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid #edf2f7;
        }

        .info-card h3 {
            margin-bottom: 20px;
            color: #2d3748;
            font-size: 1.1rem;
            border-left: 4px solid var(--primary);
            padding-left: 15px;
        }

        .info-row { 
            display: flex; 
            justify-content: space-between; 
            padding: 12px 0; 
            border-bottom: 1px solid #f1f1f1; 
        }

        .info-row:last-child { border-bottom: none; }

        .label { color: #718096; font-weight: 500; }
        .value { color: #2d3748; font-weight: 600; text-align: right; }

        .qr-section { 
            text-align: center; 
            background: #f8fafc; 
            border: 2px dashed #e2e8f0;
            border-radius: 15px;
            padding: 30px;
        }

        .qr-section img {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            background: white;
            padding: 10px;
        }

        .btn-group { 
            display: flex; 
            gap: 15px; 
            margin-top: 30px; 
        }

        .btn-group .btn-action {
            flex: 1;
            justify-content: center;
            padding: 15px;
            text-decoration: none;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .btn-group { flex-direction: column; }
        }
    </style>
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
                <a href="../profil/profil.php" class="nav-link">Profil Saya</a>
            </nav>
            <div class="sidebar-bottom">
                <div class="sidebar-user">
                    <p><strong><?= htmlspecialchars($user['nama_lengkap']); ?></strong></p>
                    <small><?= htmlspecialchars($user['email']); ?></small>
                </div>
                <button class="logout-btn" onclick="location.href='../logout.php'">Logout</button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main">
            <header class="page-header">
                <a href="riwayat.php" style="text-decoration: none; color: var(--primary); font-weight: 600; display: inline-block; margin-bottom: 10px;">
                    ← Kembali ke Riwayat
                </a>
                <h1>Detail Pesanan #BK<?= str_pad($data['id'], 3, '0', STR_PAD_LEFT); ?></h1>
            </header>

            <div class="detail-container">
                <!-- Info Status -->
                <div class="info-card">
                    <h3>Informasi Status</h3>
                    <div class="info-row">
                        <span class="label">ID Transaksi</span>
                        <span class="value">#BK<?= str_pad($data['id'], 3, '0', STR_PAD_LEFT); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Status Pesanan</span>
                        <span class="value">
                            <span class="status <?= strtolower($data['status']); ?>">
                                <?= ucfirst($data['status']); ?>
                            </span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="label">Nama Pemesan</span>
                        <span class="value"><?= htmlspecialchars($data['nama_user']); ?></span>
                    </div>
                </div>

                <!-- Detail Paket -->
                <div class="info-card">
                    <h3>Rincian Balapan</h3>
                    <div class="info-row">
                        <span class="label">Tanggal Bermain</span>
                        <span class="value"><?= date('d F Y', strtotime($data['tanggal_booking'])); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Sesi Waktu</span>
                        <span class="value"><?= date('H:i', strtotime($data['jam_booking'])); ?> WIB</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Paket Balapan</span>
                        <span class="value"><?= htmlspecialchars($data['nama_paket']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Durasi</span>
                        <span class="value"><?= $data['durasi_menit']; ?> Menit</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Jumlah Pemain</span>
                        <span class="value"><?= $data['jumlah_orang']; ?> Orang</span>
                    </div>
                    <div class="info-row" style="margin-top: 15px; border-top: 2px solid #f1f1f1; padding-top: 20px;">
                        <span class="label" style="font-size: 1.1rem; color: #2d3748;">Total Bayar</span>
                        <span class="value" style="color: var(--primary); font-size: 1.3rem;">Rp <?= number_format($data['total_harga'], 0, ',', '.'); ?></span>
                    </div>
                </div>

                <!-- Bagian QR Code (Jika Pending) -->
                <?php if ($data['status'] == 'pending'): ?>
                    <div class="qr-section">
                        <h4>Scan QRIS untuk Pembayaran</h4>
                        <p style="color: #718096; margin-bottom: 20px; font-size: 0.9rem;">Silahkan selesaikan pembayaran sebelum waktu berakhir.</p>
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=GOKART_PAY_<?= $data['id']; ?>" alt="QR Code">
                        <p style="margin-top: 20px; font-weight: 500;">Virtual Account: <span style="color: var(--primary);">8829 0000 <?= str_pad($data['id'], 4, '0', STR_PAD_LEFT); ?></span></p>
                    </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div class="btn-group">
                    <?php if ($data['status'] == 'pending'): ?>
                        <button class="btn-action primary" onclick="alert('Konfirmasi pembayaran terkirim! Admin akan mengecek secara manual.')">
                            Konfirmasi Pembayaran
                        </button>
                    <?php endif; ?>
                    
                    <button class="btn-action secondary" onclick="window.print()">
                        Cetak Bukti Booking
                    </button>
                </div>
            </div>
        </main>
    </div>
</body>
</html>