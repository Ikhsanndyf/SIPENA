// Menandai form yang sudah dikonfirmasi agar submit berikutnya tidak dicegat.
const confirmedForms = new WeakSet();

const hasSweetAlert = () => typeof window.Swal?.fire === 'function';

// Menampilkan session flash sebagai toast profesional.
const showFlashMessage = () => {
    const flashElement = document.querySelector('[data-flash-message]');

    if (!flashElement || !hasSweetAlert()) {
        return;
    }

    const type = flashElement.dataset.flashType;
    const icon = type === 'status' ? 'info' : type;
    const title = {
        error: 'Proses gagal',
        warning: 'Perlu perhatian',
        status: 'Informasi',
        success: 'Berhasil',
    }[type] ?? 'Informasi';

    window.Swal.fire({
        icon,
        title,
        text: flashElement.textContent.trim(),
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true,
    });

    // Notifikasi HTML tidak perlu ditampilkan kembali setelah toast aktif.
    flashElement.remove();
};

// Mengganti konfirmasi browser pada aksi penting seperti hapus dan penyelesaian.
const registerConfirmationDialogs = () => {
    document.addEventListener('submit', async (event) => {
        const form = event.target.closest('form[data-confirm]');

        if (!form || confirmedForms.has(form)) {
            return;
        }

        event.preventDefault();

        const message = form.dataset.confirm;
        let isConfirmed;

        if (hasSweetAlert()) {
            const result = await window.Swal.fire({
                icon: form.dataset.confirmIcon ?? 'question',
                title: form.dataset.confirmTitle ?? 'Konfirmasi tindakan',
                text: message,
                showCancelButton: true,
                confirmButtonText: form.dataset.confirmButton ?? 'Ya, lanjutkan',
                cancelButtonText: 'Batal',
                confirmButtonColor: form.dataset.confirmColor ?? '#4f46e5',
                cancelButtonColor: '#64748b',
                reverseButtons: true,
                focusCancel: true,
            });

            isConfirmed = result.isConfirmed;
        } else {
            // Konfirmasi browser menjaga fungsi tetap aman jika CDN tidak tersedia.
            isConfirmed = window.confirm(message);
        }

        if (isConfirmed) {
            confirmedForms.add(form);

            if (event.submitter) {
                form.requestSubmit(event.submitter);
            } else {
                form.requestSubmit();
            }
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    showFlashMessage();
    registerConfirmationDialogs();
});
