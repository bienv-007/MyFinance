document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-receive-revenu-prevision]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();

            const source = form.dataset.receiveRevenuPrevision;

            window.Swal.fire({
                title: 'Marquer ce revenu comme perçu ?',
                text: `Le revenu « ${source} » sera enregistré dans vos revenus réels.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Oui, enregistrer le revenu',
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

    document.querySelectorAll('[data-delete-revenu-prevision]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();

            const source = form.dataset.deleteRevenuPrevision;

            window.Swal.fire({
                title: 'Supprimer cette prévision ?',
                text: `La prévision « ${source} » sera définitivement supprimée.`,
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
