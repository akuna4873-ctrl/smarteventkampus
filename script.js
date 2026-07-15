
// SMART EVENT CAMPUS — MAIN SCRIPT

// Toggle sidebar di tampilan mobile (dashboard)
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar) sidebar.classList.toggle('open');
}

// Konfirmasi sebelum menghapus data event
function confirmDelete(nama) {
    return confirm('Yakin ingin menghapus event "' + nama + '"? Data yang dihapus tidak bisa dikembalikan.');
}

// Filter kartu event di halaman utama berdasarkan kategori
document.addEventListener('DOMContentLoaded', function () {
    const chips = document.querySelectorAll('.filter-chip');
    const cards = document.querySelectorAll('.event-card');

    chips.forEach(function (chip) {
        chip.addEventListener('click', function () {
            chips.forEach(c => c.classList.remove('active'));
            chip.classList.add('active');

            const kategori = chip.getAttribute('data-kategori');

            cards.forEach(function (card) {
                if (kategori === 'semua' || card.getAttribute('data-kategori') === kategori) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // Pencarian sederhana di tabel dashboard
    const searchInput = document.getElementById('searchTable');
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const keyword = searchInput.value.toLowerCase();
            document.querySelectorAll('table.data-table tbody tr').forEach(function (row) {
                row.style.display = row.innerText.toLowerCase().includes(keyword) ? '' : 'none';
            });
        });
    }

    // Preview gambar sebelum upload
    const imgInput = document.getElementById('gambarInput');
    const imgPreview = document.getElementById('imgPreview');
    if (imgInput && imgPreview) {
        imgInput.addEventListener('change', function () {
            if (imgInput.files && imgInput.files[0]) {
                const reader = new FileReader();
                reader.onload = e => imgPreview.src = e.target.result;
                reader.readAsDataURL(imgInput.files[0]);
                imgPreview.style.display = 'block';
            }
        });
    }
});
