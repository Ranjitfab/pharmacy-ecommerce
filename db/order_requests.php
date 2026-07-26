<?php
require "../includes/auth.php";
require "db.php";
requireLogin();

$myDB = new myDB();
$userId = $_SESSION['user_id'];

if (isset($_POST['checkout'])) {

    // ------------------------------------------------------------
    // 1. Get the user's cart and its items
    // ------------------------------------------------------------
    $myDB->select('cart', '*', ['user_id' => $userId]);

    if ($myDB->res->num_rows === 0) {
        echo "empty_cart";
        exit();
    }

    $cart = $myDB->res->fetch_assoc();

    $myDB->select('cart_items', '*', ['cart_id' => $cart['cart_id']]);
    $cartItems = $myDB->res->fetch_all(MYSQLI_ASSOC);

    if (empty($cartItems)) {
        echo "empty_cart";
        exit();
    }

    // ------------------------------------------------------------
    // 2. Validate stock BEFORE making any changes.
    // This avoids a scenario where item 1 already got deducted
    // and item 2 fails, leaving stock partially adjusted.
    // ------------------------------------------------------------
    $products = []; // cache product rows so we don't re-select them later
    foreach ($cartItems as $item) {
        $productDB = new myDB();
        $productDB->select('products', '*', ['product_id' => $item['product_id']]);
        $product = $productDB->res->fetch_assoc();

        if (!$product || $product['quantity'] < $item['quantity']) {
            echo "insufficient_stock:" . ($product['product_name'] ?? 'Unknown product');
            exit();
        }

        $products[$item['product_id']] = $product;
    }

    // ------------------------------------------------------------
    // 3. Calculate total and create the order
    // ------------------------------------------------------------
    $total = 0;
    foreach ($cartItems as $item) {
        $total += $products[$item['product_id']]['price'] * $item['quantity'];
    }

    $myDB->insert('orders', [
        'user_id'      => $userId,
        'total_amount' => $total,
        'status'       => 'completed',
    ]);

    // insert() doesn't return the new ID - fetch the most recent
    // order for this user to get it back.
    $myDB->select('orders', '*', ['user_id' => $userId]);
    $allOrders = $myDB->res->fetch_all(MYSQLI_ASSOC);
    $order = end($allOrders); // most recently inserted (highest order_id)
    $orderId = $order['order_id'];

    // ------------------------------------------------------------
    // 4. Insert order_items, deduct stock, one item at a time
    // ------------------------------------------------------------
    foreach ($cartItems as $item) {
        $product = $products[$item['product_id']];

        $myDB->insert('order_items', [
            'order_id'          => $orderId,
            'product_id'        => $item['product_id'],
            'quantity'          => $item['quantity'],
            'price_at_purchase' => $product['price'],
        ]);

        $myDB->update(
            'products',
            ['quantity' => $product['quantity'] - $item['quantity']],
            ['product_id' => $item['product_id']]
        );
    }

    // ------------------------------------------------------------
    // 5. Clear the cart now that everything has been ordered
    // ------------------------------------------------------------
    foreach ($cartItems as $item) {
        $myDB->delete('cart_items', ['cart_item_id' => $item['cart_item_id']]);
    }

    echo "success:" . $orderId;
    exit();
}
