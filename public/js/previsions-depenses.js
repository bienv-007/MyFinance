document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-validate-prevision]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();

            const description = form.dataset.validatePrevision;

            window.Swal.fire({
                title: 'Valider cette prévision ?',
                text: `Une dépense sera enregistrée pour « ${description} » et la prévision sera clôturée.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Oui, enregistrer la dépense',
                cancelButtonText: 'Annuler',
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white',
                    cancelButton: 'mr-3 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600',
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    document.querySelectorAll('[data-delete-prevision]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();

            const description = form.dataset.deletePrevision;

            window.Swal.fire({
                title: 'Supprimer cette prévision ?',
                text: `La prévision « ${description} » sera définitivement supprimée.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white',
                    cancelButton: 'mr-3 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600',
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
