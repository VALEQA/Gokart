<?php
session_start();

// Proteksi halaman admin
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require '../koneksi.php';

$pesan_aksi = "";

// 1. PASTIKAN ADA ID YANG DIKIRIM DI URL
if (!isset($_GET['id']) || empty(trim($_GET['id']))) {
    header("Location: kelola_paket.php");
    exit;
}

$id_paket = intval($_GET['id']);

// 2. PROSES UPDATE DATA KETIKA FORM DISUBMIT
if (isset($_POST['update_paket'])) {
    $nama_paket = mysqli_real_escape_string($koneksi, $_POST['nama_paket']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $durasi_menit = intval($_POST['durasi_menit']);
    $maksimal_orang = intval($_POST['maksimal_orang']);
    $harga = intval($_POST['harga']);

    $query_update = "UPDATE paket_bermain SET 
                        nama_paket = '$nama_paket', 
                        deskripsi = '$deskripsi', 
                        durasi_menit = '$durasi_menit', 
                        maksimal_orang = '$maksimal_orang', 
                        harga = '$harga' 
                     WHERE id = '$id_paket'";
    
    if (mysqli_query($koneksi, $query_update)) {
        $pesan_aksi = "<div class='alert alert-success'>Data paket berhasil diperbarui! Mengalihkan...</div>";
        // Redirect kembali ke halaman utama CRUD setelah 1.5 detik
        header("Refresh: 1.5; URL=kelola_paket.php");
    } else {
        $pesan_aksi = "<div class='alert alert-danger'>Gagal memperbarui paket: " . mysqli_error($koneksi) . "</div>";
    }
}

// 3. AMBIL DATA LAMA UNTUK DITAMPILKAN DI FORM INPUT
$paket_data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM paket_bermain WHERE id = '$id_paket'"));

// Jika ID paket tidak ditemukan di database, tendang balik ke kelola_paket.php
if (!$paket_data) {
    header("Location: kelola_paket.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoKart Admin - Edit Paket</title>
    <link rel="stylesheet" href="style-admin.css">
    <style>
        .edit-container { max-width: 600px; margin: 2rem auto 0 auto; }
        .form-group { margin-bottom: 1.2rem; }
        .form-group label { display: block; margin-bottom: 0.4rem; font-weight: 600; font-size: 0.95rem; }
        .form-control { width: 100%; padding: 0.7rem; border: 1px solid #ddd; border-radius: 6px; outline: none; box-sizing: border-box; font-size: 0.95rem; }
        .form-control:focus { border-color: #1d3557; }
        .action-flex { display: flex; gap: 10px; margin-top: 1.5rem; }
        .btn-update { flex: 2; padding: 0.8rem; background-color: #f59e0b; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 1rem; }
        .btn-update:hover { background-color: #d97706; }
        .btn-kembali { flex: 1; padding: 0.8rem; background-color: #64748b; color: white; text-align: center; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 1rem; }
        .btn-kembali:hover { background-color: #475569; }
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
                <button class="logout-btn" onclick="window.location.href='../logout.php'">
                    <span>Logout</span>
                </button>
            </div>
        </aside>

        <main class="main">
            <header class="header">
                <h1>Ubah Data Paket</h1>
                <p>Modifikasi detail informasi tarif dan kapasitas durasi paket #<?= $paket_data['id']; ?></p>
            </header>

            <div class="edit-container">
                <?= $pesan_aksi; ?>

                <div class="card">
                    <h3 style="margin-bottom: 1.5rem; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px;">✏️ Form Edit Paket</h3>
                    <form action="" method="POST">
                        <div class="form-group">
                            <label>Nama Paket</label>
                            <input type="text" name="nama_paket" class="form-control" value="<?= htmlspecialchars($paket_data['nama_paket']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi Paket</label>
                            <input type="text" name="deskripsi" class="form-control" value="<?= htmlspecialchars($paket_data['deskripsi']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Durasi Bermain (Menit)</label>
                            <input type="number" name="durasi_menit" class="form-control" value="<?= $paket_data['durasi_menit']; ?>" min="1" required>
                        </div>
                        <div class="form-group">
                            <label>Maksimal Slot Orang</label>
                            <input type="number" name="maksimal_orang" class="form-control" value="<?= $paket_data['maksimal_orang']; ?>" min="1" required>
                        </div>
                        <div class="form-group">
                            <label>Harga Sewa Paket (Rp)</label>
                            <input type="number" name="harga" class="form-control" value="<?= $paket_data['harga']; ?>" min="0" required>
                        </div>
                        
                        <div class="action-flex">
                            <a href="kelola_paket.php" class="btn-kembali">Batal</a>
                            <button type="submit" name="update_paket" class="btn-update">Perbarui Paket</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>