<?php
session_start();

// Proteksi halaman admin
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require '../koneksi.php';

$pesan_aksi = "";

// PROSES INPUT WAKTU BALAP KETIKA FORM DISUBMIT
if (isset($_POST['simpan_waktu'])) {
    $booking_id = intval($_POST['booking_id']);
    $user_id = intval($_POST['user_id']);
    
    // Ambil input waktu (format desimal 3 angka di belakang koma, misal: 24.125)
    $sektor_1 = floatval($_POST['sektor_1']);
    $sektor_2 = floatval($_POST['sektor_2']);
    $sektor_3 = floatval($_POST['sektor_3']);
    
    // Menghitung total lap secara otomatis berdasarkan penjumlahan 3 sektor
    $total_lap = $sektor_1 + $sektor_2 + $sektor_3;
    
    // Cek apakah data hasil balapan untuk booking_id ini sudah pernah diinput sebelumnya
    $cek_hasil = mysqli_query($koneksi, "SELECT id FROM hasil_balapan WHERE booking_id = '$booking_id'");
    
    if (mysqli_num_rows($cek_hasil) > 0) {
        // Jika sudah ada, lakukan UPDATE
        $query_save = "UPDATE hasil_balapan SET 
                       sektor_1 = '$sektor_1', 
                       sektor_2 = '$sektor_2', 
                       sektor_3 = '$sektor_3', 
                       total_lap = '$total_lap' 
                       WHERE booking_id = '$booking_id'";
    } else {
        // Jika belum ada, lakukan INSERT baru
        $query_save = "INSERT INTO hasil_balapan (booking_id, user_id, sektor_1, sektor_2, sektor_3, total_lap) 
                       VALUES ('$booking_id', '$user_id', '$sektor_1', '$sektor_2', '$sektor_3', '$total_lap')";
    }
    
    if (mysqli_query($koneksi, $query_save)) {
        // SINKRONISASI BEST TIME: Cek apakah lap time baru ini lebih cepat dari best_time lama di tabel users
        $user_data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT best_time FROM users WHERE id = '$user_id'"));
        $best_time_lama = floatval($user_data['best_time']);
        
        // Jika best_time lama masih default (99.999) atau total_lap baru ini lebih kecil (lebih cepat)
        if ($best_time_lama == 99.999 || $total_lap < $best_time_lama) {
            mysqli_query($koneksi, "UPDATE users SET best_time = '$total_lap' WHERE id = '$user_id'");
        }
        
        $pesan_aksi = "<div class='alert alert-success'>Catatan waktu berhasil disimpan! Total Lap: <strong>" . number_format($total_lap, 3) . " detik</strong>.</div>";
    } else {
        $pesan_aksi = "<div class='alert alert-danger'>Gagal menyimpan data waktu: " . mysqli_error($koneksi) . "</div>";
    }
}

// AMBIL DATA KANDIDAT BALAPAN (Hanya booking yang statusnya 'selesai')
$balapan_query = mysqli_query($koneksi, "
    SELECT b.id AS booking_id, b.tanggal_booking, b.jam_booking, u.id AS user_id, u.nama_lengkap, p.nama_paket, h.sektor_1, h.sektor_2, h.sektor_3, h.total_lap
    FROM booking b
    INNER JOIN users u ON b.user_id = u.id
    INNER JOIN paket_bermain p ON b.paket_id = p.id
    LEFT JOIN hasil_balapan h ON b.id = h.booking_id
    WHERE b.status = 'selesai'
    ORDER BY b.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoKart Admin - Input Waktu</title>
    <link rel="stylesheet" href="style-admin.css">
    <style>
        .form-waktu { display: flex; gap: 8px; align-items: center; }
        .input-waktu { width: 80px; padding: 0.4rem; border: 1px solid #ddd; border-radius: 6px; text-align: center; font-size: 0.9rem; }
        .btn-simpan { padding: 0.4rem 1rem; background-color: #1d3557; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.85rem; }
        .btn-simpan:hover { background-color: #457b9d; }
        .text-total { font-weight: bold; color: #e63946; font-size: 1rem; }
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
                    <li><a href="riwayat_keuangan.php" class="nav-link"><span>Riwayat Keuangan</span></a></li>
                    <li><a href="input_waktu.php" class="nav-link active"><span>Input Waktu Balap</span></a></li>
                    <li><a href="leaderboard.php" class="nav-link"><span>Lihat Leaderboard</span></a></li>
                    <li><a href="kelola_paket.php" class="nav-link"><span>Kelola Paket</span></a></li>
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
                <h1>Race Control - Input Timing Sensor</h1>
                <p>Masukkan data catatan waktu transponder pembalap per sektor (dalam satuan detik, contoh: 12.345)</p>
            </header>

            <?= $pesan_aksi; ?>

            <section class="card table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID Transaksi</th>
                            <th>Nama Pembalap</th>
                            <th>Sesi Jadwal</th>
                            <th style="text-align: center;">Input Catatan Waktu Sektor (Detik)</th>
                            <th style="text-align: center;">Best Lap Terhitung</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($balapan_query) == 0): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--gray); padding: 3rem; font-style: italic;">
                                Belum ada pembalap yang status transaksinya lunas hari ini.
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php while ($row = mysqli_fetch_assoc($balapan_query)): ?>
                            <tr>
                                <td><strong>#BK-<?= $row['booking_id']; ?></strong></td>
                                <td>
                                    <strong><?= htmlspecialchars($row['nama_lengkap']); ?></strong><br>
                                    <small style="color: var(--gray);"><?= htmlspecialchars($row['nama_paket']); ?></small>
                                </td>
                                <td>
                                    <?= date('d M Y', strtotime($row['tanggal_booking'])); ?><br>
                                    <small>Pukul <?= date('H:i', strtotime($row['jam_booking'])); ?> WIB</small>
                                </td>
                                <td style="text-align: center;">
                                    <form action="" method="POST" class="form-waktu">
                                        <input type="hidden" name="booking_id" value="<?= $row['booking_id']; ?>">
                                        <input type="hidden" name="user_id" value="<?= $row['user_id']; ?>">
                                        
                                        <input type="number" name="sektor_1" step="0.001" min="0" placeholder="Sektor 1" class="input-waktu" value="<?= $row['sektor_1'] != 99.999 ? $row['sektor_1'] : ''; ?>" required>
                                        <input type="number" name="sektor_2" step="0.001" min="0" placeholder="Sektor 2" class="input-waktu" value="<?= $row['sektor_2'] != 99.999 ? $row['sektor_2'] : ''; ?>" required>
                                        <input type="number" name="sektor_3" step="0.001" min="0" placeholder="Sektor 3" class="input-waktu" value="<?= $row['sektor_3'] != 99.999 ? $row['sektor_3'] : ''; ?>" required>
                                        
                                        <button type="submit" name="simpan_waktu" class="btn-simpan">Update</button>
                                    </form>
                                </td>
                                <td style="text-align: center;">
                                    <span class="text-total">
                                        <?= $row['total_lap'] != 99.999 ? number_format($row['total_lap'], 3) . " s" : "Belum Balap"; ?>
                                    </span>
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