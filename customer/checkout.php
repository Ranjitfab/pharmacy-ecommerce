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
    <link rel="stylesheet" href="../assets/css/tailwind.css">
</head>

<body class="font-sans bg-[#f8fafc] m-0 text-[#333] p-6 max-w-[1000px] mx-auto">
    <div class="flex justify-between items-center bg-white py-4 px-6 rounded-xl shadow-[0_2px_10px_rgba(0,0,0,0.02)] mb-6">
        <h1 class="m-0 text-2xl text-[#1a1a1a] font-bold">Checkout</h1>
        <div class="nav-links">
            <a href="shop.php" class="text-primary no-underline font-medium text-sm hover:underline">&larr; Back to Shop</a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-[0_4px_6px_rgba(0,0,0,0.02)] p-6">

    <?php if (empty($items)): ?>
        <div class="text-center p-10 text-[#718096]">
            <p>Your cart is empty.</p>
            <a href="shop.php" class="text-primary no-underline font-medium hover:underline">Start browsing items</a>
        </div>
    <?php else: ?>
        <table id="cart-table" class="w-full border-collapse mb-6">
            <thead>
                <tr>
                    <th class="text-left py-3 px-4 border-b-2 border-[#edf2f7] text-[#4a5568] font-semibold text-sm">Product</th>
                    <th class="text-left py-3 px-4 border-b-2 border-[#edf2f7] text-[#4a5568] font-semibold text-sm">Price</th>
                    <th class="text-left py-3 px-4 border-b-2 border-[#edf2f7] text-[#4a5568] font-semibold text-sm">Quantity</th>
                    <th class="text-left py-3 px-4 border-b-2 border-[#edf2f7] text-[#4a5568] font-semibold text-sm">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td class="p-4 border-b border-[#edf2f7] align-middle"><?= htmlspecialchars($item['product_name']) ?></td>
                        <td class="cell-price p-4 border-b border-[#edf2f7] align-middle font-medium text-[#2d3748]">₱<?= number_format($item['price'], 2) ?></td>
                        <td class="cell-quantity font-medium min-w-[20px] text-center p-4 border-b border-[#edf2f7] align-middle"><?= $item['quantity'] ?></td>
                        <td class="cell-subtotal p-4 border-b border-[#edf2f7] align-middle font-medium text-[#2d3748]">₱<?= number_format($item['subtotal'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="flex justify-between items-center bg-[#f8fafc] py-4 px-6 rounded-lg mt-6">
            <h3 class="m-0 text-xl text-[#2d3748] font-bold">Total: ₱<?= number_format($total, 2) ?></h3>
            <div id="checkout-message" class="text-green-600 font-medium mr-4"></div>
            <button id="place-order-btn" class="bg-primary text-white border-none py-3 px-6 rounded-lg font-medium cursor-pointer text-base transition-colors duration-200 hover:bg-primary-hover" onclick="placeOrder()">Place Order</button>
        </div>
    <?php endif; ?>
    </div>

    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/checkout.js"></script>
</body>

</html>