<?php
session_start(); 

// 1. Proteksi Halaman
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

require '../koneksi.php'; 

$user_id = $_SESSION['id_user']; 
// Ambil ID booking dari URL (Detail berdasarkan ID)
$booking_id = isset($_GET['id']) ? mysqli_real_escape_string($koneksi, $_GET['id']) : 0;

// 2. Ambil data user untuk detail info di sidebar
$user_query = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$user_id'");
$user       = mysqli_fetch_assoc($user_query);

// 3. Fetch Data Booking Detail secara Spesifik
$query = mysqli_query($koneksi, "
    SELECT b.*, p.nama_paket, p.durasi_menit, p.harga as harga_paket, u.nama_lengkap as nama_user
    FROM booking b
    JOIN paket_bermain p ON b.paket_id = p.id
    JOIN users u ON b.user_id = u.id
    WHERE b.id = '$booking_id' AND b.user_id = '$user_id'
");

$data = mysqli_fetch_assoc($query);

// Jika data manipulasi URL tidak ditemukan, kembalikan ke daftar riwayat utama
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
    <link rel="stylesheet" href="../booking/style1.css">
    <style>
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
            background: var(--white);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
        }

        .info-card h3 {
            margin-bottom: 20px;
            color: var(--text-dark);
            font-size: 1.1rem;
            border-left: 4px solid var(--primary);
            padding-left: 15px;
        }

        .info-row { 
            display: flex; 
            justify-content: space-between; 
            padding: 12px 0; 
            border-bottom: 1px solid var(--border-color); 
        }

        .info-row:last-child { border-bottom: none; }
        .label { color: var(--text-gray); font-weight: 500; }
        .value { color: var(--text-dark); font-weight: 600; text-align: right; }

        /* Pewarnaan Status Badge dinamis */
        .status {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: bold;
        }
        .status.aktif, .status.sukses, .status.selesai { background: #d4edda; color: #155724; }
        .status.pending { background: #fff3cd; color: #856404; }
        .status.batal { background: #f8d7da; color: #721c24; }

        .btn-group { 
            display: flex; 
            gap: 15px; 
            margin-top: 20px; 
        }

        .btn-action {
            flex: 1;
            padding: 12px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            border: none;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
        }
        .btn-action.primary { background: var(--primary); color: white; }
        .btn-action.primary:hover { background: var(--primary-dark); }
        .btn-action.secondary { background: var(--light); color: var(--dark); border: 1px solid var(--border-color); }
        .btn-action.secondary:hover { background: #e2e8f0; }

        @media (max-width: 768px) {
            .btn-group { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="brand">
                <h2>GOKART RACING</h2>
                <p>Management System</p>
            </div>
            <nav class="nav-menu">
                <a href="../dashboard/dashboard.php" class="nav-link">Dashboard</a>
                <a href="../booking/booking.php" class="nav-link">Booking</a>
                <a href="riwayat.php" class="nav-link active">Riwayat Booking</a>
                <a href="../hasil_balapan/hasil_balapan.php" class="nav-link">Hasil Balapan</a>
                <a href="../leaderboard/leaderboard.php" class="nav-link">Leaderboard</a>
                <a href="../pembayaran/pembayaran.php" class="nav-link">Pembayaran</a>
                <a href="../Profil/profil.php" class="nav-link">Profil Saya</a>
            </nav>
            <div class="sidebar-bottom">
                <div class="sidebar-user">
                    <p><strong><?= htmlspecialchars($user['nama_lengkap']); ?></strong></p>
                    <small><?= htmlspecialchars($user['email']); ?></small>
                </div>
                <button class="logout-btn" onclick="if(confirm('Keluar sistem?')) location.href='../logout.php'">Logout</button>
            </div>
        </aside>

        <main class="main">
            <header class="page-header">
                <a href="riwayat.php" style="text-decoration: none; color: var(--primary); font-weight: 600; display: inline-block; margin-bottom: 10px;">
                    ← Kembali ke Daftar Riwayat
                </a>
                <h1>Detail Pesanan #BK<?= str_pad($data['id'], 3, '0', STR_PAD_LEFT); ?></h1>
            </header>

            <div class="detail-container">
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
                    <div class="info-row" style="margin-top: 15px; border-top: 2px solid var(--border-color); padding-top: 20px;">
                        <span class="label" style="font-size: 1.1rem; color: var(--text-dark);">Total Bayar</span>
                        <span class="value" style="color: var(--primary); font-size: 1.3rem;">Rp <?= number_format($data['total_harga'], 0, ',', '.'); ?></span>
                    </div>
                </div>

                <div class="btn-group">
                    <?php if (strtolower($data['status']) == 'aktif' || strtolower($data['status']) == 'pending'): ?>
                        <a href="../pembayaran/pembayaran.php?id=<?= $data['id']; ?>" class="btn-action primary">
                            Pilih Metode & Bayar Sekarang
                        </a>
                    <?php endif; ?>
                    <button class="btn-action secondary" onclick="window.print()">
                        Cetak Nota Booking
                    </button>
                </div>
            </div>
        </main>
    </div>
</body>
</html>