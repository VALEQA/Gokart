<?php
session_start();
require '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Validasi Sesi Login
    if (!isset($_SESSION['id_user'])) {
        echo "error_session";
        exit;
    }
    $user_id = $_SESSION['id_user'];

    // 2. Tangkap Data & Amankan dari SQL Injection (Menyesuaikan dengan nama input di HTML)
    $tanggal      = mysqli_real_escape_string($koneksi, $_POST['tanggal_booking']); // FIX
    $jam          = mysqli_real_escape_string($koneksi, $_POST['jam_booking']);     // FIX
    $jumlah_orang = mysqli_real_escape_string($koneksi, $_POST['jumlah_orang']);    // FIX
    $total_harga  = mysqli_real_escape_string($koneksi, $_POST['total_harga']);
    $paket_id     = intval($_POST['paket_id']); 
    
    $status       = "aktif";

    // 3. Validasi Tambahan: Pastikan user tidak mengirimkan form kosong via JS Bypass
    if (empty($tanggal) || empty($jam) || empty($paket_id) || empty($jumlah_orang)) {
        echo "Error: Data booking tidak lengkap!";
        exit;
    }

    // 4. Eksekusi Query ke Database `booking`
    $query = "INSERT INTO booking (user_id, paket_id, tanggal_booking, jam_booking, jumlah_orang, total_harga, status) 
              VALUES ('$user_id', '$paket_id', '$tanggal', '$jam', '$jumlah_orang', '$total_harga', '$status')";

    if (mysqli_query($koneksi, $query)) {
        // Mengembalikan ID baris yang baru saja masuk untuk diolah di booking.js (misal buat redirect ke invoice/pembayaran)
        echo trim(mysqli_insert_id($koneksi));
        exit; 
    } else {
        echo "Error: " . mysqli_error($koneksi);
        exit;
    }
} else {
    // Jika ada yang mencoba akses file ini langsung via URL, tendang balik ke halaman booking
    header("Location: booking.php");
    exit;
}
?>