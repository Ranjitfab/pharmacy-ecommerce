
// DELETE PRODUCT

function deleteProduct(button) {
    if (!confirm("Delete this product?")) return;

    var row = $(button).closest("tr");
    var productId = row.data("id");

    $.post("../db/product_requests.php", { delete_product: 1, product_id: productId })
        .done(function (response) {
            if (response.trim() === "success") {
                row.remove();
            } else {
                alert("Error deleting product.");
            }
        })
        .fail(function () {
            alert("Error deleting product.");
        });
}


// EDIT PRODUCT

function editProduct(button) {
    var row = $(button).closest("tr");

    $("#edit-product-id").val(row.data("id"));
    $("#edit-product-name").val(row.find(".cell-name").text().trim());
    $("#edit-price").val(row.data("price"));
    $("#edit-quantity").val(row.data("quantity"));
    $("#edit-reorder").val(row.data("reorder"));
    $("#edit-expiration").val(row.data("expiration") || "");
    $("#edit-rx").prop("checked", row.data("rx") == 1);

    $("#edit-product-modal").removeClass("hidden");
}

function closeEditModal() {
    $("#edit-product-modal").addClass("hidden");
}

$(document).ready(function () {
    // Save changes
    $("#edit-product-form").on("submit", function (e) {
        e.preventDefault();

        var productId = $("#edit-product-id").val();
        var row = $('tr[data-id="' + productId + '"]');

        var data = {
            edit_product: 1,
            product_id: productId,
            product_name: $("#edit-product-name").val(),
            price: $("#edit-price").val(),
            quantity: $("#edit-quantity").val(),
            reorder_level: $("#edit-reorder").val(),
            expiration_date: $("#edit-expiration").val(),
            requires_prescription: $("#edit-rx").is(":checked") ? 1 : 0
        };

        $.post("../db/product_requests.php", data)
            .done(function (response) {
                if (response.trim() !== "success") {
                    alert("Error updating product.");
                    return;
                }

                // Update the visible table cells
                row.find(".cell-name").text(data.product_name);
                row.find(".cell-price").text("₱" + parseFloat(data.price).toFixed(2));
                row.find(".cell-quantity").text(data.quantity);
                row.find(".cell-reorder").text(data.reorder_level);
                row.find(".cell-expiration").text(data.expiration_date || "N/A");

                // Update the row's cached data-* values so the NEXT edit
                // (without a page reload) opens the modal pre-filled correctly
                row.data("price", data.price);
                row.data("quantity", data.quantity);
                row.data("reorder", data.reorder_level);
                row.data("expiration", data.expiration_date);
                row.data("rx", data.requires_prescription);

                // Refresh the Rx badge to match the new value
                var badge = data.requires_prescription == 1
                    ? '<span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-semibold">Yes</span>'
                    : '<span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-semibold">No</span>';
                row.find(".cell-rx").html(badge);

                closeEditModal();
            })
            .fail(function () {
                alert("Error updating product.");
            });
    });

    // Clicking the dark overlay (outside the modal box) closes it,
    // same interaction pattern as the cart drawer's overlay.
    $("#edit-product-modal").on("click", function (e) {
        if (e.target === this) closeEditModal();
    });
});