// resources/js/admin/profile.js

function bukaModalHapus() {
    document.getElementById('modal-hapus').classList.add('open');
}

function tutupModalHapus() {
    document.getElementById('modal-hapus').classList.remove('open');
}

// Tutup modal saat klik di luar box
document.getElementById('modal-hapus')?.addEventListener('click', function (e) {
    if (e.target === this) tutupModalHapus();
});