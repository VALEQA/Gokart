<?php
session_start(); // Wajib untuk mengambil data user yang login
require '../koneksi.php'; // Sesuaikan path-nya, biasanya cukup ../koneksi.php jika letaknya di root

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Ambil ID User dari session (Sangat penting agar tidak selalu ID 2)
    if (!isset($_SESSION['id_user'])) {
        echo "error_session";
        exit;
    }
    $user_id = $_SESSION['id_user'];

    // 2. Ambil data dari POST (Pastikan dikirim via Fetch/Form)
    $tanggal      = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $jam          = mysqli_real_escape_string($koneksi, $_POST['jam']);
    $jumlah_orang = mysqli_real_escape_string($koneksi, $_POST['kapasitas']);
    $total_harga  = mysqli_real_escape_string($koneksi, $_POST['total_harga']);
    
    // Tips: Agar paket_id dinamis, pastikan di JavaScript kamu mengirim 'paket_id' juga
    $paket_id     = isset($_POST['paket_id']) ? $_POST['paket_id'] : 1; 
    
    $status       = "aktif";

    // 3. Query INSERT (Gunakan $koneksi sesuai file koneksi.php kamu)
    $query = "INSERT INTO booking (user_id, paket_id, tanggal_booking, jam_booking, jumlah_orang, total_harga, status) 
              VALUES ('$user_id', '$paket_id', '$tanggal', '$jam', '$jumlah_orang', '$total_harga', '$status')";

    if (mysqli_query($koneksi, $query)) {
        echo "success";
    } else {
        // Jika error, tampilkan detailnya untuk debug
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>