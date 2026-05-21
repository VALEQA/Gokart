document.addEventListener("DOMContentLoaded", () => {
  console.log("JS Admin GoKart Aktif");

  // ==========================================================================
  // 1. EFEK HIGHLIGHT BARIS TABEL (ROW HOVER)
  // ==========================================================================
  const tableRows = document.querySelectorAll(".admin-table tbody tr");
  
  tableRows.forEach((row) => {
    const columns = row.querySelectorAll("td");
    if (columns.length > 1) {
      row.style.transition = "all 0.2s ease";
      row.addEventListener("mouseenter", () => {
        row.style.backgroundColor = "rgba(29, 53, 87, 0.03)";
        row.style.transform = "translateX(4px)";
      });
      row.addEventListener("mouseleave", () => {
        row.style.backgroundColor = "";
        row.style.transform = "translateX(0)";
      });
    }
  });

  // ==========================================================================
  // 2. MODAL POP-UP PREVIEW BUKTI TRANSFER (FITUR TAMBAHAN)
  // ==========================================================================
  const previewButtons = document.querySelectorAll(".view-proof-btn");
  
  // Buat elemen modal secara dinamis lewat JavaScript biar gak ngotak-ngatik HTML
  const modal = document.createElement("div");
  modal.style.cssText = `
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.7); display: none; justify-content: center;
    align-items: center; z-index: 9999; cursor: pointer;
  `;
  
  const modalImg = document.createElement("img");
  modalImg.style.cssText = "max-width: 90%; max-height: 80%; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);";
  
  modal.appendChild(modalImg);
  document.body.appendChild(modal);

  // Daftarkan aksi klik pada tombol "Lihat Bukti"
  previewButtons.forEach(btn => {
    btn.addEventListener("click", (e) => {
      // Cegah browser membuka tab baru
      e.preventDefault(); 
      const imageSrc = btn.getAttribute("href");
      
      // Masukkan sumber gambar lalu munculkan modalnya
      modalImg.src = imageSrc;
      modal.style.display = "flex";
    });
  });

  // Klik di area mana saja pada modal untuk menutup kembali
  modal.addEventListener("click", () => {
    modal.style.display = "none";
  });
});