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
    // helper doesn't support JOINs, so this is a simple N+1 lookup.
    // Fine at this project's scale (a handful of cart items).
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" href="../assets/css/tailwind.css">
</head>
<body class="font-sans bg-[#f8fafc] m-0 text-[#333] p-6 max-w-[1000px] mx-auto">
    <div class="flex justify-between items-center bg-white py-4 px-6 rounded-xl shadow-[0_2px_10px_rgba(0,0,0,0.02)] mb-6">
        <h1 class="m-0 text-2xl text-[#1a1a1a] font-bold">Your Cart</h1>
        <div class="nav-links">
            <a href="shop.php" class="text-primary no-underline font-medium text-sm hover:underline">&larr; Continue Shopping</a>
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
                        <th class="text-left py-3 px-4 border-b-2 border-[#edf2f7] text-[#4a5568] font-semibold text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr data-cart-item-id="<?= $item['cart_item_id'] ?>">
                            <td class="p-4 border-b border-[#edf2f7] align-middle"><?= htmlspecialchars($item['product_name']) ?></td>
                            <td class="cell-price p-4 border-b border-[#edf2f7] align-middle font-medium text-[#2d3748]">₱<?= number_format($item['price'], 2) ?></td>
                            <td class="p-4 border-b border-[#edf2f7] align-middle">
                                <div class="flex items-center gap-2">
                                    <button onclick="changeQuantity(this, -1)" class="bg-[#edf2f7] border-none py-1 px-2.5 rounded-md cursor-pointer text-[#4a5568] font-bold transition-colors duration-200 hover:bg-[#e2e8f0]">-</button>
                                    <span class="cell-quantity font-medium min-w-[20px] text-center"><?= $item['quantity'] ?></span>
                                    <button onclick="changeQuantity(this, 1)" class="bg-[#edf2f7] border-none py-1 px-2.5 rounded-md cursor-pointer text-[#4a5568] font-bold transition-colors duration-200 hover:bg-[#e2e8f0]">+</button>
                                </div>
                            </td>
                            <td class="cell-subtotal p-4 border-b border-[#edf2f7] align-middle font-medium text-[#2d3748]">₱<?= number_format($item['subtotal'], 2) ?></td>
                            <td class="p-4 border-b border-[#edf2f7] align-middle"><button class="bg-[#fee2e2] text-[#dc2626] border-none py-2 px-3 rounded-md cursor-pointer text-[13px] font-medium transition-colors duration-200 hover:bg-[#fecaca]" onclick="removeItem(this)">Remove</button></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="flex justify-between items-center bg-[#f8fafc] py-4 px-6 rounded-lg">
                <h3 class="m-0 text-xl text-[#2d3748] font-bold">Total: ₱<span id="cart-total"><?= number_format($total, 2) ?></span></h3>
                <button class="bg-primary text-white border-none py-3 px-6 rounded-lg font-medium cursor-pointer text-base transition-colors duration-200 hover:bg-primary-hover" onclick="location.href='checkout.php'">Proceed to Checkout</button>
            </div>
        <?php endif; ?>
    </div>

    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/cart.js"></script>
</body>
</html>