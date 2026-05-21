<?php
session_start();

// Proteksi halaman admin
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require '../koneksi.php';

// Fitur Pencarian (Berdasarkan ID Booking atau Nama Lengkap)
$keyword = "";
$where_clause = "WHERE b.status = 'selesai'";

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $keyword = mysqli_real_escape_string($koneksi, trim($_GET['search']));
    $where_clause .= " AND (b.id LIKE '%$keyword%' OR u.nama_lengkap LIKE '%$keyword%')";
}

// 1. Hitung ulang total pendapatan bersih khusus dari data yang statusnya 'selesai'
$calc_query = mysqli_query($koneksi, "
    SELECT SUM(b.total_harga) as total_masuk 
    FROM booking b
    INNER JOIN users u ON b.user_id = u.id
    $where_clause
");
$income_data = mysqli_fetch_assoc($calc_query);
$total_pendapatan_bersih = $income_data['total_masuk'] ?? 0;

// 2. Ambil data semua transaksi yang sudah SELESAI (Lunas) sesuai kolom database racinghub
$riwayat_query = mysqli_query($koneksi, "
    SELECT b.*, u.nama_lengkap, u.email, p.nama_paket 
    FROM booking b
    INNER JOIN users u ON b.user_id = u.id
    INNER JOIN paket_bermain p ON b.paket_id = p.id
    $where_clause
    ORDER BY b.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoKart Admin - Riwayat Keuangan</title>
    <link rel="stylesheet" href="style-admin.css">
    <style>
        .search-box {
            display: flex;
            gap: 10px;
            margin-bottom: 1.5rem;
            max-width: 500px;
        }
        .search-input {
            flex: 1;
            padding: 0.6rem 1rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            outline: none;
            font-size: 0.95rem;
        }
        .search-input:focus { border-color: var(--primary); }
        .btn-search {
            padding: 0.6rem 1.2rem;
            background-color: var(--dark);
            color: var(--white);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
        }
        .btn-search:hover { background-color: #112237; }
        .btn-reset {
            padding: 0.6rem 1.2rem;
            background-color: #f1f5f9;
            color: var(--gray);
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.95rem;
            display: inline-block;
            border: 1px solid #e2e8f0;
        }
        .revenue-banner {
            background: linear-gradient(135deg, var(--dark) 0%, #2b4c7e 100%);
            color: var(--white);
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow);
        }
        .revenue-banner h2 { font-size: 2rem; color: #ffd700; margin-top: 0.2rem; }
        .success-badge {
            background-color: #d1fae5;
            color: #065f46;
            padding: 0.2rem 0.6rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 700;
        }
    </style>
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
                    <li><a href="dashboard.php" class="nav-link"><span>Dashboard</span></a></li>
                    <li><a href="riwayat_keuangan.php" class="nav-link active"><span>Riwayat Keuangan</span></a></li>
                    <li><a href="input_waktu.php" class="nav-link"><span>Input Waktu Balap</span></a></li>
                    <li><a href="leaderboard.php" class="nav-link"><span>Lihat Leaderboard</span></a></li>
                    <li><a href="kelola_paket.php" class="nav-link"><span>Kelola Paket</span></a></li>
                    <li><a href="kelola_users.php" class="nav-link"><span>Kelola Users</span></a></li>
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
                <h1>Laporan Riwayat Keuangan</h1>
                <p>Rekapitulasi pembukuan seluruh transaksi masuk yang sah</p>
            </header>

            <div class="revenue-banner">
                <div>
                    <p style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.9;">Total Buku Kas Masuk (Lunas)</p>
                    <h2>Rp <?= number_format($total_pendapatan_bersih, 0, ',', '.'); ?></h2>
                </div>
                <span style="font-size: 2.5rem;">📊</span>
            </div>

            <form action="" method="GET" class="search-box">
                <input type="text" name="search" class="search-input" placeholder="Cari ID Booking atau nama pembalap..." value="<?= htmlspecialchars($keyword); ?>">
                <button type="submit" class="btn-search">Cari</button>
                <?php if(!empty($keyword)): ?>
                    <a href="riwayat_keuangan.php" class="btn-reset">Reset</a>
                <?php endif; ?>
            </form>

            <section class="card table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID Transaksi</th>
                            <th>Nama Racer</th>
                            <th>Paket Bermain</th>
                            <th>Tanggal & Sesi Jadwal</th>
                            <th>Status</th>
                            <th style="text-align: right;">Nominal Uang</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($riwayat_query) == 0): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--gray); padding: 3rem; font-style: italic;">
                                Tidak ditemukan data riwayat transaksi keuangan yang cocok.
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php while ($row = mysqli_fetch_assoc($riwayat_query)): ?>
                            <tr>
                                <td><strong>#BK-<?= $row['id']; ?></strong></td>
                                <td>
                                    <strong><?= htmlspecialchars($row['nama_lengkap']); ?></strong><br>
                                    <small style="color: #64748b;"><?= htmlspecialchars($row['email']); ?></small>
                                </td>
                                <td><span class="badge-paket" style="background-color: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 600;"><?= htmlspecialchars($row['nama_paket']); ?></span></td>
                                <td>
                                    <?= date('d M Y', strtotime($row['tanggal_booking'])); ?><br>
                                    <small style="color: #64748b;">Jam: <?= date('H:i', strtotime($row['jam_booking'])); ?> WIB</small>
                                </td>
                                <td><span class="success-badge">LUNAS</span></td>
                                <td style="text-align: right; font-weight: bold; color: #10b981; font-size: 1.05rem;">
                                    + Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>