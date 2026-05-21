document.addEventListener("DOMContentLoaded", () => {
  const generateBtn = document.getElementById("generate-btn");
  const saveBtn = document.getElementById("save-btn");
  const photoInput = document.getElementById("driver-photo");
  let uploadedImageURL = "";

  if (generateBtn) {
    // Handle input foto profil
    photoInput.addEventListener("change", function (event) {
      const file = event.target.files[0];
      if (file) {
        // Validasi tambahan agar browser tidak crash jika user upload file raksasa
        if (file.size > 2 * 1024 * 1024) {
          alert("Pit Stop! Ukuran foto terlalu besar (Maksimal 2MB).");
          this.value = "";
          return;
        }

        uploadedImageURL = URL.createObjectURL(file);
        document.getElementById("file-label-text").innerText =
          "✅ Foto Berhasil Dimasukkan!";
        document.getElementById("file-label-text").style.border =
          "1px solid #10b981";
      }
    });

    // Trigger cetak lisensi virtual
    generateBtn.addEventListener("click", () => {
      const nameInput = document.getElementById("driver-name").value;
      const nickInput = document.getElementById("driver-nickname").value;
      let numInput = document.getElementById("driver-number").value;

      if (nameInput.trim() === "") {
        alert("Pit Stop! Isi dulu Namamu sebelum bikin lisensi!");
        return;
      }

      // Validasi batas nomor lambung mobil
      if (numInput > 99) numInput = 99;
      if (numInput < 0) numInput = 0;

      // Suntik data ke elemen kartu lisensi
      document.getElementById("card-name").innerText = nameInput.toUpperCase();
      document.getElementById("card-nickname").innerText = nickInput
        ? `"${nickInput.toUpperCase()}"`
        : '"THE ROOKIE"';

      document.getElementById("card-number").innerText = numInput
        ? numInput
        : "99";
      document.getElementById("card-signature").innerText = nameInput;

      // Mengunci nomor seri unik acak agar terlihat seperti ID card resmi
      document.getElementById("card-license-no").innerText =
        "FT-" + Math.floor(1000 + Math.random() * 9000) + "-X";

      // Render Foto Profil ke dalam Kartu
      const cardPhoto = document.getElementById("card-photo");
      if (uploadedImageURL !== "") {
        cardPhoto.style.backgroundImage = `url(${uploadedImageURL})`;
        cardPhoto.style.backgroundSize =
          "cover"; /* Menjaga foto proporsional tidak gepeng */
        cardPhoto.style.backgroundPosition =
          "center"; /* Fokus pada tengah foto */
        cardPhoto.innerHTML = ""; /* Menghilangkan emoji bendera 🏁 */
      } else {
        cardPhoto.style.backgroundImage = "none";
        cardPhoto.innerHTML =
          "🏁"; /* Kembalikan emoji bendera jika tanpa foto */
      }

      // Tampilkan wrapper kartu lisensi
      document.getElementById("id-card-wrapper").style.display = "block";

      // Efek pop-up animasi ringan saat muncul
      const idCard = document.getElementById("id-card-result");
      idCard.style.transform = "scale(0.95)";
      setTimeout(() => {
        idCard.style.transform = "scale(1)";
        idCard.style.transition = "0.3s ease";
      }, 50);
    });

    // Download kartu menggunakan html2canvas
    saveBtn.addEventListener("click", () => {
      const cardElement = document.getElementById("id-card-result");
      const originalText = saveBtn.innerText;
      saveBtn.innerText = "⏳ MEMOTRET KARTU...";

      html2canvas(cardElement, {
        scale: 2, // Membuat hasil screenshot tajam (HD)
        backgroundColor: null, // Sisi luar lengkungan kartu transparan
        useCORS: true, // Mengatasi kendala keamanan rendering gambar lokal browser
      }).then((canvas) => {
        const link = document.createElement("a");
        // Membersihkan spasi pada nama file download agar rapi
        const driverName = document
          .getElementById("card-name")
          .innerText.replace(/\s+/g, "_");

        link.download = `Lisensi_Balap_${driverName}.png`;
        link.href = canvas.toDataURL("image/png");
        link.click();

        saveBtn.innerText = "✅ BERHASIL DISIMPAN!";
        setTimeout(() => {
          saveBtn.innerText = originalText;
        }, 3000);
      });
    });
  }
});
