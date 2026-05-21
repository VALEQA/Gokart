<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require '../koneksi.php';
$pesan_aksi = "";

if (!isset($_GET['id']) || empty(trim($_GET['id']))) {
    header("Location: kelola_users.php");
    exit;
}

$id_user = intval($_GET['id']);

// PROSES UPDATE DATA PROFIL USER
if (isset($_POST['update_profile'])) {
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $nomor_hp = mysqli_real_escape_string($koneksi, $_POST['nomor_hp']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $role = $_POST['role'];
    $total_bermain = intval($_POST['total_bermain']);

    $query_update = "UPDATE users SET 
                        nama_lengkap = '$nama_lengkap', 
                        nomor_hp = '$nomor_hp', 
                        email = '$email', 
                        role = '$role',
                        total_bermain = '$total_bermain'
                     WHERE id = '$id_user'";
    
    if (mysqli_query($koneksi, $query_update)) {
        $pesan_aksi = "<div class='alert alert-success'>Profil user berhasil diperbarui dari pusat! Mengalihkan...</div>";
        header("Refresh: 1.5; URL=kelola_users.php");
    } else {
        $pesan_aksi = "<div class='alert alert-danger'>Gagal update: " . mysqli_error($koneksi) . "</div>";
    }
}

// AMBIL DATA LAMA USER
$user_data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$id_user'"));
if (!$user_data) {
    header("Location: kelola_users.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoKart Admin - Edit Profil User</title>
    <link rel="stylesheet" href="style-admin.css">
    <style>
        .edit-container { max-width: 600px; margin: 2rem auto 0 auto; }
        .form-group { margin-bottom: 1.2rem; }
        .form-group label { display: block; margin-bottom: 0.4rem; font-weight: 600; font-size: 0.95rem; }
        .form-control { width: 100%; padding: 0.7rem; border: 1px solid #ddd; border-radius: 6px; outline: none; box-sizing: border-box; font-size: 0.95rem; }
        .action-flex { display: flex; gap: 10px; margin-top: 1.5rem; }
        .btn-update { flex: 2; padding: 0.8rem; background-color: #1d3557; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 1rem; }
        .btn-update:hover { background-color: #457b9d; }
        .btn-kembali { flex: 1; padding: 0.8rem; background-color: #64748b; color: white; text-align: center; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 1rem; }
    </style>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-top">
                <div class="brand"><h2>GoKart Admin</h2></div>
                <ul class="nav-menu">
                    <li><a href="kelola_users.php" class="nav-link active"><span>Kembali ke Kelola User</span></a></li>
                </ul>
            </div>
        </aside>

        <main class="main">
            <header class="header">
                <h1>Edit Profil Pembalap</h1>
                <p>Mengubah data kredensial akun User ID #<?= $user_data['id']; ?> secara manual</p>
            </header>

            <div class="edit-container">
                <?= $pesan_aksi; ?>
                <div class="card">
                    <form action="" method="POST">
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($user_data['nama_lengkap']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Nomor Handphone</label>
                            <input type="text" name="nomor_hp" class="form-control" value="<?= htmlspecialchars($user_data['nomor_hp']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Alamat Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user_data['email']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Hak Akses / Role Level</label>
                            <select name="role" class="form-control">
                                <option value="user" <?= $user_data['role'] === 'user' ? 'selected' : ''; ?>>User (Pembalap)</option>
                                <option value="admin" <?= $user_data['role'] === 'admin' ? 'selected' : ''; ?>>Admin (Race Director)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Total Bermain (Counter Manual)</label>
                            <input type="number" name="total_bermain" class="form-control" value="<?= $user_data['total_bermain']; ?>" min="0" required>
                        </div>
                        
                        <div class="action-flex">
                            <a href="kelola_users.php" class="btn-kembali">Batal</a>
                            <button type="submit" name="update_profile" class="btn-update">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>