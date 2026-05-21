document.addEventListener("DOMContentLoaded", () => {
    
    // 1. Menangani Navigasi Aktif (Sidebar Menu)
    const navLinks = document.querySelectorAll(".nav-link");
    navLinks.forEach((link) => {
        link.addEventListener("click", () => {
            navLinks.forEach((l) => l.classList.remove("active"));
            link.classList.add("active");
        });
    });

    // 2. Animasi Angka Statistik (Dukung Angka Bulat & Desimal Milidetik)
    const animateStats = () => {
        const stats = document.querySelectorAll(".stat-number");
        
        stats.forEach((num) => {
            const originalText = num.innerText.trim();
            
            // JIKA ANTISIPASI: Belum ada data balapan atau kosong, abaikan animasi
            if (originalText === "Belum Ada" || originalText === "-") return;
            
            // Bersihkan format teks: ubah koma ke titik, hapus kata "detik" atau spasi
            const cleanNumber = originalText.replace(/,/g, '.').replace(/[^0-9.]/g, '');
            const target = parseFloat(cleanNumber);

            // Validasi jika gagal dikonversi menjadi angka murni
            if (isNaN(target) || target <= 0) return;

            let count = 0;
            const duration = 1000; // Durasi animasi dipercepat menjadi 1 detik agar terasa responsif
            const startTime = performance.now();

            const updateCount = (currentTime) => {
                const elapsedTime = currentTime - startTime;
                const progress = Math.min(elapsedTime / duration, 1); // Batasi progress maksimal 1 (100%)

                // Menggunakan rumus Easing Out agar animasi melambat di akhir (efek premium)
                const easeProgress = 1 - Math.pow(1 - progress, 3);
                count = easeProgress * target;

                // Cek apakah angka target memiliki format desimal (Waktu Terbaik)
                if (originalText.includes(".") || originalText.includes(",")) {
                    // Tampilkan 3 digit desimal sesuai format waktu gokart (Milidetik)
                    const formattedCount = count.toFixed(3);
                    num.innerText = originalText.includes(",") ? formattedCount.replace('.', ',') + " detik" : formattedCount + " detik";
                } else {
                    // Angka bulat (Total Bermain & Booking Aktif)
                    num.innerText = Math.floor(count);
                }

                if (progress < 1) {
                    requestAnimationFrame(updateCount);
                } else {
                    // Kembalikan ke teks asli database saat animasi selesai
                    num.innerText = originalText;
                }
            };
            
            requestAnimationFrame(updateCount);
        });
    };
    animateStats();

    // 3. Interaksi Tombol Logout (Mengambil alih kendali redirect dari HTML)
    const logoutBtn = document.querySelector(".logout-btn");
    if (logoutBtn) {
        // Hapus atribut onclick bawaan HTML (jika ada) agar fungsi dikontrol penuh lewat JS ini
        logoutBtn.removeAttribute('onclick'); 
        logoutBtn.addEventListener("click", (e) => {
            e.preventDefault();
            if (confirm("Apakah Anda yakin ingin keluar dari sistem GoKart Racing?")) {
                alert("Sesi berakhir. Sampai jumpa di lintasan! 🏁");
                window.location.href = '../logout.php';
            }
        });
    }

    // 4. Delegasi Klik untuk Kotak Aksi Cepat (Mencegah Bug Navigasi)
    document.addEventListener("click", (e) => {
        const aksiBox = e.target.closest(".aksi-box.clickable");
        if (aksiBox) {
            const title = aksiBox.querySelector("h3").innerText.trim();
            
            // Biarkan redirect bawaan HTML berjalan alami untuk modul yang sudah siap
            if (title === "Lihat Leaderboard") {
                console.log("Membuka halaman papan peringkat...");
            }
        }
    });

});