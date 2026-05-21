document.addEventListener("DOMContentLoaded", () => {
    
    // 1. Logika Interaksi Kunci Logout (Menyesuaikan Class .logout-btn)
    const btnLogout = document.querySelector(".logout-btn");
    
    if (btnLogout) {
        // Putus aksi bawaan onclick inline HTML agar tidak terjadi eksekusi ganda
        btnLogout.removeAttribute('onclick'); 
        
        btnLogout.addEventListener("click", (e) => {
            e.preventDefault(); // Mencegah lompatan instan browser
            
            if (confirm("Apakah Anda yakin ingin keluar dari sistem RacingHub?")) {
                alert("Sesi Anda telah berakhir. Sampai jumpa kembali di sirkuit!");
                // Membuka komentar agar dialihkan secara bersih ke file penghancur session
                window.location.href = '../logout.php';
            }
        });
    }

    // 2. Navigasi Otomatis (Menjaga style menu sidebar tetap menyala pada halaman aktif)
    const currentUrl = window.location.pathname;
    const navLinks = document.querySelectorAll(".nav-menu .nav-link");

    navLinks.forEach(link => {
        // Jika tautan href pada menu COCOK dengan URL browser saat ini
        if (currentUrl.includes(link.getAttribute("href"))) {
            navLinks.forEach(l => l.classList.remove("active"));
            link.classList.add("active");
        }
    });

});