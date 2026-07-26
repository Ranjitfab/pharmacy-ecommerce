function placeOrder() {
    var button = document.getElementById("place-order-btn");
    var messageBox = document.getElementById("checkout-message");

    button.disabled = true; // prevent double-clicks from creating duplicate orders
    button.textContent = "Placing order...";

    var xhttp = new XMLHttpRequest();
    xhttp.open("POST", "../db/order_requests.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            var result = this.responseText.trim();

            if (result.startsWith("success")) {
                var orderId = result.split(":")[1];
                messageBox.innerHTML = "<p style='color:green;'>Order #" + orderId + " placed successfully!</p>";
                setTimeout(function () {
                    location.href = "shop.php";
                }, 2000);
            } else if (result === "empty_cart") {
                messageBox.innerHTML = "<p style='color:red;'>Your cart is empty.</p>";
                button.disabled = false;
                button.textContent = "Place Order";
            } else if (result.startsWith("insufficient_stock")) {
                var productName = result.split(":")[1];
                messageBox.innerHTML = "<p style='color:red;'>Not enough stock for: " + productName + "</p>";
                button.disabled = false;
                button.textContent = "Place Order";
            } else {
                messageBox.innerHTML = "<p style='color:red;'>Something went wrong. Please try again.</p>";
                button.disabled = false;
                button.textContent = "Place Order";
            }
        }
    };

    xhttp.send("checkout=1");
}