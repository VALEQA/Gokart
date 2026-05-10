document.addEventListener("DOMContentLoaded", () => {
    
    // Logika Logout
    const btnLogout = document.getElementById("btnLogout");
    if (btnLogout) {
        btnLogout.addEventListener("click", () => {
            if (confirm("Apakah Anda yakin ingin keluar dari sistem?")) {
                alert("Sesi berakhir. Sampai jumpa kembali!");
                // window.location.href = '../logout.php';
            }
        });
    }

    // Navigasi Aktif (Opsional jika sidebar tidak di-include)
    const navLinks = document.querySelectorAll(".nav-link");
    navLinks.forEach(link => {
        link.addEventListener("click", function() {
            navLinks.forEach(l => l.classList.remove("active"));
            this.classList.add("active");
        });
    });

});