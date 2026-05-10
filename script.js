document.addEventListener('DOMContentLoaded', () => {
    
    // ==========================================
    // 1. FITUR TOMBOL SCROLL DOWN
    // ==========================================
    const scrollBtn = document.getElementById('scroll-btn');
    if (scrollBtn) {
        scrollBtn.addEventListener('click', () => {
            const targetElement = document.getElementById('specs');
            if (targetElement) {
                targetElement.scrollIntoView({ behavior: 'smooth' });
            }
        });
    }
// ==========================================
    // FITUR GENERATOR LISENSI BALAP (VERSI PRO: UPLOAD & SAVE)
    // ==========================================
    const generateBtn = document.getElementById('generate-btn');
    const saveBtn = document.getElementById('save-btn');
    const photoInput = document.getElementById('driver-photo');
    let uploadedImageURL = ''; // Variabel penyimpan foto sementara

    if (generateBtn) {
        // 1. Logika saat foto dipilih dari HP/Laptop
        photoInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                // Ubah file gambar jadi URL lokal sementara
                uploadedImageURL = URL.createObjectURL(file);
                // Ubah teks tombol biar pengguna tahu foto sudah masuk
                document.getElementById('file-label-text').innerText = '✅ Foto Berhasil Dimasukkan!';
                document.getElementById('file-label-text').style.borderColor = '#00e676';
            }
        });

        // 2. Logika saat tombol "CETAK LISENSI" ditekan
        generateBtn.addEventListener('click', () => {
            const nameInput = document.getElementById('driver-name').value;
            const nickInput = document.getElementById('driver-nickname').value;
            let numInput = document.getElementById('driver-number').value;

            if (nameInput.trim() === '') {
                alert("Pit Stop! Isi dulu Namamu sebelum bikin lisensi!");
                return;
            }

            if (numInput > 99) numInput = 99;
            if (numInput < 0) numInput = 0;

            document.getElementById('card-name').innerText = nameInput.toUpperCase();
            document.getElementById('card-nickname').innerText = nickInput ? `"${nickInput.toUpperCase()}"` : '"THE ROOKIE"';
            document.getElementById('card-number').innerText = numInput ? numInput : '99';

            document.getElementById('card-signature').innerText = nameInput; // Tanda tangan sesuai nama
            document.getElementById('card-license-no').innerText = "FT-" + Math.floor(1000 + Math.random() * 9000) + "-X"; // Bikin nomor acak

            // Pasang foto pengguna ke dalam ID Card
            const cardPhoto = document.getElementById('card-photo');
            if (uploadedImageURL !== '') {
                cardPhoto.style.backgroundImage = `url(${uploadedImageURL})`;
                cardPhoto.innerHTML = ''; // Hapus bendera 🏁
            } else {
                cardPhoto.style.backgroundImage = 'none';
                cardPhoto.innerHTML = '🏁'; // Kembalikan bendera kalau nggak ada foto
            }

            // Munculkan hasil akhirnya
            const idCardWrapper = document.getElementById('id-card-wrapper');
            idCardWrapper.style.display = 'block';
            
            const idCard = document.getElementById('id-card-result');
            idCard.style.transform = 'scale(0.5)';
            setTimeout(() => { idCard.style.transform = 'scale(1)'; }, 50);
        });

        // 3. Logika untuk "SAVE/DOWNLOAD" gambar menggunakan html2canvas
        saveBtn.addEventListener('click', () => {
            const cardElement = document.getElementById('id-card-result');
            const originalText = saveBtn.innerText;
            
            saveBtn.innerText = "⏳ MEMOTRET KARTU..."; // Animasi teks loading
            
            // Proses "memotret" HTML menjadi gambar HD (scale: 2)
            html2canvas(cardElement, {
                scale: 2, 
                backgroundColor: null // Background transparan
            }).then(canvas => {
                // Buat link download palsu, lalu di-klik otomatis
                const link = document.createElement('a');
                link.download = `Lisensi_Balap_${document.getElementById('card-name').innerText}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click(); 
                
                // Kembalikan teks tombol
                saveBtn.innerText = "✅ BERHASIL DISIMPAN!";
                setTimeout(() => { saveBtn.innerText = originalText; }, 3000);
            });
        });
    }
});