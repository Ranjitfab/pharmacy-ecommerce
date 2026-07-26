<?php
require "../includes/auth.php";
require "db.php";
requireAdmin();

$myDB = new myDB();

// ------------------------------------------------------------
// ADD PRODUCT - traditional form POST, page reload (matches the
// professor's original pattern). Triggered by admin/products.php's
// "Add Product" form.
// ------------------------------------------------------------
if (isset($_POST['add_product'])) {
    $myDB->insert('products', [
        'category_id'            => $_POST['category_id'],
        'supplier_id'             => $_POST['supplier_id'],
        'product_name'            => $_POST['product_name'],
        'description'             => $_POST['description'],
        'price'                   => $_POST['price'],
        'quantity'                => $_POST['quantity'],
        'reorder_level'           => $_POST['reorder_level'],
        'expiration_date'         => $_POST['expiration_date'],
        'requires_prescription'   => isset($_POST['requires_prescription']) ? 1 : 0,
    ]);

    header("Location: ../admin/products.php");
    exit();
}

// ------------------------------------------------------------
// EDIT PRODUCT - AJAX. Expects POST data + product_id, returns
// a plain "success" or "error" string for product.js to check.
// ------------------------------------------------------------
if (isset($_POST['edit_product'])) {
    $myDB->update(
        'products',
        [
            'product_name'          => $_POST['product_name'],
            'price'                 => $_POST['price'],
            'quantity'              => $_POST['quantity'],
            'reorder_level'         => $_POST['reorder_level'],
            'expiration_date'       => $_POST['expiration_date'],
            'requires_prescription' => isset($_POST['requires_prescription']) ? 1 : 0,
        ],
        ['product_id' => $_POST['product_id']]
    );

    echo "success";
    exit();
}

// ------------------------------------------------------------
// DELETE PRODUCT - AJAX. Expects product_id, returns "success".
// ------------------------------------------------------------
if (isset($_POST['delete_product'])) {
    $myDB->delete('products', ['product_id' => $_POST['product_id']]);
    echo "success";
    exit();
}
