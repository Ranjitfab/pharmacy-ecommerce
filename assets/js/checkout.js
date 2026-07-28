function placeOrder() {
    var button = $("#place-order-btn");
    var messageBox = $("#checkout-message");

    button.prop("disabled", true);
    button.text("Placing order...");

    $.post("../db/order_requests.php", { checkout: 1 })
        .done(function(response) {
            var result = response.trim();

            if (result.startsWith("success")) {
                var orderId = result.split(":")[1];
                messageBox.html("<p style='color:green;'>Order #" + orderId + " placed successfully!</p>");
                setTimeout(function () {
                    location.href = "shop.php";
                }, 2000);
            } else if (result === "empty_cart") {
                messageBox.html("<p style='color:red;'>Your cart is empty.</p>");
                button.prop("disabled", false);
                button.text("Place Order");
            } else if (result.startsWith("insufficient_stock")) {
                var productName = result.split(":")[1];
                messageBox.html("<p style='color:red;'>Not enough stock for: " + productName + "</p>");
                button.prop("disabled", false);
                button.text("Place Order");
            } else {
                messageBox.html("<p style='color:red;'>Something went wrong. Please try again.</p>");
                button.prop("disabled", false);
                button.text("Place Order");
            }
        })
        .fail(function() {
            messageBox.html("<p style='color:red;'>Something went wrong. Please try again.</p>");
            button.prop("disabled", false);
            button.text("Place Order");
        });
}