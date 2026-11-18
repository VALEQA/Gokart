# 🏁 Go-Kart Racing Hub

Sistem informasi manajemen sirkuit go-kart berbasis web. Pembalap bisa melihat paket bermain, melakukan booking, mengunggah bukti pembayaran, memantau riwayat balapan, dan mencetak kartu lisensi. Admin mengelola paket, pengguna, input waktu lap, serta rekap keuangan.

Dibangun dengan **PHP (prosedural) + MySQL** dan dirancang untuk berjalan di **XAMPP** tanpa framework, cocok sebagai proyek pembelajaran maupun dasar aplikasi booking yang lebih besar.

![Go-Kart Racing Hub](kart.png)

---

## ✨ Fitur

### Sisi Pembalap (user)
| Fitur | Keterangan |
|---|---|
| Registrasi & Login | Password disimpan dengan `password_hash()` dan diverifikasi dengan `password_verify()`. |
| Booking Sesi | Pilih paket bermain, tanggal, jam, dan jumlah orang; total harga dihitung otomatis. |
| Pembayaran | Upload bukti transfer, status booking dipantau admin. |
| Riwayat Booking | Daftar sesi yang pernah dibooking beserta detail dan statusnya. |
| Leaderboard | Peringkat berdasarkan overall lap maupun waktu per sektor (S1/S2/S3). |
| Hasil Balapan | Waktu lap milik sendiri per sektor. |
| Kartu Lisensi | Halaman lisensi pembalap yang bisa dicetak, nomor lisensi dibuat otomatis dari ID user (`FT-0001-X`). |
| Profil | Ubah data diri dan foto profil. |

### Sisi Admin
| Fitur | Keterangan |
|---|---|
| Dashboard | Ringkasan aktivitas sirkuit dan booking masuk. |
| Kelola Paket | CRUD paket bermain (nama, deskripsi, durasi, maksimal orang, harga). |
| Kelola Pengguna | CRUD akun pembalap dan admin. |
| Input Waktu | Memasukkan hasil balapan (sektor 1–3, total lap) yang langsung mengisi leaderboard. |
| Leaderboard Admin | Pemantauan peringkat seluruh pembalap. |
| Riwayat Keuangan | Rekap transaksi/pembayaran yang masuk. |

Setiap halaman (kecuali login/register) dilindungi pengecekan `$_SESSION['login']`, dan halaman admin menambahkan pengecekan `$_SESSION['role'] === 'admin'`.

---

## 🧰 Teknologi

- **Bahasa**: PHP 8 (gaya prosedural, ekstensi `mysqli`)
- **Database**: MySQL / MariaDB, nama database `racinghub`
- **Frontend**: HTML5, CSS3, JavaScript vanilla
- **Server lokal**: XAMPP (Apache + MySQL)

---

## 📁 Struktur Folder

```text
Gokart/
├── index.php                 # Landing page
├── login.php / register.php  # Autentikasi
├── logout.php
├── koneksi.php               # Koneksi database (host, user, pass, db)
├── kart.png                  # Aset gambar
├── racing_hub.sql            # Dump lengkap: skema + data contoh
├── polosan.sql               # Skema saja (tanpa data contoh)
├── admin/                    # Dashboard & CRUD admin
├── booking/                  # Form dan proses booking
├── pembayaran/               # Upload bukti transfer (+ folder uploads/)
├── riwayat booking/          # Riwayat booking user
├── hasil_balapan/            # Waktu lap user
├── leaderboard/              # Papan peringkat
├── lisensi/                  # Kartu lisensi pembalap
├── Profil/                   # Profil & update profil
└── sampah/                   # File eksperimen lama (tidak dipakai aplikasi)
```

---

## 🚀 Cara Menjalankan

### 1. Siapkan XAMPP
Install [XAMPP](https://www.apachefriends.org/) lalu jalankan **Apache** dan **MySQL** dari XAMPP Control Panel.

### 2. Letakkan project di `htdocs`
Salin folder ini ke `C:\xampp\htdocs\Gokart` (Windows) atau `/opt/lampp/htdocs/Gokart` (Linux/macOS).

### 3. Import database
Buka [http://localhost/phpmyadmin](http://localhost/phpmyadmin) → tab **Import**:

- `racing_hub.sql` — skema **+ data contoh** (paket, user, booking, hasil balapan). Cocok untuk langsung mencoba.
- `polosan.sql` — skema **kosong** saja. Pakai ini kalau ingin mulai dari nol.

Keduanya membuat database `racinghub` secara otomatis.

### 4. Sesuaikan koneksi
Edit `koneksi.php` bila konfigurasi MySQL Anda berbeda:

```php
$host = "localhost";
$user = "root";     // default XAMPP
$pass = "";         // default XAMPP kosong
$db   = "racinghub";
```

### 5. Buka aplikasi
```
http://localhost/Gokart/
```

Daftar akun baru lewat halaman **Register** untuk masuk sebagai user. Untuk masuk sebagai admin, gunakan akun ber-role `admin` dari data contoh (`admin@gokart.com`), atau ubah role akun Anda sendiri langsung di tabel `users`:

```sql
UPDATE users SET role = 'admin' WHERE email = 'email-kamu@contoh.com';
```

### 6. Izin folder upload
Pastikan folder `pembayaran/uploads/` dapat ditulis oleh web server agar bukti transfer bisa tersimpan.

---

## 🔐 Catatan Keamanan

Proyek ini dibuat untuk keperluan pembelajaran, jadi sebelum dipakai di lingkungan nyata:

- **Ganti kredensial default** di `koneksi.php` dan hapus seluruh data contoh (`racing_hub.sql` memuat akun demo beserta hash password-nya).
- Query masih menggunakan `mysqli_real_escape_string()`. Untuk produksi, migrasikan ke **prepared statement** (`mysqli_prepare` / PDO) agar sepenuhnya aman dari SQL injection.
- Aktifkan HTTPS dan set `session.cookie_httponly` / `session.cookie_secure`.
- Tambahkan validasi tipe file dan ukuran maksimum pada upload bukti pembayaran.
- Folder `sampah/` berisi file uji coba yang tidak dipakai; aman dihapus.

---

## 📄 Lisensi

Dirilis di bawah lisensi **MIT** — bebas dipakai, dimodifikasi, dan dibagikan dengan tetap mencantumkan atribusi.
