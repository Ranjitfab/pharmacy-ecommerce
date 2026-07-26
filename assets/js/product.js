// Delete product AJAX
function deleteProduct(button) {
    if (!confirm("Delete this product?")) return;

    var row = button.closest("tr");
    var productId = row.dataset.id;

    var xhttp = new XMLHttpRequest();
    xhttp.open("POST", "../db/product_requests.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            if (this.responseText.trim() === "success") {
                row.remove(); // update the table without reloading the page
            } else {
                alert("Error deleting product.");
            }
        }
    };

    xhttp.send("delete_product=1&product_id=" + encodeURIComponent(productId));
}

// Edit product AJAX
//TODO: CURRENTLY ONLY USING PROMPT() FOR INOUT, CHANGE TO MODAL FORM UI 
function editProduct(button) {
    var row = button.closest("tr");
    var productId = row.dataset.id;

    var currentName = row.querySelector(".cell-name").textContent;
    var currentPrice = row.querySelector(".cell-price").textContent;
    var currentQuantity = row.querySelector(".cell-quantity").textContent;
    var currentReorder = row.querySelector(".cell-reorder").textContent;
    var currentExpiration = row.querySelector(".cell-expiration").textContent;

    var newName = prompt("Product name:", currentName);
    if (newName === null) return; // cancelled

    var newPrice = prompt("Price:", currentPrice);
    var newQuantity = prompt("Quantity:", currentQuantity);
    var newReorder = prompt("Reorder level:", currentReorder);
    var newExpiration = prompt("Expiration date (YYYY-MM-DD):", currentExpiration);

    var params = "edit_product=1"
        + "&product_id=" + encodeURIComponent(productId)
        + "&product_name=" + encodeURIComponent(newName)
        + "&price=" + encodeURIComponent(newPrice)
        + "&quantity=" + encodeURIComponent(newQuantity)
        + "&reorder_level=" + encodeURIComponent(newReorder)
        + "&expiration_date=" + encodeURIComponent(newExpiration);

    var xhttp = new XMLHttpRequest();
    xhttp.open("POST", "../db/product_requests.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            if (this.responseText.trim() === "success") {
                // update the row's text directly instead of reloading the page
                row.querySelector(".cell-name").textContent = newName;
                row.querySelector(".cell-price").textContent = newPrice;
                row.querySelector(".cell-quantity").textContent = newQuantity;
                row.querySelector(".cell-reorder").textContent = newReorder;
                row.querySelector(".cell-expiration").textContent = newExpiration;
            } else {
                alert("Error updating product.");
            }
        }
    };

    xhttp.send(params);
}