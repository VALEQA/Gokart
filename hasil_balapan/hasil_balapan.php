<?php
session_start(); // Membaca session login user

// Proteksi halaman: Tendang kembali ke login jika belum diautentikasi
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

require '../koneksi.php'; // Hubungkan ke database menggunakan variabel $koneksi

// Ambil ID User aktif dari session login
$user_id = $_SESSION['id_user']; 

// Mengambil profil user yang sedang login untuk info sidebar
$user_query = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$user_id'");
$user_logged = mysqli_fetch_assoc($user_query);

// QUERY UTAMA HASIL BALAPAN: Mengambil semua riwayat balap user yang bersangkutan
$hasil_query = mysqli_query($koneksi, "
    SELECT h.*, b.tanggal_booking, b.jam_booking, p.nama_paket 
    FROM hasil_balapan h
    INNER JOIN booking b ON h.booking_id = b.id
    INNER JOIN paket_bermain p ON b.paket_id = p.id
    WHERE h.user_id = '$user_id'
    ORDER BY b.tanggal_booking DESC, b.jam_booking DESC
");

// Siapkan data untuk Grafik Analisis Performa (Mengambil 5 balapan terbaru)
$chart_query = mysqli_query($koneksi, "
    SELECT h.total_lap, b.tanggal_booking 
    FROM hasil_balapan h
    INNER JOIN booking b ON h.booking_id = b.id
    WHERE h.user_id = '$user_id'
    ORDER BY b.tanggal_booking DESC, b.jam_booking DESC
    LIMIT 5
");

$chart_labels = [];
$chart_data = [];
while ($c_row = mysqli_fetch_assoc($chart_query)) {
    $chart_labels[] = date('d M', strtotime($c_row['tanggal_booking']));
    $chart_data[] = (float)$c_row['total_lap'];
}

// Balikkan urutan data agar grafik bergerak dari kiri (lama) ke kanan (baru)
$chart_labels = array_reverse($chart_labels);
$chart_data = array_reverse($chart_data);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoKart Racing - Hasil Balapan</title>
    <link rel="stylesheet" href="style.css"> 
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    <li><a href="../hasil_balapan/hasil_balapan.php" class="nav-link active"><span>Hasil Balapan</span></a></li>
                    <li><a href="../leaderboard/leaderboard.php" class="nav-link"><span>Leaderboard</span></a></li>
                    <li><a href="../pembayaran/pembayaran.php" class="nav-link"><span>Pembayaran</span></a></li>
                    <li><a href="../Profil_sayaGokart/profil_saya.php" class="nav-link"><span>Profil Saya</span></a></li>
                </ul>
            </div>

            <div class="sidebar-bottom">
                <div class="sidebar-user">
                    <p><strong><?= htmlspecialchars($user_logged['nama_lengkap']); ?></strong></p>
                    <small><?= htmlspecialchars($user_logged['email']); ?></small>
                </div>
                <button class="logout-btn">Logout</button>
            </div>
        </aside>

        <main class="main">
            <header class="header">
                <h1>Hasil Balapan Saya</h1>
                <p>Analisis catatan waktu dan track record performa balap Anda</p>
            </header>

            <?php if (!empty($chart_data)): ?>
            <section class="card chart-container" style="margin-bottom: 2rem;">
                <h3>Tren Waktu Lap Tercepat (5 Balapan Terakhir)</h3>
                <div style="height: 250px; position: relative; margin-top: 15px;">
                    <canvas id="performanceChart"></canvas>
                </div>
            </section>
            <?php endif; ?>

            <section class="card table-container">
                <h3>Rincian Waktu Per Sektor</h3>
                <table class="racing-table">
                    <thead>
                        <tr>
                            <th>Tanggal & Sesi</th>
                            <th>Paket</th>
                            <th style="text-align: center;">Sektor 1</th>
                            <th style="text-align: center;">Sektor 2</th>
                            <th style="text-align: center;">Sektor 3</th>
                            <th style="text-align: right;">Total Lap</th>
                            <th style="text-align: center;">Posisi Finish</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        while($row = mysqli_fetch_assoc($hasil_query)): 
                        ?>
                        <tr>
                            <td>
                                <strong><?= date('d M Y', strtotime($row['tanggal_booking'])); ?></strong><br>
                                <small style="color: var(--gray);">⏰ Sesi: <?= date('H:i', strtotime($row['jam_booking'])); ?></small>
                            </td>
                            <td><span class="badge-paket"><?= htmlspecialchars($row['nama_paket']); ?></span></td>
                            <td style="text-align: center;" class="sector-time"><?= number_format($row['sektor_1'], 3); ?>s</td>
                            <td style="text-align: center;" class="sector-time"><?= number_format($row['sektor_2'], 3); ?>s</td>
                            <td style="text-align: center;" class="sector-time"><?= number_format($row['sektor_3'], 3); ?>s</td>
                            <td style="text-align: right;" class="total-time-highlight">
                                <?= number_format($row['total_lap'], 3); ?> <span>detik</span>
                            </td>
                            <td style="text-align: center;">
                                <span class="finish-badge P-<?= $row['posisi_finish']; ?>">P<?= $row['posisi_finish']; ?></span>
                            </td>
                        </tr>
                        <?php 
                        endwhile; 
                        
                        if (mysqli_num_rows($hasil_query) == 0):
                        ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--gray); padding: 3rem; font-style: italic;">
                                Anda belum memiliki catatan hasil balapan resmi. Selesaikan booking Anda untuk mencetak rekor!
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

    <script src="script.js"></script>
    <script>
        const ctx = document.getElementById('performanceChart');
        if(ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?= json_encode($chart_labels); ?>,
                    datasets: [{
                        label: 'Waktu Total Lap (Detik)',
                        data: <?= json_encode($chart_data); ?>,
                        borderColor: '#e63946',
                        backgroundColor: 'rgba(230, 57, 70, 0.08)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.3,
                        pointBackgroundColor: '#1d3557',
                        pointHoverBackgroundColor: '#e63946',
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'top' }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            title: { display: true, text: 'Durasi (Detik)', font: { weight: 'bold' } }
                        },
                        x: {
                            title: { display: true, text: 'Tanggal Balapan', font: { weight: 'bold' } }
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>