document.addEventListener('DOMContentLoaded', () => {
    const notify = (type, message) => {
        if (window.toastr && typeof window.toastr[type] === 'function') {
            window.toastr.options = {
                closeButton: true,
                newestOnTop: true,
                progressBar: true,
                positionClass: 'toast-top-right',
                timeOut: 4200,
                extendedTimeOut: 1000,
                toastClass: 'toastr-custom toast',
            };
            window.toastr[type](message);
            return;
        }

        const fallback = document.createElement('div');
        fallback.className = 'fixed right-4 top-4 z-[100] rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white shadow-xl';
        fallback.textContent = message;
        document.body.appendChild(fallback);
        window.setTimeout(() => fallback.remove(), 4200);
    };

    window.BudgetUI = { notify };

    const startDate = document.querySelector('#date_debut');
    const endDate = document.querySelector('#date_fin');

    if (startDate && endDate) {
        const syncEndDateMinimum = () => {
            if (!startDate.value) {
                endDate.removeAttribute('min');
                return;
            }

            const [year, month, day] = startDate.value.split('-').map(Number);
            const nextDay = new Date(Date.UTC(year, month - 1, day + 1));
            const nextDayValue = [
                nextDay.getUTCFullYear(),
                String(nextDay.getUTCMonth() + 1).padStart(2, '0'),
                String(nextDay.getUTCDate()).padStart(2, '0'),
            ].join('-');

            endDate.min = nextDayValue;
        };

        startDate.addEventListener('change', syncEndDateMinimum);
        syncEndDateMinimum();
    }

    document.querySelectorAll('[data-delete-budget]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();

            const period = form.dataset.deleteBudget;

            window.Swal.fire({
                title: 'Supprimer ce budget ?',
                text: `Le budget « ${period} » sera définitivement supprimé.`,
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
