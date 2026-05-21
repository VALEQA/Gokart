<?php
session_start();

// Proteksi halaman admin
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require '../koneksi.php';

// Navigasi Sektor
$view = isset($_GET['view']) ? $_GET['view'] : 'total_lap';
$allowed_views = ['total_lap', 'sektor_1', 'sektor_2', 'sektor_3'];
if (!in_array($view, $allowed_views)) { $view = 'total_lap'; }

$sector_titles = [
    'total_lap' => 'Overall Lap',
    'sektor_1'  => 'Sektor 1',
    'sektor_2'  => 'Sektor 2',
    'sektor_3'  => 'Sektor 3'
];

// Query data catatan waktu terbaik
$query_text = "SELECT u.nama_lengkap, u.email, MIN(h.$view) as waktu_terbaik 
               FROM hasil_balapan h
               INNER JOIN users u ON h.user_id = u.id
               WHERE h.$view < 99.999
               GROUP BY h.user_id
               ORDER BY waktu_terbaik ASC";
$leaderboard_query = mysqli_query($koneksi, $query_text);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoKart Admin - Leaderboard</title>
    <link rel="stylesheet" href="style-admin.css">
    <style>
        .leaderboard-tabs { display: flex; gap: 10px; margin-bottom: 1.5rem; flex-wrap: wrap; }
        .tab-btn { padding: 0.6rem 1.5rem; border-radius: 20px; text-decoration: none; font-weight: 600; font-size: 0.9rem; background-color: var(--white); color: var(--gray); border: 1px solid #e2e8f0; transition: var(--transition); }
        .tab-btn:hover { background-color: #f1f5f9; color: var(--dark); }
        .tab-btn.active { background-color: var(--primary); color: var(--white); border-color: var(--primary); }
        .rank-badge { display: inline-flex; align-items: center; justify-content: center; width: 35px; height: 35px; border-radius: 50%; font-weight: 800; font-size: 0.95rem; }
        .rank-1 { background-color: #ffd700; color: #6e5500; }
        .rank-2 { background-color: #e2e8f0; color: #4a5568; }
        .rank-3 { background-color: #edd1b6; color: #7c4a1b; }
        .rank-badge:not(.rank-1):not(.rank-2):not(.rank-3) { background-color: var(--bg-body); color: var(--gray); }
        .time-highlight { font-weight: 800; font-size: 1.2rem; color: var(--primary); }
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
                    <li><a href="leaderboard.php" class="nav-link active"><span>Lihat Leaderboard</span></a></li>
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
                <h1>Sirkuit Leaderboard (Admin View)</h1>
                <p>Pantauan rekapan waktu terbaik seluruh pembalap</p>
            </header>

            <div class="leaderboard-tabs">
                <?php foreach ($sector_titles as $key => $title): ?>
                    <a href="leaderboard.php?view=<?= $key; ?>" class="tab-btn <?= $view === $key ? 'active' : ''; ?>">
                        <?= $title; ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <section class="card table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 100px; text-align: center;">Posisi</th>
                            <th>Nama Pembalap</th>
                            <th>Email</th>
                            <th style="text-align: right;">Waktu Terbaik (<?= $sector_titles[$view]; ?>)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while($row = mysqli_fetch_assoc($leaderboard_query)): 
                        ?>
                        <tr>
                            <td style="text-align: center;">
                                <span class="rank-badge rank-<?= $no; ?>"><?= $no; ?></span>
                            </td>
                            <td><strong><?= htmlspecialchars($row['nama_lengkap']); ?></strong></td>
                            <td><?= htmlspecialchars($row['email']); ?></td>
                            <td class="time-highlight" style="text-align: right;">
                                <?= number_format($row['waktu_terbaik'], 3); ?> <span style="font-size: 0.8rem; font-weight: normal; color: var(--gray);">detik</span>
                            </td>
                        </tr>
                        <?php 
                        $no++;
                        endwhile; 
                        ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>