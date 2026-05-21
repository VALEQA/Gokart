document.addEventListener("DOMContentLoaded", () => {
  // 1. Definisikan Objek Penampung Elemen UI
  const elements = {
    timeBtns: document.querySelectorAll(".time-btn"),
    paketCards: document.querySelectorAll(".paket-card"),
    inputDate: document.getElementById("inputDate"),
    sumDate: document.getElementById("sumDate"),
    sumTime: document.getElementById("sumTime"),
    sumPaket: document.getElementById("sumPaket"),
    sumDuration: document.getElementById("sumDuration"),
    sumPlayers: document.getElementById("sumPlayers"),
    sumTotal: document.getElementById("sumTotal"),
    confirmBtn: document.querySelector(".confirm-btn"),
    formBooking: document.getElementById("formBooking"), // Ambil elemen form utama
  };

  // 2. State Data Lokal Pemesanan Temp
  let currentBooking = {
    date: elements.inputDate.value,
    time: "",
    packageId: "",
    packageName: "",
    duration: "",
    price: 0,
    players: "",
  };

  // 3. Fungsi Helper: Konversi Angka Menjadi Format Mata Uang Rupiah Standard Web
  const formatRupiah = (num) => {
    return new Intl.NumberFormat("id-ID", {
      style: "currency",
      currency: "IDR",
      minimumFractionDigits: 0,
    }).format(num);
  };

  // 4. Fungsi Sinkronisasi Data dari State ke Layar Ringkasan (Kanan)
  const refreshSummaryUI = () => {
    elements.sumDate.textContent = elements.inputDate.value || "-";
    elements.sumTime.textContent = currentBooking.time || "-";
    elements.sumPaket.textContent = currentBooking.packageName || "-";
    elements.sumDuration.textContent = currentBooking.duration || "-";
    elements.sumPlayers.textContent = currentBooking.players
      ? `${currentBooking.players} Orang`
      : "-";
    elements.sumTotal.textContent = formatRupiah(currentBooking.price);

    // SUNTIK DATA KE HIDDEN INPUT: Agar form HTML sinkron jika sewaktu-waktu dibaca browser
    if (document.getElementById("hiddenPaketId")) {
      document.getElementById("hiddenPaketId").value = currentBooking.packageId;
      document.getElementById("hiddenTanggal").value = elements.inputDate.value;
      document.getElementById("hiddenJam").value = currentBooking.time;
      document.getElementById("hiddenJumlahOrang").value =
        currentBooking.players;
      document.getElementById("hiddenTotalHarga").value = currentBooking.price;
    }
  };

  // 5. Fungsi Mengambil Nilai Atribut 'data-' Dari Kartu Paket yang Dipilih
  const syncDataFromCard = (card) => {
    if (!card) return;

    currentBooking.packageId = card.getAttribute("data-id") || card.dataset.id;
    currentBooking.packageName = card.dataset.name;
    currentBooking.duration = card.dataset.duration;
    currentBooking.price = parseInt(card.dataset.price) || 0;
    currentBooking.players = card.dataset.players;

    refreshSummaryUI();
  };

  // 6. Event Listener: Aksi Memilih Tombol Jam Sesi
  elements.timeBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      elements.timeBtns.forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");

      // Mengambil dari text murni atau atribut data-time yang kita buat di HTML
      currentBooking.time = (btn.dataset.time || btn.textContent).trim();
      refreshSummaryUI();
    });
  });

  // 7. Event Listener: Aksi Memilih Kartu Paket Balap
  elements.paketCards.forEach((card) => {
    card.addEventListener("click", () => {
      elements.paketCards.forEach((c) => c.classList.remove("active"));
      card.classList.add("active");
      syncDataFromCard(card);
    });
  });

  // 8. Event Listener: Mengubah Kalender Tanggal Balap
  elements.inputDate.addEventListener("change", () => {
    currentBooking.date = elements.inputDate.value;
    refreshSummaryUI();
  });

  // 9. Event Listener: Mengambil Alih Aksi Submit Form Melalui Fetch API
  if (elements.formBooking) {
    elements.formBooking.addEventListener("submit", (e) => {
      e.preventDefault(); // MENCEGAH browser reload halaman otomatis

      // Validasi awal kelengkapan data di sisi klien
      if (
        !currentBooking.time ||
        !currentBooking.packageName ||
        !currentBooking.packageId
      ) {
        alert("Harap pilih Jam dan Paket terlebih dahulu sebelum konfirmasi!");
        return;
      }

      // Kunci tombol agar user tidak melakukan klik ganda (Double Submit Bug)
      elements.confirmBtn.disabled = true;
      elements.confirmBtn.textContent = "Memproses...";

      // Bangun FormData dengan key yang sinkron 100% dengan $_POST di proses_booking.php
      const formData = new FormData();
      formData.append("tanggal_booking", elements.inputDate.value); // FIXED KEY
      formData.append("jam_booking", currentBooking.time); // FIXED KEY
      formData.append("paket_id", currentBooking.packageId);
      formData.append("jumlah_orang", currentBooking.players); // FIXED KEY
      formData.append("total_harga", currentBooking.price);

      // Tembak paket data ke backend pemroses secara asinkron (AJAX)
      fetch("proses_booking.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.text())
        .then((result) => {
          const cleanResult = result.trim();

          // Ekstrak ID angka murni yang dihasilkan oleh mysqli_insert_id
          const extractId = cleanResult.replace(/\D/g, "");

          if (extractId !== "") {
            alert("🎉 Booking Berhasil Disimpan! Silakan lakukan pembayaran.");
            // Bawa parameter ID barunya menuju halaman invoice pembayaran
            window.location.href =
              "../pembayaran/pembayaran.php?id=" + extractId;
          } else if (cleanResult.includes("error_session")) {
            alert("Sesi Anda telah berakhir. Silakan login kembali.");
            window.location.href = "../login.php";
          } else {
            alert("Gagal menyimpan transaksi.\nRespon server: " + cleanResult);
            elements.confirmBtn.disabled = false;
            elements.confirmBtn.textContent = "Konfirmasi Booking";
          }
        })
        .catch((error) => {
          console.error("Error Fetching:", error);
          alert(
            "Terjadi masalah koneksi ke server database. Coba beberapa saat lagi.",
          );
          elements.confirmBtn.disabled = false;
          elements.confirmBtn.textContent = "Konfirmasi Booking";
        });
    });
  }

  // Tambahan: Logika konfirmasi logout khusus di halaman booking
  const logoutBtn = document.querySelector(".logout-btn");
  if (logoutBtn) {
    logoutBtn.removeAttribute("onclick"); // Putus link sepihak dari HTML bawaan
    logoutBtn.addEventListener("click", (e) => {
      e.preventDefault();
      if (
        confirm(
          "Apakah Anda yakin ingin keluar dan membatalkan pemesanan gokart?",
        )
      ) {
        window.location.href = "../logout.php";
      }
    });
  }

  // 10. Inisialisasi Kondisi Awal Saat Layar Selesai Dimuat
  const activeTime = document.querySelector(".time-btn.active");
  if (activeTime)
    currentBooking.time = (
      activeTime.dataset.time || activeTime.textContent
    ).trim();

  const initialCard = document.querySelector(".paket-card.active");
  if (initialCard) {
    syncDataFromCard(initialCard);
  } else {
    refreshSummaryUI();
  }
});
