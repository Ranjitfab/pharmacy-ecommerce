<?php
require "../includes/auth.php";
require "db.php";
requireLogin();

$myDB = new myDB();
$userId = $_SESSION['user_id'];

if (isset($_POST['checkout'])) {

    //Get the user's cart and its items
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


    //Validates if stock exists or insufficient stock
    $products = [];
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


    //Calculates order total and creates order
    $total = 0;
    foreach ($cartItems as $item) {
        $total += $products[$item['product_id']]['price'] * $item['quantity'];
    }

    $myDB->insert('orders', [
        'user_id'      => $userId,
        'total_amount' => $total,
        'status'       => 'completed',
    ]);


    $myDB->select('orders', '*', ['user_id' => $userId]);
    $allOrders = $myDB->res->fetch_all(MYSQLI_ASSOC);
    $order = end($allOrders); // most recently inserted
    $orderId = $order['order_id'];


    // Insert order_items, deduct stock, one item at a time
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

    //Clear the cart items
    foreach ($cartItems as $item) {
        $myDB->delete('cart_items', ['cart_item_id' => $item['cart_item_id']]);
    }

    echo "success:" . $orderId;
    exit();
}
