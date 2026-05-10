document.addEventListener("DOMContentLoaded", () => {
    
    // 1. Menangani Navigasi Aktif
    const navLinks = document.querySelectorAll(".nav-link");
    navLinks.forEach((link) => {
        link.addEventListener("click", () => {
            navLinks.forEach((l) => l.classList.remove("active"));
            link.classList.add("active");
        });
    });

    // 2. Animasi Angka Statistik (Support Milidetik & Desimal)
    const animateStats = () => {
        const stats = document.querySelectorAll(".stat-number");
        
        stats.forEach((num) => {
            const originalText = num.innerText.trim();
            
            // Bersihkan format: ganti koma ke titik, hapus karakter non-angka
            const cleanNumber = originalText.replace(/,/g, '.').replace(/[^0-9.]/g, '');
            const target = parseFloat(cleanNumber);

            // Jika bukan angka (seperti '-'), abaikan animasi
            if (isNaN(target) || target <= 0) return;

            let count = 0;
            const duration = 1500; // 1.5 detik
            const increment = target / (duration / 16); 

            const updateCount = () => {
                count += increment;
                if (count < target) {
                    if (Number.isInteger(target)) {
                        num.innerText = Math.floor(count);
                    } else {
                        // Tampilkan desimal, kembalikan titik ke koma untuk tampilan
                        num.innerText = count.toFixed(2).replace('.', ',');
                    }
                    requestAnimationFrame(updateCount);
                } else {
                    // Set hasil akhir sesuai teks asli dari database
                    num.innerText = originalText;
                }
            };
            updateCount();
        });
    };
    animateStats();

    // 3. Interaksi Tombol Logout
    const logoutBtn = document.querySelector(".logout-btn");
    if (logoutBtn) {
        logoutBtn.addEventListener("click", () => {
            if (confirm("Apakah Anda yakin ingin keluar?")) {
                alert("Sesi berakhir. Sampai jumpa di lintasan!");
                // window.location.href = 'logout.php';
            }
        });
    }

    // 4. Delegasi Klik untuk Tombol Detail & Aksi
    document.addEventListener("click", (e) => {
        // Cek jika yang diklik adalah card aksi-box
        const aksiBox = e.target.closest(".aksi-box.clickable");
        if (aksiBox) {
            const title = aksiBox.querySelector("h3").innerText;
            if (title === "Lihat Leaderboard") {
                alert("Fitur Leaderboard segera hadir!");
            }
        }
    });

});