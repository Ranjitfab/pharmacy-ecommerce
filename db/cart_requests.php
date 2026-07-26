<?php
require "../includes/auth.php";
require "db.php";
requireLogin();

$myDB = new myDB();
$userId = $_SESSION['user_id'];

// ------------------------------------------------------------
// Helper: get the logged-in user's cart_id, creating a cart
// if they don't have one yet.
// ------------------------------------------------------------
function getOrCreateCartId($myDB, $userId)
{
    $myDB->select('cart', '*', ['user_id' => $userId]);

    if ($myDB->res->num_rows > 0) {
        $cart = $myDB->res->fetch_assoc();
        return $cart['cart_id'];
    }

    $myDB->insert('cart', ['user_id' => $userId]);

    // insert() doesn't return the new ID, so fetch it back
    $myDB->select('cart', '*', ['user_id' => $userId]);
    $cart = $myDB->res->fetch_assoc();
    return $cart['cart_id'];
}

// ------------------------------------------------------------
// ADD TO CART - AJAX
// If the product is already in the cart, increase its quantity
// instead of creating a duplicate row (matches the
// unique_cart_product constraint in the schema).
// ------------------------------------------------------------
if (isset($_POST['add_to_cart'])) {
    $cartId = getOrCreateCartId($myDB, $userId);
    $productId = $_POST['product_id'];
    $qtyToAdd = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    $myDB->select('cart_items', '*', ['cart_id' => $cartId, 'product_id' => $productId]);

    if ($myDB->res->num_rows > 0) {
        $existing = $myDB->res->fetch_assoc();
        $newQty = $existing['quantity'] + $qtyToAdd;

        $myDB->update(
            'cart_items',
            ['quantity' => $newQty],
            ['cart_item_id' => $existing['cart_item_id']]
        );
    } else {
        $myDB->insert('cart_items', [
            'cart_id'    => $cartId,
            'product_id' => $productId,
            'quantity'   => $qtyToAdd,
        ]);
    }

    echo "success";
    exit();
}

// ------------------------------------------------------------
// UPDATE QUANTITY - AJAX (from the cart page, +/- buttons)
// ------------------------------------------------------------
if (isset($_POST['update_quantity'])) {
    $newQty = (int)$_POST['quantity'];

    if ($newQty <= 0) {
        $myDB->delete('cart_items', ['cart_item_id' => $_POST['cart_item_id']]);
        echo "removed";
        exit();
    }

    $myDB->update(
        'cart_items',
        ['quantity' => $newQty],
        ['cart_item_id' => $_POST['cart_item_id']]
    );

    echo "success";
    exit();
}

// ------------------------------------------------------------
// REMOVE ITEM - AJAX
// ------------------------------------------------------------
if (isset($_POST['remove_item'])) {
    $myDB->delete('cart_items', ['cart_item_id' => $_POST['cart_item_id']]);
    echo "success";
    exit();
}