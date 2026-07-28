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

    // Fetch each item's product details individually - the select()
    foreach ($cartItems as $item) {
        $productDB = new myDB();
        $productDB->select('products', '*', ['product_id' => $item['product_id']]);
        $product = $productDB->res->fetch_assoc();

        $subtotal = $product['price'] * $item['quantity'];
        $total += $subtotal;

        $items[] = [
            'cart_item_id' => $item['cart_item_id'],
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
    <title>Your Cart</title>
</head>
<body>
    <h1>Your Cart</h1>
    <p><a href="shop.php">Continue Shopping</a></p>

    <?php if (empty($items)): ?>
        <p>Your cart is empty.</p>
    <?php else: ?>
        <table id="cart-table" border="1">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr data-cart-item-id="<?= $item['cart_item_id'] ?>">
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td class="cell-price">₱<?= number_format($item['price'], 2) ?></td>
                        <td>
                            <button onclick="changeQuantity(this, -1)">-</button>
                            <span class="cell-quantity"><?= $item['quantity'] ?></span>
                            <button onclick="changeQuantity(this, 1)">+</button>
                        </td>
                        <td class="cell-subtotal">₱<?= number_format($item['subtotal'], 2) ?></td>
                        <td><button onclick="removeItem(this)">Remove</button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Total: ₱<span id="cart-total"><?= number_format($total, 2) ?></span></h3>
        <button onclick="location.href='checkout.php'">Proceed to Checkout</button>
    <?php endif; ?>

    <script src="../assets/js/cart.js"></script>
</body>
</html>