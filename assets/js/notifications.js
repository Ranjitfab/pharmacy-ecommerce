document.addEventListener('DOMContentLoaded', async function () {
    const notificationList = document.getElementById('admin-notification-list');
    if (!notificationList) {
        return;
    }

    try {
        const response = await fetch('../db/notification_requests.php');
        const result = await response.json();
        const notifications = Array.isArray(result.notifications) ? result.notifications : [];

        if (!notifications.length) {
            return;
        }

        const summary = notifications.map((item) => item.message).join('\n');

        if ('Notification' in window) {
            if (Notification.permission === 'granted') {
                new Notification('RxStock Admin Alert', {
                    body: summary,
                });
            } else if (Notification.permission !== 'denied') {
                Notification.requestPermission().then((permission) => {
                    if (permission === 'granted') {
                        new Notification('RxStock Admin Alert', {
                            body: summary,
                        });
                    }
                });
            }
        }

        window.alert('Admin alert:\n' + summary);
    } catch (error) {
        console.error('Unable to load notifications:', error);
    }
});
