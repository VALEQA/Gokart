<?php
session_start(); // Membaca session login user

// Proteksi halaman
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

require '../koneksi.php'; 

$user_id = $_SESSION['id_user']; 

// Ambil profil user untuk info sidebar
$user_query = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$user_id'");
$user_logged = mysqli_fetch_assoc($user_query);

// Proses submit form konfirmasi pembayaran
$pesan_sukses = "";
$pesan_error = "";

if (isset($_POST['submit_pembayaran'])) {
    $booking_id = $_POST['booking_id'];
    $bank_tujuan = mysqli_real_escape_string($koneksi, $_POST['bank_tujuan']);
    $nama_rekening = mysqli_real_escape_string($koneksi, $_POST['nama_rekening']);
    
    // Upload File Handler
    $nama_file = $_FILES['bukti_transfer']['name'];
    $ukuran_file = $_FILES['bukti_transfer']['size'];
    $error_file = $_FILES['bukti_transfer']['error'];
    $tmp_name = $_FILES['bukti_transfer']['tmp_name'];
    
    if ($error_file === 0) {
        $ekstensi_valid = ['jpg', 'jpeg', 'png'];
        $ekstensi_file = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
        
        if (in_array($ekstensi_file, $ekstensi_valid)) {
            if ($ukuran_file < 2000000) { 
                $nama_file_baru = uniqid() . '.' . $ekstensi_file;
                
                if (!is_dir('uploads')) {
                    mkdir('uploads', 0777, true);
                }
                
                if (move_uploaded_file($tmp_name, 'uploads/' . $nama_file_baru)) {
                    // Update status booking menjadi selesai karena sudah dibayar
                    $update_query = mysqli_query($koneksi, "UPDATE booking SET status = 'selesai' WHERE id = '$booking_id'");
                    
                    if ($update_query) {
                        $pesan_sukses = "Pembayaran sukses dikonfirmasi! Selamat balapan.";
                        header("Refresh: 2; URL=pembayaran.php");
                    } else {
                        $pesan_error = "Gagal memperbarui status transaksi di database.";
                    }
                }
            } else {
                $pesan_error = "Ukuran gambar terlalu besar! Maksimal 2MB.";
            }
        } else {
            $pesan_error = "Format file tidak valid! Gunakan PNG/JPG/JPEG.";
        }
    } else {
        $pesan_error = "Wajib mengunggah gambar bukti transfer.";
    }
}

