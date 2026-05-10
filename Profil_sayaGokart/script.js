document.addEventListener("DOMContentLoaded", function () {

    const editBtn = document.querySelector(".profile-info .btn.red"); // tombol edit
    const inputs = document.querySelectorAll(".card input"); // semua input

    const nameDisplay = document.querySelector(".profile-info h3"); // nama atas
    const emailDisplay = document.querySelector(".profile-info p"); // email atas

    let isEdit = false; // status mode

    editBtn.addEventListener("click", function () {

        if (!isEdit) { 
            // MODE EDIT
            inputs.forEach(input => {
                input.removeAttribute("readonly"); // aktifkan input
                input.style.background = "#fff";
            });

            editBtn.textContent = "Simpan"; // ubah tombol
            isEdit = true; // ubah status

        } else {
            // MODE SIMPAN
            inputs.forEach(input => {
                input.setAttribute("readonly", true); // kunci input
                input.style.background = "#f9f9f9";
            });

            // update tampilan atas
            nameDisplay.textContent = inputs[0].value;
            emailDisplay.textContent = inputs[1].value;

            editBtn.textContent = "Edit Profil"; // reset tombol
            isEdit = false; // reset status

            alert("Profil berhasil diperbarui!");
        }

        // animasi klik
        editBtn.style.transform = "scale(0.9)";
        setTimeout(() => {
            editBtn.style.transform = "scale(1)";
        }, 150);
    });

});
