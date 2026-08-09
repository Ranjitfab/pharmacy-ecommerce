
// TOAST NOTIFICATION LOGIC

function showAdminToast(message, type) {
    if ($("#toast-container").length === 0) return;

    var toastId = "toast-" + Date.now() + "-" + Math.floor(Math.random() * 1000);
    var toastHtml = '<div class="toast toast-' + type + '" id="' + toastId + '">' + message + '</div>';

    $("#toast-container").append(toastHtml);

    var toastEl = $("#" + toastId);
    setTimeout(function () { toastEl.addClass("show"); }, 10);

    setTimeout(function () {
        toastEl.removeClass("show");
        setTimeout(function () { toastEl.remove(); }, 300);
    }, 6000);
}

// Track which alerts have already been shown, so re-polling doesn't re-notify about the same low-stock/expiring item
var seenAlerts = new Set(typeof initialAlertKeys !== "undefined" ? initialAlertKeys : []);

function checkForNewAlerts() {
    $.get("../db/notification_requests.php")
        .done(function (response) {
            var result = typeof response === "string" ? JSON.parse(response) : response;
            var notifications = Array.isArray(result.notifications) ? result.notifications : [];

            notifications.forEach(function (alert) {
                var key = alert.type + "|" + alert.product;
                if (seenAlerts.has(key)) return; // already shown, skip

                seenAlerts.add(key);
                showAdminToast(alert.message, alert.type);

                if ("Notification" in window && Notification.permission === "granted") {
                    new Notification("RxStock Admin Alert", { body: alert.message });
                }
            });
        })
        .fail(function (err) {
            console.error("Unable to load notifications:", err);
        });
}

// Browser notification permission button

$(document).ready(function () {
    var enableBtn = $("#enable-notifications-btn");

    if (!("Notification" in window)) {
        enableBtn.hide();
    } else if (Notification.permission === "granted") {
        enableBtn.text("Notifications Enabled").prop("disabled", true);
    }

    enableBtn.on("click", function () {
        Notification.requestPermission().then(function (permission) {
            if (permission === "granted") {
                enableBtn.text("Notifications Enabled").prop("disabled", true);
                showAdminToast("Browser notifications enabled.", "low_stock");
            }
        });
    });

    // Poll every 30 seconds
    setInterval(checkForNewAlerts, 30000);
});