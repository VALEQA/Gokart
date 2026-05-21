document.addEventListener("DOMContentLoaded", () => {
  console.log("JS Leaderboard GoKart Aktif");

  // ==========================================================================
  // 1. EFEK INTERAKSI BARIS TABEL (HOVER EFFECT)
  // ==========================================================================
  const tableRows = document.querySelectorAll(".leaderboard-table tbody tr");
  
  tableRows.forEach((row) => {
    // Mengecek jumlah kolom td. Jika cuma 1, artinya itu baris pesan 'Data Kosong' (colspan="4")
    const columns = row.querySelectorAll("td");
    
    if (columns.length > 1) {
      row.style.transition = "all 0.2s ease";

      row.addEventListener("mouseenter", () => {
        row.style.transform = "scale(1.01)"; // Sedikit membesar memberi efek pop-out 
        row.style.backgroundColor = "rgba(232, 240, 254, 0.3)"; // Highlight warna background tipis
        row.style.boxShadow = "0 6px 15px rgba(0,0,0,0.05)";
        row.style.cursor = "pointer";
      });
      
      row.addEventListener("mouseleave", () => {
        row.style.transform = "scale(1)";
        row.style.backgroundColor = "";
        row.style.boxShadow = "none";
      });
    }
  });

  // ==========================================================================
  // 2. LOGIKA INTERAKSI MENUS TAB (OPSIONAL - ADD-ON)
  // ==========================================================================
  // Memberikan efek visual klik instan pada tombol tab sektor sebelum halaman reload
  const tabButtons = document.querySelectorAll(".tab-btn");
  tabButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
      tabButtons.forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");
    });
  });
});