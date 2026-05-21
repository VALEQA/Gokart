<?php
session_start();

// Proteksi Halaman
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

require '../koneksi.php';

$user_id = $_SESSION['id_user'];

// Ambil data user untuk info profil sidebar
$user_query = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$user_id'");
$user_logged = mysqli_fetch_assoc($user_query);

$nama_pembalap = $user_logged['nama_lengkap'] ?? $user_logged['nama'] ?? 'Pembalap GoKart';

// Generate nomor lisensi otomatis berbasis database untuk nilai default
$default_license_no = "FT-" . str_pad($user_id, 4, "0", STR_PAD_LEFT) . "-X";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoKart Racing - Racing License Generator</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="lisensi.css">
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
                    <li><a href="../pembayaran/pembayaran.php" class="nav-link"><span>Pembayaran</span></a></li>
                    <li><a href="../Profil_sayaGokart/profil_saya.php" class="nav-link"><span>Profil Saya</span></a></li>
                </ul>
            </div>
            <div class="sidebar-bottom">
                <div class="sidebar-user">
                    <p><strong><?= htmlspecialchars($nama_pembalap); ?></strong></p>
                    <small><?= htmlspecialchars($user_logged['email']); ?></small>
                </div>
                <button class="logout-btn" onclick="if(confirm('Keluar dari sistem?')) window.location.href='../logout.php'">
                    <span>Logout</span>
                </button>
            </div>
        </aside>

        <main class="main">
            <header class="header">
                <h1>Lisensi Pembalap</h1>
                <p>Desain dan unduh kartu lisensi balap virtual Anda secara instan</p>
            </header>

            <div class="panel">
                <h2 class="panel-header">🪪 RACING LICENSE GENERATOR</h2>
                <p class="panel-desc">Buat Kartu Lisensi Balap Virtualmu Sendiri!</p>

                <div class="license-form">
                    <input type="text" id="driver-name" placeholder="Nama Lengkap" value="<?= htmlspecialchars($nama_pembalap); ?>" autocomplete="off">
                    <input type="text" id="driver-nickname" placeholder="Julukan Balap (Opsional)" autocomplete="off">
                    <input type="number" id="driver-number" placeholder="Nomor Mobil Pilihan (0-99)" min="0" max="99">
                    
                    <label for="driver-photo" class="file-label">
                        <span id="file-label-text">📸 Upload Foto Profil (Opsional)</span>
                    </label>
                    <input type="file" id="driver-photo" accept="image/*">

                    <button id="generate-btn">CETAK LISENSI</button>
                </div>

                <div id="id-card-wrapper">
                    <div class="id-card" id="id-card-result" style="width: 500px; height: 300px; position: relative; text-align: left;">
                        <div class="id-hologram"></div>
                        <div class="id-header">
                            <div class="header-left">
                                <span class="country">IDN</span>
                                <span class="title">RACING SUPER LICENSE</span>
                            </div>
                            <div class="header-right">FULL THROTTLE</div>
                        </div>

                        <div class="id-body">
                            <div class="id-photo-container">
                                <div class="id-photo" id="card-photo">🏁</div>
                                <div class="id-class" id="card-class">PRO CLASS</div>
                            </div>
                            
                            <div class="id-details">
                                <div class="detail-row">
                                    <div class="field">
                                        <p class="label">LICENSE NO.</p>
                                        <h4 id="card-license-no" data-default="<?= $default_license_no; ?>"><?= $default_license_no; ?></h4>
                                    </div>
                                    <div class="field">
                                        <p class="label">BLOOD TYPE</p>
                                        <h4 id="card-blood">O+</h4>
                                    </div>
                                </div>
                                
                                <div class="field">
                                    <p class="label">DRIVER NAME</p>
                                    <h3 id="card-name">MAX VERSTAPPEN</h3>
                                </div>
                                <div class="field">
                                    <p class="label">RACING ALIAS</p>
                                    <h4 class="alias" id="card-nickname">"MAD MAX"</h4>
                                </div>
                                
                                <div class="signature-box">
                                    <p class="label">HOLDER'S SIGNATURE</p>
                                    <div class="signature" id="card-signature">M. Verstappen</div>
                                </div>
                            </div>
                            
                            <div class="id-side">
                                <div class="id-chip"></div>
                                <div class="id-number" id="card-number">33</div>
                            </div>
                        </div>

                        <div class="id-footer">
                            <div class="id-barcode"></div>
                            <div class="id-legal">
                                ISSUED: <?= date('m/Y'); ?><br>
                                VALID THRU: 12/2028
                            </div>
                        </div>
                    </div>

                    <button id="save-btn" class="save-btn">📥 DOWNLOAD HD LICENSE</button>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="license-generator.js"></script>
</body>
</html>