<?php
session_start(); // Membaca session login user

// Proteksi halaman: Tendang kembali ke login jika belum diautentikasi
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

require '../koneksi.php'; // Hubungkan ke database menggunakan variabel $koneksi kamu

// Ambil ID User aktif dari session login
$user_id = $_SESSION['id_user']; 

// Mengambil profil user yang sedang login untuk info sidebar
$user_query = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$user_id'");
$user_logged = mysqli_fetch_assoc($user_query);

// Fitur Navigasi Sektor: Menangkap parameter 'view' dari URL (GET)
$view = isset($_GET['view']) ? $_GET['view'] : 'total_lap';

// Validasi kolom agar aman dari SQL Injection
$allowed_views = ['total_lap', 'sektor_1', 'sektor_2', 'sektor_3'];
if (!in_array($view, $allowed_views)) {
    $view = 'total_lap';
}

// Judul kolom dinamis berdasarkan sektor yang dipilih
$sector_titles = [
    'total_lap' => 'Overall Lap',
    'sektor_1'  => 'Sektor 1',
    'sektor_2'  => 'Sektor 2',
    'sektor_3'  => 'Sektor 3'
];

// QUERY UTAMA LEADERBOARD: Ambil waktu TERKECIL (MIN) tiap user dari tabel hasil_balapan
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
    <title>GoKart Racing - Leaderboard</title>
    <link rel="stylesheet" href="style.css">
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
                    <li><a href="../dashboard/dashboard.php" class="nav-link"><span>Dashboard</span></a></li>
                    <li><a href="../booking/booking.php" class="nav-link"><span>Booking</span></a></li>
                    <li><a href="../riwayat booking/riwayat.php" class="nav-link"><span>Riwayat Booking</span></a></li>
                    <li><a href="../hasil_balapan/hasil_balapan.php" class="nav-link"><span>Hasil Balapan</span></a></li>
                    <li><a href="../leaderboard/leaderboard.php" class="nav-link active"><span>Leaderboard</span></a></li>
                    <li><a href="../pembayaran/pembayaran.php" class="nav-link"><span>Pembayaran</span></a></li>
                    <li><a href="../Profil_sayaGokart/profil_saya.php" class="nav-link"><span>Profil Saya</span></a></li>
                </ul>
            </div>

            <div class="sidebar-bottom">
                <div class="sidebar-user">
                    <p><strong><?= htmlspecialchars($user_logged['nama_lengkap']); ?></strong></p>
                    <small><?= htmlspecialchars($user_logged['email']); ?></small>
                </div>
                <button class="logout-btn" onclick="if(confirm('Apakah Anda yakin ingin keluar dari sistem GoKart Racing?')) window.location.href='../logout.php'">
                    <span>Logout</span>
                </button>
            </div>
        </aside>

        <main class="main">
            <header class="header">
                <h1>Top Racers Leaderboard</h1>
                <p>Daftar catatan waktu terbaik pembalap GoKart</p>
            </header>

            <div class="leaderboard-tabs">
                <?php foreach ($sector_titles as $key => $title): ?>
                    <a href="leaderboard.php?view=<?= $key; ?>" class="tab-btn <?= $view === $key ? 'active' : ''; ?>">
                        <?= $title; ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <section class="card table-container">
                <table class="leaderboard-table">
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
                        <tr class="<?= ($no <= 3) ? 'podium-row' : ''; ?>">
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
                        
                        // Kondisi jika data balapan di database kosong
                        if (mysqli_num_rows($leaderboard_query) == 0):
                        ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--gray); padding: 3rem; font-style: italic;">
                                Belum ada catatan waktu resmi untuk kategori <?= $sector_titles[$view]; ?>.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
    
    <script src="script.js"></script>
</body>
</html>