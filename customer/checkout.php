<?php
require "../includes/auth.php";
require "../db/db.php";
requireLogin();

$myDB = new myDB();
$userId = $_SESSION['user_id'];

$myDB->select('cart', '*', ['user_id' => $userId]);
$cart = $myDB->res->num_rows > 0 ? $myDB->res->fetch_assoc() : null;

$items = [];
$total = 0;

if ($cart) {
    $myDB->select('cart_items', '*', ['cart_id' => $cart['cart_id']]);
    $cartItems = $myDB->res->fetch_all(MYSQLI_ASSOC);

    foreach ($cartItems as $item) {
        $productDB = new myDB();
        $productDB->select('products', '*', ['product_id' => $item['product_id']]);
        $product = $productDB->res->fetch_assoc();

        $subtotal = $product['price'] * $item['quantity'];
        $total += $subtotal;

        $items[] = [
            'product_name' => $product['product_name'],
            'price'        => $product['price'],
            'quantity'     => $item['quantity'],
            'subtotal'     => $subtotal,
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
</head>

<body>
    <h1>Checkout</h1>

    <?php if (empty($items)): ?>
        <p>Your cart is empty. <a href="shop.php">Go shopping</a></p>
    <?php else: ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td>₱<?= number_format($item['price'], 2) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>₱<?= number_format($item['subtotal'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Total: ₱<?= number_format($total, 2) ?></h3>

        <div id="checkout-message"></div>
        <button id="place-order-btn" onclick="placeOrder()">Place Order</button>
    <?php endif; ?>

    <script src="../assets/js/checkout.js"></script>
</body>

</html>