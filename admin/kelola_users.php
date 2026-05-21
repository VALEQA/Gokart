<?php
session_start();

// Proteksi halaman admin
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require '../koneksi.php';
$pesan_aksi = "";

// PROSES HAPUS USER (DELETE)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id_hapus = intval($_GET['id']);
    
    // Cegah admin menghapus dirinya sendiri yang sedang login
    if ($id_hapus == $_SESSION['id_user_kamu_jika_ada'] || $id_hapus == 1) { 
        $pesan_aksi = "<div class='alert alert-danger'>Gagal! Kamu tidak bisa menghapus akun Utama Admin ini.</div>";
    } else {
        $query_hapus = "DELETE FROM users WHERE id = '$id_hapus'";
        if (mysqli_query($koneksi, $query_hapus)) {
            $pesan_aksi = "<div class='alert alert-danger'>User berhasil didelete dari sistem.</div>";
        }
        header("Refresh: 1.5; URL=kelola_users.php");
    }
}

// AMBIL SEMUA DATA USER
$users_query = mysqli_query($koneksi, "SELECT * FROM users ORDER BY role ASC, nama_lengkap ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoKart Admin - Kelola Users</title>
    <link rel="stylesheet" href="style-admin.css">
    <style>
        .badge-role { padding: 3px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold; }
        .role-admin { background-color: #fecdd3; color: #9f1239; }
        .role-user { background-color: #e0f2fe; color: #0369a1; }
        .btn-edit { background-color: #f59e0b; color: white; padding: 4px 10px; border-radius: 4px; text-decoration: none; font-size: 0.85rem; font-weight: 600; }
        .btn-delete { background-color: #ef4444; color: white; padding: 4px 10px; border-radius: 4px; text-decoration: none; font-size: 0.85rem; font-weight: 600; margin-left: 5px; }
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
                    <li><a href="kelola_paket.php" class="nav-link"><span>Kelola Paket</span></a></li>
                    <li><a href="kelola_users.php" class="nav-link active"><span>Kelola Users</span></a></li>
                </ul>
            </div>
            <div class="sidebar-bottom">
                <div class="sidebar-user">
                    <p><strong>Admin Panel</strong></p>
                    <small>Race Director</small>
                </div>
                <button class="logout-btn" onclick="if(confirm('Keluar?')) window.location.href='../logout.php'"><span>Logout</span></button>
            </div>
        </aside>

        <main class="main">
            <header class="header">
                <h1>Data Manajemen User</h1>
                <p>Melihat dan memperbarui profil biodata pendaftar sirkuit</p>
            </header>

            <?= $pesan_aksi; ?>

            <section class="card table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Lengkap</th>
                            <th>Kontak Email / HP</th>
                            <th style="text-align: center;">Role Level</th>
                            <th style="text-align: center;">Total Main</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($users_query)): ?>
                        <tr>
                            <td>#<?= $row['id']; ?></td>
                            <td><strong><?= htmlspecialchars($row['nama_lengkap']); ?></strong></td>
                            <td>
                                ✉️ <?= htmlspecialchars($row['email']); ?><br>
                                📞 <?= htmlspecialchars($row['nomor_hp']); ?>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge-role <?= $row['role'] === 'admin' ? 'role-admin' : 'role-user'; ?>">
                                    <?= strtoupper($row['role']); ?>
                                </span>
                            </td>
                            <td style="text-align: center; font-weight: bold;"><?= $row['total_bermain']; ?> Kali</td>
                            <td style="text-align: center;">
                                <a href="edit_user.php?id=<?= $row['id']; ?>" class="btn-edit">Edit Profil</a>
                                <a href="kelola_users.php?action=delete&id=<?= $row['id']; ?>" class="btn-delete" onclick="return confirm('Hapus user <?= $row['nama_lengkap']; ?>?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>