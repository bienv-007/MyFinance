document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-delete-notification], [data-delete-notifications]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            window.Swal.fire({ title: 'Supprimer ?', text: 'Cette action est irréversible.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Supprimer', cancelButtonText: 'Annuler' }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });
    });

    window.markNotificationRead = async (id, button) => {
        try {
            const response = await fetch(`/api/notifications/${id}/read`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            });
            if (response.ok) {
                const badge = button.closest('article')?.querySelector('.rounded-full.bg-indigo-100');
                if (badge) badge.remove();
                if (window.toastr) toastr.success('Notification marquée comme lue.');
            }
        } catch (_) {
            if (window.toastr) toastr.error('Impossible de marquer la notification.');
        }
    };

    const center = document.querySelector('[data-notification-center]');

    if (!center || !window.fetch) {
        return;
    }

    const list = center.querySelector('[data-notification-list]');
    const headerCount = center.querySelector('[data-header-notification-count]');
    const sidebarCount = document.querySelector('[data-sidebar-notification-count]');
    const allRead = center.querySelector('[data-notifications-all-read]');
    const knownIds = new Set([...list.querySelectorAll('[data-notification-id]')].map((item) => item.dataset.notificationId));
    let firstRequest = true;
    let isRequestInFlight = false;
    let preferences = { notif_son: true, notif_vibration: true, notif_navigateur: false };
    const notifAudio = new Audio('/sounds/notification.wav');

    const loadPreferences = async () => {
        try {
            const response = await fetch('/api/notification-preferences', {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });
            if (response.ok) {
                const payload = await response.json();
                if (payload.data) preferences = payload.data;
            }
        } catch (_) {}
    };

    const playNotifEffect = (notification) => {
        if (preferences.notif_son) {
            notifAudio.currentTime = 0;
            notifAudio.play().catch(() => {});
        }
        if (preferences.notif_vibration && navigator.vibrate) {
            navigator.vibrate(200);
        }
        if (preferences.notif_navigateur && 'Notification' in window && Notification.permission === 'granted') {
            new Notification(notification.titre, { body: notification.contenu, icon: '/favicon.ico' });
        }
    };

    loadPreferences();
    window.setInterval(loadPreferences, 60000);

    const updateCount = (count, hasNotifications) => {
        [headerCount, sidebarCount].filter(Boolean).forEach((badge) => {
            badge.textContent = count;
            badge.classList.toggle('hidden', count === 0);
        });
        allRead.classList.toggle('hidden', count > 0 || !hasNotifications);
    };

    const createItem = (notification) => {
        const link = document.createElement('a');
        link.dataset.notificationId = notification.id_notification;
        link.href = `${center.dataset.notificationShowUrl}/${notification.id_notification}`;
        link.className = `block border-b border-slate-100 px-4 py-3 hover:bg-slate-50${notification.est_lue ? '' : ' bg-indigo-50/50'}`;

        const title = document.createElement('p');
        title.className = 'truncate text-sm font-semibold text-slate-800';
        title.textContent = notification.titre;
        const content = document.createElement('p');
        content.className = 'mt-1 truncate text-xs text-slate-500';
        content.textContent = notification.contenu;
        link.append(title, content);

        return link;
    };

    const refreshNotifications = async () => {
        if (document.hidden || isRequestInFlight) return;

        isRequestInFlight = true;
        try {
            const response = await fetch(center.dataset.notificationsUrl, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });
            if (!response.ok) return;

            const payload = await response.json();
            const notifications = payload.data || [];
            const freshNotifications = notifications.filter((notification) => !knownIds.has(String(notification.id_notification)));

            list.replaceChildren();
            if (notifications.length) {
                notifications.slice(0, 5).forEach((notification) => list.append(createItem(notification)));
            } else {
                const empty = document.createElement('p');
                empty.className = 'px-4 py-8 text-center text-sm text-slate-500';
                empty.textContent = 'Aucune notification.';
                list.append(empty);
            }
            notifications.forEach((notification) => knownIds.add(String(notification.id_notification)));
            updateCount(Number(payload.unread_count || 0), notifications.length > 0);

            if (!firstRequest && freshNotifications.length && window.BudgetUI?.notify) {
                freshNotifications.reverse().forEach((notification) => {
                    window.BudgetUI.notify('info', notification.titre);
                    playNotifEffect(notification);
                });
            }
        } catch (_) {
            // La prochaine vérification relancera la synchronisation si le réseau est momentanément indisponible.
        } finally {
            firstRequest = false;
            isRequestInFlight = false;
        }
    };

    refreshNotifications();
    window.setInterval(refreshNotifications, 15000);
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) refreshNotifications();
    });
});
