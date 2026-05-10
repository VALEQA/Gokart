document.addEventListener("DOMContentLoaded", () => {
    // 1. Definisikan Elemen UI
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
        confirmBtn: document.querySelector(".confirm-btn")
    };

    // 2. State Data Booking (Sesuai kolom database di image_9de4fc.png)
    let currentBooking = {
        date: elements.inputDate.value,
        time: "",
        packageId: "", // Untuk kolom paket_id
        packageName: "",
        duration: "",
        price: 0,
        players: "" // Untuk kolom jumlah_orang
    };

    // 3. Fungsi Helper: Format Rupiah
    const formatRupiah = (num) => {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0
        }).format(num);
    };

    // 4. Fungsi Render Ringkasan ke UI
    const refreshSummaryUI = () => {
        elements.sumDate.textContent = elements.inputDate.value;
        elements.sumTime.textContent = currentBooking.time || "-";
        elements.sumPaket.textContent = currentBooking.packageName || "-";
        elements.sumDuration.textContent = currentBooking.duration || "-";
        elements.sumPlayers.textContent = currentBooking.players ? `${currentBooking.players} Orang` : "-";
        elements.sumTotal.textContent = formatRupiah(currentBooking.price);
    };

    // 5. Fungsi Sinkronisasi Data dari Kartu Paket
    const syncDataFromCard = (card) => {
        if (!card) return;
        
        // Ambil data dari atribut 'data-' di HTML
        currentBooking.packageId = card.dataset.id || "1"; // Pastikan di PHP ada data-id
        currentBooking.packageName = card.dataset.name;
        currentBooking.duration = card.dataset.duration;
        currentBooking.price = parseInt(card.dataset.price);
        currentBooking.players = card.dataset.players; // Mengambil kapasitas (Keluarga = 4)
        
        refreshSummaryUI();
    };

    // 6. Event Listener: Pilih Jam
    elements.timeBtns.forEach((btn) => {
        btn.addEventListener("click", () => {
            elements.timeBtns.forEach((b) => b.classList.remove("active"));
            btn.classList.add("active");
            currentBooking.time = btn.textContent.trim();
            refreshSummaryUI();
        });
    });

    // 7. Event Listener: Pilih Paket
    elements.paketCards.forEach((card) => {
        card.addEventListener("click", () => {
            elements.paketCards.forEach((c) => c.classList.remove("active"));
            card.classList.add("active");
            syncDataFromCard(card);
        });
    });

    // 8. Event Listener: Ubah Tanggal
    elements.inputDate.addEventListener("change", () => {
        currentBooking.date = elements.inputDate.value;
        refreshSummaryUI();
    });

    // 9. Event Listener: Tombol Konfirmasi (Simpan ke Database)
    elements.confirmBtn.addEventListener("click", () => {
        // Validasi pilihan
        if (!currentBooking.time || !currentBooking.packageName) {
            alert("Harap pilih Jam dan Paket terlebih dahulu!");
            return;
        }

        // Persiapkan data untuk dikirim ke proses_booking.php
        const formData = new FormData();
        formData.append('tanggal', elements.inputDate.value);
        formData.append('jam', currentBooking.time);
        formData.append('paket_id', currentBooking.packageId);
        formData.append('kapasitas', currentBooking.players);
        formData.append('total_harga', currentBooking.price);

        // Kirim data menggunakan Fetch API
        fetch('proses_booking.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(result => {
            if (result.trim() === "success") {
                alert("Booking Berhasil Disimpan!");
                window.location.href = "../riwayat booking/riwayat.php";
            } else {
                alert("Gagal menyimpan booking: " + result);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Terjadi kesalahan koneksi ke server.");
        });
    });

    // 10. Inisialisasi Awal saat Halaman Dimuat
    // Set jam aktif awal
    const activeTime = document.querySelector(".time-btn.active");
    if (activeTime) currentBooking.time = activeTime.textContent.trim();

    // Set paket aktif awal
    const initialCard = document.querySelector(".paket-card.active");
    if (initialCard) {
        syncDataFromCard(initialCard);
    } else {
        refreshSummaryUI();
    }
});