<?php

include '../koneksi.php';

$id = $_POST['id'];

$password_lama = $_POST['password_lama'];

$password_baru = $_POST['password_baru'];

$konfirmasi_password = $_POST['konfirmasi_password'];

// ambil data user
$query = mysqli_query($conn,
"SELECT * FROM users WHERE id='$id'");

$data = mysqli_fetch_assoc($query);

// cek password lama
if($password_lama != $data['password']){

    echo "
    <script>
        alert('Password lama salah!');
        window.location='profil_saya.php';
    </script>
    ";

    exit;
}

// cek konfirmasi password
if($password_baru != $konfirmasi_password){

    echo "
    <script>
        alert('Konfirmasi password tidak cocok!');
        window.location='profil_saya.php';
    </script>
    ";

    exit;
}

// update password
mysqli_query($conn,
"UPDATE users
SET password='$password_baru'
WHERE id='$id'");

// berhasil
echo "
<script>
    alert('Password berhasil diganti!');
    window.location='profil_saya.php';
</script>
";

?>