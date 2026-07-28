// ------------------------------------------------------------
// DRAWER TOGGLE LOGIC
// ------------------------------------------------------------
function openCartDrawer() {
    $("#drawer-overlay").addClass("open");
    $("#cart-drawer").addClass("open");
    refreshCartDrawer();
}

function closeCartDrawer() {
    $("#drawer-overlay").removeClass("open");
    $("#cart-drawer").removeClass("open");
}

function refreshCartDrawer() {
    if ($("#drawer-body").length === 0) return; // Not on a page with a drawer

    $.post("../db/cart_requests.php", { fetch_cart: 1 })
        .done(function(response) {
            try {
                var data = JSON.parse(response);
                $("#drawer-body").html(data.html);
                $("#drawer-subtotal").text(data.total);
                $("#cart-badge").text(data.count);
            } catch (e) {
                console.error("Error parsing cart data", e);
            }
        });
}

// ------------------------------------------------------------
// TOAST NOTIFICATION LOGIC
// ------------------------------------------------------------
function showToast(message) {
    if ($("#toast-container").length === 0) return;
    
    var toastId = "toast-" + Date.now();
    var toastHtml = '<div class="toast" id="' + toastId + '"><i class="fa-solid fa-circle-check"></i> ' + message + '</div>';
    
    $("#toast-container").append(toastHtml);
    
    var toastEl = $("#" + toastId);
    setTimeout(function() {
        toastEl.addClass("show");
    }, 10);
    
    setTimeout(function() {
        toastEl.removeClass("show");
        setTimeout(function() {
            toastEl.remove();
        }, 300);
    }, 3000);
}

// ------------------------------------------------------------
// ADD TO CART - called from shop.php's product cards
// ------------------------------------------------------------
function addToCart(productId) {
    $.post("../db/cart_requests.php", { add_to_cart: 1, product_id: productId, quantity: 1 })
        .done(function(response) {
            if (response.trim() === "success") {
                if ($("#cart-drawer").length > 0) {
                    refreshCartDrawer(); // Refresh in background
                    showToast("Item added to cart!");
                } else {
                    alert("Added to cart!");
                }
            } else {
                alert("Error adding to cart.");
            }
        })
        .fail(function() {
            alert("Error adding to cart.");
        });
}

// ------------------------------------------------------------
// CHANGE QUANTITY (+/- buttons)
// ------------------------------------------------------------
function changeQuantity(button, delta) {
    // Check if we are inside the drawer or the standalone cart table
    var isDrawer = $(button).closest(".drawer-item").length > 0;
    var row = $(button).closest("tr, .drawer-item");
    var cartItemId = row.data("cart-item-id");
    
    var quantitySpan = isDrawer ? row.find("span").first() : row.find(".cell-quantity");
    var currentQty = parseInt(quantitySpan.text());
    var newQty = currentQty + delta;

    if (newQty <= 0 && !confirm("Remove this item from your cart?")) {
        return;
    }

    $.post("../db/cart_requests.php", { update_quantity: 1, cart_item_id: cartItemId, quantity: newQty })
        .done(function(response) {
            var result = response.trim();
            if (result === "removed" || result === "success") {
                if (isDrawer) {
                    refreshCartDrawer();
                } else {
                    // Update legacy cart.php table
                    if (result === "removed") {
                        row.remove();
                    } else {
                        quantitySpan.text(newQty);
                        var price = parsePrice(row.find(".cell-price").text());
                        var newSubtotal = price * newQty;
                        row.find(".cell-subtotal").text("₱" + newSubtotal.toFixed(2));
                    }
                    recalculateTotal();
                }
            } else {
                alert("Error updating quantity.");
            }
        })
        .fail(function() {
            alert("Error updating quantity.");
        });
}

// ------------------------------------------------------------
// REMOVE ITEM (Remove button on cart.php)
// ------------------------------------------------------------
function removeItem(button) {
    if (!confirm("Remove this item from your cart?")) return;

    var row = $(button).closest("tr");
    var cartItemId = row.data("cart-item-id");

    $.post("../db/cart_requests.php", { remove_item: 1, cart_item_id: cartItemId })
        .done(function(response) {
            if (response.trim() === "success") {
                row.remove();
                recalculateTotal();
            } else {
                alert("Error removing item.");
            }
        })
        .fail(function() {
            alert("Error removing item.");
        });
}

// ------------------------------------------------------------
// Helpers for legacy cart.php
// ------------------------------------------------------------
function parsePrice(text) {
    return parseFloat(text.replace("₱", "").replace(/,/g, ""));
}

function recalculateTotal() {
    var subtotalCells = $("#cart-table .cell-subtotal");
    var total = 0;

    subtotalCells.each(function () {
        total += parsePrice($(this).text());
    });

    var totalEl = $("#cart-total");
    if (totalEl.length) {
        totalEl.text(total.toFixed(2));
    }

    if (subtotalCells.length === 0) {
        location.reload();
    }
}