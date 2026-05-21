document.addEventListener("DOMContentLoaded", () => {
  const uploadArea = document.getElementById("uploadArea");
  const fileInput = document.getElementById("fileInput");
  const imagePreview = document.getElementById("imagePreview");
  const uploadText = document.getElementById("uploadText");

  // Memicu klik file input ketika area kotak di-klik
  if (uploadArea) {
    uploadArea.addEventListener("click", () => fileInput.click());

    // Handling Drag & Drop file image
    uploadArea.addEventListener("dragover", (e) => {
      e.preventDefault();
      uploadArea.style.borderColor = "var(--primary)";
      uploadArea.style.background = "var(--primary-light)";
    });

    uploadArea.addEventListener("dragleave", () => {
      uploadArea.style.borderColor = "#ddd";
      uploadArea.style.background = "var(--bg-body)";
    });

    uploadArea.addEventListener("drop", (e) => {
      e.preventDefault();
      uploadArea.style.borderColor = "#ddd";
      uploadArea.style.background = "var(--bg-body)";

      if (e.dataTransfer.files.length > 0) {
        const file = e.dataTransfer.files[0];

        // Validasi tipe file agar benar-benar gambar
        if (file.type.startsWith("image/")) {
          fileInput.files = e.dataTransfer.files;
          previewImage(file);
        } else {
          alert(
            "Format file tidak valid! Harap masukkan file gambar (PNG/JPG/JPEG).",
          );
        }
      }
    });
  }

  // Mengubah tampilan ketika file dipilih lewat browser explorer
  if (fileInput) {
    fileInput.addEventListener("change", function () {
      if (this.files && this.files[0]) {
        previewImage(this.files[0]);
      }
    });
  }

  // Fungsi membaca gambar dan menampilkannya sebagai preview thumbnail
  function previewImage(file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      imagePreview.src = e.target.result;
      imagePreview.style.display = "inline-block";
      uploadText.innerText = `File Terpilih: ${file.name}`;
    };
    reader.readAsDataURL(file);
  }
});

// Fungsi utilitas tombol untuk menyalin nomor rekening bank otomatis ke clipboard device
function copyText(textValue) {
  navigator.clipboard
    .writeText(textValue)
    .then(() => {
      alert("Nomor rekening berhasil disalin ke clipboard!");
    })
    .catch((err) => {
      console.error("Gagal menyalin teks: ", err);
      // Fallback untuk browser lama yang tidak mendukung navigator.clipboard secara penuh
      const tempInput = document.createElement("input");
      tempInput.value = textValue;
      document.body.appendChild(tempInput);
      tempInput.select();
      document.execCommand("copy");
      document.body.removeChild(tempInput);
      alert("Nomor rekening berhasil disalin!");
    });
}
