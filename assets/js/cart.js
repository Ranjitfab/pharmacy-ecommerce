// Add to cart from shop.php
function addToCart(productId) {
    var xhttp = new XMLHttpRequest();
    xhttp.open("POST", "../db/cart_requests.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            if (this.responseText.trim() === "success") {
                alert("Added to cart!");
            } else {
                alert("Error adding to cart.");
            }
        }
    };

    xhttp.send("add_to_cart=1&product_id=" + encodeURIComponent(productId) + "&quantity=1");
}

// Change quantity using +/- by reading the quantity row
function changeQuantity(button, delta) {
    var row = button.closest("tr");
    var cartItemId = row.dataset.cartItemId;
    var quantitySpan = row.querySelector(".cell-quantity");
    var currentQty = parseInt(quantitySpan.textContent);
    var newQty = currentQty + delta;

    if (newQty <= 0 && !confirm("Remove this item from your cart?")) {
        return;
    }

    var xhttp = new XMLHttpRequest();
    xhttp.open("POST", "../db/cart_requests.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            var result = this.responseText.trim();

            if (result === "removed") {
                row.remove();
                recalculateTotal();
            } else if (result === "success") {
                quantitySpan.textContent = newQty;

                var price = parsePrice(row.querySelector(".cell-price").textContent);
                var newSubtotal = price * newQty;
                row.querySelector(".cell-subtotal").textContent = "₱" + newSubtotal.toFixed(2);

                recalculateTotal();
            } else {
                alert("Error updating quantity.");
            }
        }
    };

    xhttp.send("update_quantity=1&cart_item_id=" + encodeURIComponent(cartItemId) + "&quantity=" + newQty);
}


// Remove item button on cart.php)
function removeItem(button) {
    if (!confirm("Remove this item from your cart?")) return;

    var row = button.closest("tr");
    var cartItemId = row.dataset.cartItemId;

    var xhttp = new XMLHttpRequest();
    xhttp.open("POST", "../db/cart_requests.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            if (this.responseText.trim() === "success") {
                row.remove();
                recalculateTotal();
            } else {
                alert("Error removing item.");
            }
        }
    };

    xhttp.send("remove_item=1&cart_item_id=" + encodeURIComponent(cartItemId));
}

// ------------------------------------------------------------
// Helpers
// ------------------------------------------------------------
function parsePrice(text) {
    // Strips the ₱ symbol and any thousands-separator commas
    return parseFloat(text.replace("₱", "").replace(/,/g, ""));
}

function recalculateTotal() {
    var subtotalCells = document.querySelectorAll("#cart-table .cell-subtotal");
    var total = 0;

    subtotalCells.forEach(function (cell) {
        total += parsePrice(cell.textContent);
    });

    var totalEl = document.getElementById("cart-total");
    if (totalEl) {
        totalEl.textContent = total.toFixed(2);
    }

    // If the cart is now empty, the page will reload
    if (subtotalCells.length === 0) {
        location.reload();
    }
}