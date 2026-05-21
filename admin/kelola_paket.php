<?php
session_start();

// Proteksi halaman admin
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require '../koneksi.php';

$pesan_aksi = "";

// 1. PROSES TAMBAH PAKET BARU (CREATE)
if (isset($_POST['tambah_paket'])) {
    $nama_paket = mysqli_real_escape_string($koneksi, $_POST['nama_paket']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $durasi_menit = intval($_POST['durasi_menit']);
    $maksimal_orang = intval($_POST['maksimal_orang']);
    $harga = intval($_POST['harga']);

    $query_tambah = "INSERT INTO paket_bermain (nama_paket, deskripsi, durasi_menit, maksimal_orang, harga) 
                     VALUES ('$nama_paket', '$deskripsi', '$durasi_menit', '$maksimal_orang', '$harga')";
    
    if (mysqli_query($koneksi, $query_tambah)) {
        $pesan_aksi = "<div class='alert alert-success'>Paket baru <strong>$nama_paket</strong> berhasil ditambahkan!</div>";
    } else {
        $pesan_aksi = "<div class='alert alert-danger'>Gagal menambah paket: " . mysqli_error($koneksi) . "</div>";
    }
}

// 2. PROSES HAPUS PAKET (DELETE)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id_hapus = intval($_GET['id']);
    
    $query_hapus = "DELETE FROM paket_bermain WHERE id = '$id_hapus'";
    if (mysqli_query($koneksi, $query_hapus)) {
        $pesan_aksi = "<div class='alert alert-danger'>Paket berhasil dihapus dari sistem.</div>";
    } else {
        $pesan_aksi = "<div class='alert alert-danger'>Gagal menghapus paket (Mungkin data ini masih terikat dengan transaksi booking).</div>";
    }
    header("Refresh: 1.5; URL=kelola_paket.php");
}

// 3. AMBIL DATA SELURUH PAKET (READ)
$paket_query = mysqli_query($koneksi, "SELECT * FROM paket_bermain ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoKart Admin - Kelola Paket</title>
    <link rel="stylesheet" href="style-admin.css">
    <style>
        .crud-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; margin-top: 1.5rem; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.4rem; font-weight: 600; font-size: 0.9rem; }
        .form-control { width: 100%; padding: 0.6rem; border: 1px solid #ddd; border-radius: 6px; outline: none; box-sizing: border-box; }
        .form-control:focus { border-color: #1d3557; }
        .btn-submit { width: 100%; padding: 0.7rem; background-color: #10b981; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 0.95rem; }
        .btn-submit:hover { background-color: #059669; }
        .btn-edit { background-color: #f59e0b; color: white; padding: 4px 10px; border-radius: 4px; text-decoration: none; font-size: 0.85rem; font-weight: 600; }
        .btn-delete { background-color: #ef4444; color: white; padding: 4px 10px; border-radius: 4px; text-decoration: none; font-size: 0.85rem; font-weight: 600; margin-left: 5px; }
        .btn-edit:hover { background-color: #d97706; }
        .btn-delete:hover { background-color: #dc2626; }
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
                <button class="logout-btn" onclick="if(confirm('Keluar dari panel admin?')) window.location.href='../logout.php'">
                    <span>Logout</span>
                </button>
            </div>
        </aside>

        <main class="main">
            <header class="header">
                <h1>Manajemen Paket Bermain</h1>
                <p>Tambah, edit, atau hapus opsi variasi paket rental gokart yang muncul di halaman user</p>
            </header>

            <?= $pesan_aksi; ?>

            <div class="crud-grid">
                <div class="card">
                    <h3 style="margin-bottom: 1.2rem; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px;">➕ Tambah Paket</h3>
                    <form action="" method="POST">
                        <div class="form-group">
                            <label>Nama Paket</label>
                            <input type="text" name="nama_paket" class="form-control" placeholder="Contoh: Super Max Speed" required>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi Singkat</label>
                            <input type="text" name="deskripsi" class="form-control" placeholder="Contoh: Sesi khusus pro racer" required>
                        </div>
                        <div class="form-group">
                            <label>Durasi (Menit)</label>
                            <input type="number" name="durasi_menit" class="form-control" placeholder="Contoh: 15" min="1" required>
                        </div>
                        <div class="form-group">
                            <label>Maksimal Slot Orang</label>
                            <input type="number" name="maksimal_orang" class="form-control" placeholder="Contoh: 1" min="1" required>
                        </div>
                        <div class="form-group">
                            <label>Harga Sewa (Rp)</label>
                            <input type="number" name="harga" class="form-control" placeholder="Contoh: 200000" min="0" required>
                        </div>
                        <button type="submit" name="tambah_paket" class="btn-submit">Simpan Paket</button>
                    </form>
                </div>

                <div class="card table-container">
                    <h3 style="margin-bottom: 1.2rem; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px;">📋 Opsi Paket Aktif</h3>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Paket</th>
                                <th>Durasi</th>
                                <th>Kapasitas</th>
                                <th style="text-align: right;">Harga</th>
                                <th style="text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($paket_query)): ?>
                            <tr>
                                <td>#<?= $row['id']; ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($row['nama_paket']); ?></strong><br>
                                    <small style="color: #64748b;"><?= htmlspecialchars($row['deskripsi']); ?></small>
                                </td>
                                <td><?= $row['durasi_menit']; ?> Menit</td>
                                <td><?= $row['maksimal_orang']; ?> Orang</td>
                                <td style="text-align: right; font-weight: bold;">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                                <td style="text-align: center;">
                                    <a href="edit_paket.php?id=<?= $row['id']; ?>" class="btn-edit">Edit</a>
                                    <a href="kelola_paket.php?action=delete&id=<?= $row['id']; ?>" class="btn-delete" onclick="return confirm('Hapus paket <?= $row['nama_paket']; ?> secara permanen?')">Hapus</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>