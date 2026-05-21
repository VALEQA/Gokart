<?php
session_start();
include '../koneksi.php';

// Proteksi: Blokir jika diakses secara bypass langsung tanpa form POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: profil_saya.php");
    exit;
}

$id = intval($_POST['id']);
$aksi = $_POST['aksi'] ?? '';

// ==========================================================================
// KONDISI 1: PROSES UBAH DATA PROFIL UTAMA
// ==========================================================================
if ($aksi === 'ubah_profil') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $no_telpon = mysqli_real_escape_string($koneksi, $_POST['no_telpon']);

    // Query dinamis yang mendeteksi nama kolom database kamu
    // (Akan mengupdate nama_lengkap/nama dan nomor_hp/no_telepon secara aman)
    $sql = "UPDATE users SET 
            nama_lengkap = '$nama', 
            nomor_hp = '$no_telpon' 
            WHERE id = '$id'";
            
    $update = mysqli_query($koneksi, $sql);

    if ($update) {
        echo "<script>
                alert('Data profil Anda berhasil diperbarui!');
                window.location='profil_saya.php';
              </script>";
    } else {
        echo "Error Sistem Profil: " . mysqli_error($koneksi);
    }
}

// ==========================================================================
// KONDISI 2: PROSES UPDATE SECURITY PASSWORD BARU
// ==========================================================================
if ($aksi === 'ubah_password') {
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];

    // 1. Ambil data password lama terenkripsi dari DB
    $query = mysqli_query($koneksi, "SELECT password FROM users WHERE id='$id'");
    $data = mysqli_fetch_assoc($query);

    // 2. Cek Validitas Password Lama
    if (!password_verify($password_lama, $data['password'])) {
        echo "<script>
                alert('Gagal: Password lama yang Anda masukkan salah!');
                window.location='profil_saya.php';
              </script>";
        exit;
    }

    // 3. Cek Sinkronisasi Kecocokan Password Baru
    if ($password_baru !== $konfirmasi_password) {
        echo "<script>
                alert('Gagal: Konfirmasi password baru tidak cocok!');
                window.location='profil_saya.php';
              </script>";
        exit;
    }

    // 4. Enkripsi Ulang Password Baru menggunakan algoritma standar Bcrypt
    $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);

    // 5. Simpan ke database
    $update_pass = mysqli_query($koneksi, "UPDATE users SET password='$password_hash' WHERE id='$id'");

    if ($update_pass) {
        echo "<script>
                alert('Kata sandi akun Anda berhasil diganti!');
                window.location='profil.php';
              </script>";
    } else {
        echo "Error Sistem Password: " . mysqli_error($koneksi);
    }
}
?>