// -------------------------------------------------------------------------
// FITUR PILIH TRANSAKSI:
// -------------------------------------------------------------------------
// Ambil daftar SEMUA booking aktif milik user yang belum dibayar
$all_bookings_query = mysqli_query($koneksi, "
    SELECT b.*, p.nama_paket 
    FROM booking b
    INNER JOIN paket_bermain p ON b.paket_id = p.id
    WHERE b.user_id = '$user_id' AND b.status = 'aktif'
    ORDER BY b.created_at DESC
");

// Cek apakah ada ID booking spesifik yang dipilih via URL (?id=...)
$selected_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$data_booking = null;

if ($selected_id > 0) {
    // Validasi apakah booking ini benar milik user yang login dan memang berstatus aktif
    $single_query = mysqli_query($koneksi, "
        SELECT b.*, p.nama_paket 
        FROM booking b
        INNER JOIN paket_bermain p ON b.paket_id = p.id
        WHERE b.id = '$selected_id' AND b.user_id = '$user_id' AND b.status = 'aktif'
    ");
    $data_booking = mysqli_fetch_assoc($single_query);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoKart Racing - Pembayaran</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Tambahan Style Khusus UI Komponen Pembayaran */
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        /* Tombol Bayar Universal */
        .pay-btn {
            display: block;
            width: 100%;
            padding: 0.8rem;
            background-color: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            transition: var(--transition);
        }
        .pay-btn:hover { background-color: #c92a36; opacity: 0.9; }

        /* Grid Layout Info Rekening & Form */
        .payment-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        @media (max-width: 992px) { .payment-grid { grid-template-columns: 1fr; } }

        /* Detail Slip Tagihan */
        .bill-row {
            display: flex;
            justify-content: space-between;
            padding: 0.6rem 0;
            font-size: 0.95rem;
        }
        .bill-row span { color: var(--gray); }
        .bill-total { margin-top: 1rem; text-align: right; }
        .bill-total h2 { color: var(--primary); font-size: 1.8rem; margin-top: 0.2rem; }
        hr { border: 0; border-top: 1px solid #eee; margin: 1rem 0; }

        /* Bank Item Style */
        .bank-item {
            padding: 1rem;
            background-color: var(--bg-body);
            border-radius: 8px;
            border-left: 4px solid var(--dark);
        }
        .account-number {
            font-family: monospace;
            font-size: 1.2rem;
            font-weight: bold;
            margin: 0.3rem 0;
            color: var(--dark);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .copy-badge {
            font-size: 0.75rem;
            background-color: var(--primary-light);
            color: var(--primary);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            cursor: pointer;
            font-family: sans-serif;
        }

        /* Form Controls */
        .form-group { margin-bottom: 1.2rem; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 0.4rem; font-size: 0.9rem; }
        .input-field {
            width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 8px; outline: none; font-size: 0.95rem;
        }
        .input-field:focus { border-color: var(--primary); }

        /* Area Drop Gambar Bukti Transfer */
        .upload-area {
            border: 2px dashed #ddd; padding: 2rem; border-radius: 8px; text-align: center; cursor: pointer; background: var(--bg-body); transition: var(--transition);
        }
        .upload-area:hover { border-color: var(--primary); background: var(--primary-light); }
        
        /* Tabel Tagihan Depan */
        .bill-list-table th { padding: 1rem 0.5rem; color: var(--gray); font-size: 0.8rem; text-transform: uppercase; border-bottom: 2px solid #eee; }
        .bill-list-table td { padding: 1.2rem 0.5rem; border-bottom: 1px solid #f1f1f1; }
    </style>
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
                    <li><a href="../leaderboard/leaderboard.php" class="nav-link"><span>Leaderboard</span></a></li>
                    <li><a href="pembayaran.php" class="nav-link active"><span>Pembayaran</span></a></li>
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
                <h1>Pusat Pembayaran</h1>
                <p>Pilih dan selesaikan tagihan sirkuit balap Anda</p>
            </header>

            <?php if(!empty($pesan_sukses)): ?>
                <div class="alert alert-success"><?= $pesan_sukses; ?></div>
            <?php endif; ?>
            <?php if(!empty($pesan_error)): ?>
                <div class="alert alert-danger"><?= $pesan_error; ?></div>
            <?php endif; ?>

            <?php if (mysqli_num_rows($all_bookings_query) > 0): ?>
                
                <?php if (!$data_booking): ?>
                    <section class="card">
                        <h3 style="border-left: 4px solid var(--primary); padding-left: 10px; margin-bottom: 0.5rem; color: var(--dark);">Daftar Tagihan Menunggu Pembayaran</h3>
                        <p style="color: var(--gray); font-size: 0.9rem; margin-bottom: 1.5rem;">Klik tombol "Bayar Sekarang" pada transaksi yang ingin Anda lunasi:</p>
                        
                        <div style="overflow-x: auto;">
                            <table class="bill-list-table" style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr>
                                        <th>ID Booking</th>
                                        <th>Paket</th>
                                        <th>Jadwal Sesi</th>
                                        <th>Total Tagihan</th>
                                        <th style="text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($bill = mysqli_fetch_assoc($all_bookings_query)): ?>
                                    <tr>
                                        <td><strong>#BK-<?= $bill['id']; ?></strong></td>
                                        <td><?= htmlspecialchars($bill['nama_paket']); ?></td>
                                        <td><?= date('d M Y', strtotime($bill['tanggal_booking'])); ?> (<?= date('H:i', strtotime($bill['jam_booking'])); ?>)</td>
                                        <td style="font-weight: bold; color: var(--primary);">Rp <?= number_format($bill['total_harga'], 0, ',', '.'); ?></td>
                                        <td style="text-align: center;">
                                            <a href="pembayaran.php?id=<?= $bill['id']; ?>" class="pay-btn" style="padding: 0.5rem 1rem; font-size: 0.85rem; text-decoration: none; display: inline-block; width: auto;">Bayar Sekarang</a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                
                <?php else: ?>
                    <div style="margin-bottom: 1.5rem;">
                        <a href="pembayaran.php" style="color: var(--primary); text-decoration: none; font-weight: bold; display: inline-block;">← Kembali ke Daftar Tagihan</a>
                    </div>

                    <div class="payment-grid">
                        <div class="payment-info-section">
                            <section class="card" style="margin-bottom: 1.5rem;">
                                <h3 style="border-left: 4px solid var(--primary); padding-left: 10px; margin-bottom: 1rem; color: var(--dark);">Rincian Tagihan Terpilih</h3>
                                <div class="bill-row">
                                    <span>ID Booking</span>
                                    <strong>#GR-<?= $data_booking['id']; ?></strong>
                                </div>
                                <div class="bill-row">
                                    <span>Paket Balap</span>
                                    <strong><?= htmlspecialchars($data_booking['nama_paket']); ?></strong>
                                </div>
                                <div class="bill-row">
                                    <span>Jadwal Sesi</span>
                                    <strong><?= date('d M Y', strtotime($data_booking['tanggal_booking'])); ?> | <?= date('H:i', strtotime($data_booking['jam_booking'])); ?> WIB</strong>
                                </div>
                                <hr>
                                <div class="bill-total">
                                    <p style="color: var(--gray); font-size: 0.85rem;">Total yang Harus Dibayar</p>
                                    <h2>Rp <?= number_format($data_booking['total_harga'], 0, ',', '.'); ?></h2>
                                </div>
                            </section>

                            <section class="card">
                                <h3 style="border-left: 4px solid var(--primary); padding-left: 10px; margin-bottom: 0.5rem; color: var(--dark);">Metode Pembayaran Transfer</h3>
                                <p style="color: var(--gray); font-size: 0.9rem; margin-bottom: 1rem;">Silakan transfer ke salah satu rekening resmi sirkuit berikut:</p>
                                <div class="bank-item">
                                    <strong>Bank BCA</strong>
                                    <p class="account-number">8291-0492-11 <span class="copy-badge" onclick="copyText('8291049211')">Salin</span></p>
                                    <small style="color: var(--gray);">a.n PT GoKart Racing Nusantara</small>
                                </div>
                                <div class="bank-item" style="margin-top: 1rem;">
                                    <strong>Bank Mandiri</strong>
                                    <p class="account-number">132-0021-9942-1 <span class="copy-badge" onclick="copyText('132002199421')">Salin</span></p>
                                    <small style="color: var(--gray);">a.n PT GoKart Racing Nusantara</small>
                                </div>
                            </section>
                        </div>

                        <div class="payment-form-section">
                            <section class="card">
                                <h3 style="border-left: 4px solid var(--primary); padding-left: 10px; margin-bottom: 1rem; color: var(--dark);">Form Konfirmasi Transfer</h3>
                                <form action="" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="booking_id" value="<?= $data_booking['id']; ?>">
                                    
                                    <div class="form-group">
                                        <label>Pilih Bank Tujuan Anda</label>
                                        <select name="bank_tujuan" class="input-field" required>
                                            <option value="">-- Pilih Bank --</option>
                                            <option value="BCA">Transfer ke BCA</option>
                                            <option value="MANDIRI">Transfer ke Mandiri</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Nama Pemilik Rekening Pengirim</label>
                                        <input type="text" name="nama_rekening" class="input-field" placeholder="Contoh: Andi Pratama" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Unggah Bukti Transfer (PNG, JPG max 2MB)</label>
                                        <div class="upload-area" id="uploadArea">
                                            <p id="uploadText" style="color: var(--gray); font-size: 0.9rem;">Klik atau Seret Gambar ke Sini</p>
                                            <input type="file" name="bukti_transfer" id="fileInput" accept="image/*" required style="display: none;">
                                            <img id="imagePreview" src="#" alt="Preview" style="display: none; max-width: 100%; max-height: 180px; margin-top: 10px; border-radius: 6px; border: 1px solid #eee;">
                                        </div>
                                    </div>

                                    <button type="submit" name="submit_pembayaran" class="pay-btn" style="margin-top: 1rem;">Kirim Konfirmasi Pembayaran</button>
                                </form>
                            </section>
                        </div>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="card" style="text-align: center; padding: 4rem; color: var(--gray);">
                    <span style="font-size: 3rem;">🎉</span>
                    <h3 style="margin-top: 1rem; color: var(--dark); font-size: 1.4rem;">Tidak Ada Tagihan Aktif</h3>
                    <p style="margin-top: 0.3rem;">Semua transaksi Anda telah lunas, atau Anda belum melakukan booking jadwal balapan baru.</p>
                    <button class="pay-btn" style="width: auto; margin: 1.5rem auto 0 auto; padding: 0.8rem 2rem;" onclick="window.location.href='../booking/booking.php'">Booking Sekarang</button>
                </div>
            <?php endif; ?>
        </main>
    </div>
    
    <script src="script.js"></script>
</body>
</html>