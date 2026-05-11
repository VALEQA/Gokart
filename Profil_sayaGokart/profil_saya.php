<?php
session_start(); // Wajib ada untuk baca session
include '../koneksi.php';

// Cek apakah ada yang login
if (!isset($_SESSION['login'])) { // Menggunakan key login milik Sakha
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['id_user']; // Mengambil ID dari session Sakha
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$id_user'");
$data = mysqli_fetch_assoc($query);
?>
<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
    content="width=device-width, initial-scale=1.0">

    <title>
        Profil Saya - GoKart Racing
    </title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="container">

    <!-- Sidebar -->
    <aside class="sidebar">
    <h2>GoKart Racing</h2>
    <p class="sub">Booking System</p>
    <<ul>
    <li><a href="../index.html" style="text-decoration:none; color:inherit; display:block;">Home</a></li>
    
    <li><a href="../dashboard/dashboard.php">Dashboard</a></li>
    <li><a href="../booking/booking.php">Booking</a></li>
    <li><a href="../riwayat booking/riwayat.php">Riwayat Balapan</a></li>
    <li><a href="../pertemuan 3 copy/detail_booking.php" style="text-decoration:none; color:inherit; display:block;">Detail Booking</a></li>
    
    <li class="active">Profil Saya</li>
</ul>

    <div class="logout">
        <a href="../login.php" style="text-decoration:none; color:gray;">Logout</a>
    </div>
</aside>
    <!-- Main -->
    <main class="main">

        <h1>Profil Saya</h1>

        <p class="desc">
            Kelola informasi profil Anda
        </p>

        <!-- FORM -->
        <form
        action="update_profil.php"
        method="POST"
        id="profileForm">

            <input
            type="hidden"
            name="id"
            value="<?php echo $data['id']; ?>">

            <!-- Profile Header -->
            <div class="profile-card">

                <div class="banner"></div>

                <div class="profile-info">

                    <div class="avatar"></div>

                    <div class="text">

                        <h3>
                           <h3><?php echo $data['nama_lengkap']; ?></h3>
                        </h3>

                        <p>
                            <?php echo $data['email']; ?>
                        </p>

                    </div>

                    <!-- BUTTON -->
                    <button
                    type="button"
                    id="editBtn"
                    class="btn red">

                        Edit Profil

                    </button>

                </div>

            </div>

            <!-- Informasi -->
            <div class="card">

                <h3>
                    Informasi Pribadi
                </h3>

                <label>
                    Nama Lengkap
                </label>

                <input
                type="text"
                name="nama"
                id="nama"
                value="<?php echo $data['nama_lengkap']; ?>"
                readonly>

                <label>
                    Email
                </label>

                <input
                type="email"
                name="email"
                id="email"
                value="<?php echo $data['email']; ?>"
                readonly>

                <label>
                    Nomor Telepon
                </label>

                <input
                type="text"
                name="no_telpon"
                id="no_telpon"
                value="<?php echo $data['nomor_hp']; ?>"
                readonly>

            </div>

        </form>

        <!-- Statistik -->
        <div class="grid">

            <div class="card">

                <h3>Statistik</h3>

                <p>
                    Total Race
                    <span class="right">
                        24
                    </span>
                </p>

                <p>
                    Best Time

                    <span class="green">
                        00:45.234
                    </span>
                </p>

                <p>
                    Member Since

                    <span class="right">
                        Jan 2026
                    </span>
                </p>

            </div>

            <!-- Keamanan -->
            <div class="card">

                <h3>Keamanan</h3>

                <form
                action="update_password.php"
                method="POST">

                    <input
                    type="hidden"
                    name="id"
                    value="<?php echo $data['id']; ?>">

                    <label>
                        Password Lama
                    </label>

                    <input
                    type="password"
                    name="password_lama"
                    required>

                    <label>
                        Password Baru
                    </label>

                    <input
                    type="password"
                    name="password_baru"
                    required>

                    <label>
                        Konfirmasi Password
                    </label>

                    <input
                    type="password"
                    name="konfirmasi_password"
                    required>

                    <button
                    type="submit"
                    class="btn red">

                        Ganti Password

                    </button>

                </form>

            </div>

        </div>

    </main>

</div>

<!-- SCRIPT -->
<script>

let isEditing = false;

const editBtn =
document.getElementById("editBtn");

const inputs = [

    document.getElementById("nama"),

    document.getElementById("email"),

    document.getElementById("no_telpon")

];

const form =
document.getElementById("profileForm");

editBtn.addEventListener("click", function(){

    if(!isEditing){

        // buka readonly
        inputs.forEach(input => {

            input.removeAttribute("readonly");

        });

        // ubah tombol
        editBtn.innerHTML =
        "Simpan Perubahan";

        isEditing = true;

    }else{

        // submit form
        form.submit();

    }

});

</script>

</body>
</html>