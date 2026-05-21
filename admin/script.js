document.addEventListener("DOMContentLoaded", () => {
  console.log("JS Admin GoKart Aktif");

  // ==========================================================================
  // 1. EFEK HIGHLIGHT BARIS TABEL (ROW HOVER)
  // ==========================================================================
  const tableRows = document.querySelectorAll(".admin-table tbody tr");
  
  tableRows.forEach((row) => {
    // Memastikan baris tersebut bukan baris pesan "Data Kosong" (colspan="6")
    const columns = row.querySelectorAll("td");
    if (columns.length > 1) {
      row.style.transition = "all 0.2s ease";

      row.addEventListener("mouseenter", () => {
        row.style.backgroundColor = "rgba(29, 53, 87, 0.03)"; // Highlight biru gelap tipis sesuai tema
        row.style.transform = "translateX(4px)"; // Efek geser sedikit ke kanan saat disorot
      });

      row.addEventListener("mouseleave", () => {
        row.style.backgroundColor = "";
        row.style.transform = "translateX(0)";
      });
    }
  });

  // ==========================================================================
  // 2. OTOMATISASI DAN FALLBACK UTK LINK BUKTI TRANSFER
  // ==========================================================================
  // Jika kamu ingin menambahkan fitur modal pop-up gambar langsung di halaman tanpa buka tab baru,
  // Kamu bisa menambahkan modifikasi interaksi klik di area ini ke depannya.
});