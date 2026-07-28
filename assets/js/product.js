// Delete product AJAX
function deleteProduct(button) {
    if (!confirm("Delete this product?")) return;

    var row = $(button).closest("tr");
    var productId = row.data("id");

    $.post("../db/product_requests.php", { delete_product: 1, product_id: productId })
        .done(function(response) {
            if (response.trim() === "success") {
                row.remove(); // update the table without reloading the page
            } else {
                alert("Error deleting product.");
            }
        })
        .fail(function() {
            alert("Error deleting product.");
        });
}

// Edit product AJAX
//TODO: CURRENTLY ONLY USING PROMPT() FOR INOUT, CHANGE TO MODAL FORM UI 
function editProduct(button) {
    var row = $(button).closest("tr");
    var productId = row.data("id");

    var currentName = row.find(".cell-name").text();
    var currentPrice = row.find(".cell-price").text();
    var currentQuantity = row.find(".cell-quantity").text();
    var currentReorder = row.find(".cell-reorder").text();
    var currentExpiration = row.find(".cell-expiration").text();

    var newName = prompt("Product name:", currentName);
    if (newName === null) return; // cancelled

    var newPrice = prompt("Price:", currentPrice.replace('₱', '').trim());
    if (newPrice === null) return;

    var newQuantity = prompt("Quantity:", currentQuantity);
    if (newQuantity === null) return;

    var newReorder = prompt("Reorder level:", currentReorder);
    if (newReorder === null) return;

    var newExpiration = prompt("Expiration date (YYYY-MM-DD):", currentExpiration);
    if (newExpiration === null) return;

    var data = {
        edit_product: 1,
        product_id: productId,
        product_name: newName,
        price: newPrice,
        quantity: newQuantity,
        reorder_level: newReorder,
        expiration_date: newExpiration
    };

    $.post("../db/product_requests.php", data)
        .done(function(response) {
            if (response.trim() === "success") {
                // update the row's text directly instead of reloading the page
                row.find(".cell-name").text(newName);
                row.find(".cell-price").text("₱" + parseFloat(newPrice).toFixed(2));
                row.find(".cell-quantity").text(newQuantity);
                row.find(".cell-reorder").text(newReorder);
                row.find(".cell-expiration").text(newExpiration);
            } else {
                alert("Error updating product.");
            }
        })
        .fail(function() {
            alert("Error updating product.");
        });
}