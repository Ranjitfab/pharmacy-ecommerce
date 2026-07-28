<?php
require "../includes/auth.php";
require "db.php";
requireLogin();

$myDB = new myDB();
$userId = $_SESSION['user_id'];

// Creates or gets the cart of a user
function getOrCreateCartId($myDB, $userId)
{
    $myDB->select('cart', '*', ['user_id' => $userId]);

    if ($myDB->res->num_rows > 0) {
        $cart = $myDB->res->fetch_assoc();
        return $cart['cart_id'];
    }

    $myDB->insert('cart', ['user_id' => $userId]);

    $myDB->select('cart', '*', ['user_id' => $userId]);
    $cart = $myDB->res->fetch_assoc();
    return $cart['cart_id'];
}

// Add to cart AJAX 
if (isset($_POST['add_to_cart'])) {
    $cartId = getOrCreateCartId($myDB, $userId);
    $productId = $_POST['product_id'];
    $qtyToAdd = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    $myDB->select('cart_items', '*', ['cart_id' => $cartId, 'product_id' => $productId]);

    //If item is already in cart, increase quantity instead of creating another row
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

// Update quantity using +/-
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
