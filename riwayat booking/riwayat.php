<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

require '../koneksi.php';

$user_id = $_SESSION['id_user'];

// Ambil info user untuk sidebar
$user_query = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$user_id'");
$user_logged = mysqli_fetch_assoc($user_query);

// Ambil seluruh daftar antrean booking milik user ini
$riwayat_query = mysqli_query($koneksi, "
    SELECT b.*, p.nama_paket 
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
    <link class="styles" rel="stylesheet" href="../booking/style1.css">
    <style>
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            background: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
        }
        .riwayat-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        .riwayat-table th, .riwayat-table td {
            padding: 15px 20px;
            border-bottom: 1px solid var(--border-color);
        }
        .riwayat-table th {
            background-color: var(--bg-body);
            color: var(--text-dark);
            font-weight: 700;
        }
        .riwayat-table tr:last-child td { border: none; }
        .riwayat-table tr:hover { background-color: var(--primary-light); }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: bold;
            display: inline-block;
        }
        .status-badge.aktif { background: #d4edda; color: #155724; }
        .status-badge.pending { background: #fff3cd; color: #856404; }
        .status-badge.batal { background: #f8d7da; color: #721c24; }

        .btn-detail {
            padding: 6px 12px;
            background: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: var(--transition);
        }
        .btn-detail:hover { background: var(--primary-dark); }
        .empty-state { text-align: center; padding: 40px; color: var(--text-gray); }
    </style>
</head>
<body>
<div class="container">
    <aside class="sidebar">
        <div class="brand">
            <h2>GoKart Racing</h2>
            <p>Booking System</p>
        </div>
        <ul class="nav-menu">
            <li><a href="../dashboard/dashboard.php" class="nav-link">Dashboard</a></li>
            <li><a href="../booking/booking.php" class="nav-link">Booking</a></li>
            <li><a href="riwayat.php" class="nav-link active">Riwayat Booking</a></li>
            <li><a href="../hasil_balapan/hasil_balapan.php" class="nav-link">Hasil Balapan</a></li>
            <li><a href="../leaderboard/leaderboard.php" class="nav-link">Leaderboard</a></li>
            <li><a href="../pembayaran/pembayaran.php" class="nav-link">Pembayaran</a></li>
            <li><a href="../Profil/profil.php" class="nav-link">Profil Saya</a></li>
        </ul>
        <div class="sidebar-bottom">
            <div class="sidebar-user">
                <p><strong><?= htmlspecialchars($user_logged['nama_lengkap']); ?></strong></p>
                <small><?= htmlspecialchars($user_logged['email']); ?></small>
            </div>
            <button class="logout-btn" onclick="if(confirm('Yakin ingin keluar?')) location.href='../logout.php'">Logout</button>
        </div>
    </aside>

    <main class="main">
        <header class="page-header">
            <h1>Riwayat Booking Anda</h1>
            <p>Pantau status jadwal balapan dan tiket gokart Anda di sini</p>
        </header>

        <div class="table-responsive">
            <table class="riwayat-table">
                <thead>
                    <tr>
                        <th>No Invoice</th>
                        <th>Paket</th>
                        <th>Tanggal Main</th>
                        <th>Jam Sesi</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($riwayat_query) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($riwayat_query)): ?>
                            <tr>
                                <td><strong>#BK<?= str_pad($row['id'], 3, '0', STR_PAD_LEFT); ?></strong></td>
                                <td><?= htmlspecialchars($row['nama_paket']); ?></td>
                                <td><?= date('d M Y', strtotime($row['tanggal_booking'])); ?></td>
                                <td><?= date('H:i', strtotime($row['jam_booking'])); ?> WIB</td>
                                <td><strong>Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></strong></td>
                                <td>
                                    <span class="status-badge <?= strtolower($row['status']); ?>">
                                        <?= ucfirst($row['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="detail_booking.php?id=<?= $row['id']; ?>" class="btn-detail">Lihat Detail</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="empty-state">
                                <p>Anda belum pernah melakukan booking gokart.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>