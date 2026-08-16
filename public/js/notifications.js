document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-delete-notification], [data-delete-notifications]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            window.Swal.fire({ title: 'Supprimer ?', text: 'Cette action est irréversible.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Supprimer', cancelButtonText: 'Annuler' }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });
    });
});
