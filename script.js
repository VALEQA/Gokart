document.addEventListener("DOMContentLoaded", () => {
  // ==========================================================================
  // 1. FITUR TOMBOL SCROLL DOWN SMOOTH
  // ==========================================================================
  const scrollBtn = document.getElementById("scroll-btn");

  if (scrollBtn) {
    scrollBtn.addEventListener("click", () => {
      const targetElement = document.getElementById("specs");
      if (targetElement) {
        targetElement.scrollIntoView({
          behavior: "smooth",
          block: "start",
        });
      }
    });
  }

  // ==========================================================================
  // 2. INTERAKSI TREK BALAP (HIGHLIGHT TIKUNGAN DAN PATH)
  // ==========================================================================
  const turnLabels = document.querySelectorAll(".turn-label");
  const trackPaths = document.querySelectorAll("#track-svg path");

  turnLabels.forEach((label, index) => {
    // Efek saat kursor masuk ke label T1-T6
    label.addEventListener("mouseenter", () => {
      label.style.transform = "scale(1.3)";
      label.style.backgroundColor = "#ff5722"; // Berubah jadi warna oranye racing
      label.style.boxShadow = "0 0 15px #ff5722";

      // Beri efek kilas pada lintasan trek yang berdekatan (opsional jika index match)
      if (trackPaths[index % trackPaths.length]) {
        trackPaths[index % trackPaths.length].style.opacity = "0.8";
      }
    });

    // Efek saat kursor keluar dari label
    label.addEventListener("mouseleave", () => {
      label.style.transform = "scale(1)";
      label.style.backgroundColor = ""; // Kembali ke CSS bawaan
      label.style.boxShadow = "";

      trackPaths.forEach((path) => (path.style.opacity = "1"));
    });
  });

  // ==========================================================================
  // 3. ANIMASI FADE-IN UNTUK PLACEMENT RPM ENGINE
  // ==========================================================================
  const rpmLabels = document.querySelectorAll(".pcp-rpm");

  // Membuat teks RPM muncul bergantian dengan efek delay kosmetik
  rpmLabels.forEach((rpm, idx) => {
    rpm.style.opacity = "0";
    rpm.style.transition = "all 0.6s ease";

    // Memicu animasi setelah halaman termuat
    setTimeout(
      () => {
        rpm.style.opacity = "1";
      },
      300 * (idx + 1),
    );
  });
});
