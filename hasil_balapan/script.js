document.addEventListener("DOMContentLoaded", () => {
  
  // 1. Animasi Hover Interaktif pada Baris Tabel Sektor
  const tableRows = document.querySelectorAll(".racing-table tbody tr");

  tableRows.forEach((row) => {
    // Memastikan baris tersebut berisi data balapan (bukan baris pesan 'data kosong' yang memiliki colspan 7)
    const columns = row.querySelectorAll("td");
    
    if (columns.length > 1) {
      row.style.transition = "all 0.2s ease-in-out";

      row.addEventListener("mouseenter", () => {
        row.style.transform = "translateX(6px)";
        row.style.backgroundColor = "rgba(230, 57, 70, 0.03)";
        row.style.boxShadow = "inset 3px 0 0 var(--primary)"; // Memberi pilar merah tipis di kiri baris saat di-hover
      });

      row.addEventListener("mouseleave", () => {
        row.style.transform = "translateX(0)";
        row.style.backgroundColor = "";
        row.style.boxShadow = "";
      });
    }
  });

  // 2. Event Listener Langsung untuk Tombol Logout di Sidebar
  const logoutBtn = document.querySelector(".logout-btn");
  if (logoutBtn) {
    // Putus total fungsi onclick inline bawaan HTML agar tidak terjadi konfirmasi ganda
    logoutBtn.removeAttribute("onclick");
    
    logoutBtn.addEventListener("click", (e) => {
      e.preventDefault(); // Menghentikan trigger instan browser
      
      if (confirm("Apakah Anda yakin ingin keluar dari sistem GoKart Racing?")) {
        window.location.href = "../logout.php";
      }
    });
  }
});