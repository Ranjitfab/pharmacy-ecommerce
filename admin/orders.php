<?php
require "../includes/auth.php";
require "../db/db.php";
requireAdmin();

$myDB = new myDB();
$myDB->select('orders', '*');
$orders = $myDB->res->fetch_all(MYSQLI_ASSOC);

// Newest orders first - select() has no ORDER BY support, so sort in PHP
usort($orders, function ($a, $b) {
    return $b['order_id'] - $a['order_id'];
});

// Attach customer name + line items to each order.
// select() has no JOIN support, so this is a per-order lookup -
// same tradeoff as cart.php and checkout.php.
foreach ($orders as &$order) {
    $userDB = new myDB();
    $userDB->select('users', '*', ['user_id' => $order['user_id']]);
    $user = $userDB->res->fetch_assoc();
    $order['customer_name'] = $user ? $user['first_name'] . ' ' . $user['last_name'] : 'Unknown';

    $itemsDB = new myDB();
    $itemsDB->select('order_items', '*', ['order_id' => $order['order_id']]);
    $rawItems = $itemsDB->res->fetch_all(MYSQLI_ASSOC);

    $order['items'] = [];
    foreach ($rawItems as $item) {
        $productDB = new myDB();
        $productDB->select('products', '*', ['product_id' => $item['product_id']]);
        $product = $productDB->res->fetch_assoc();

        $order['items'][] = [
            'product_name' => $product ? $product['product_name'] : 'Deleted product',
            'quantity'     => $item['quantity'],
            'price'        => $item['price_at_purchase'],
        ];
    }
}
unset($order); // break the reference from the foreach above
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="../assets/css/tailwind.css">
</head>

<body class="font-sans bg-[#f4f7f6] m-0 text-[#333]">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white min-h-screen shadow-md flex flex-col">
            <div class="p-6 border-b border-gray-100">
                <h1 class="text-xl font-bold text-primary m-0">RxStock Admin</h1>
            </div>
            <nav class="flex-1 p-4 flex flex-col gap-2">
                <a href="dashboard.php" class="text-gray-600 px-4 py-3 rounded-lg font-medium no-underline hover:bg-gray-50 transition-colors">Dashboard</a>
                <a href="products.php" class="text-gray-600 px-4 py-3 rounded-lg font-medium no-underline hover:bg-gray-50 transition-colors">Manage Products</a>
                <a href="orders.php" class="bg-primary/10 text-primary px-4 py-3 rounded-lg font-medium no-underline">Manage Orders</a>
            </nav>
            <div class="p-4 border-t border-gray-100">
                <a href="../auth/logout.php" class="text-red-500 font-medium no-underline flex items-center gap-2 px-4 py-2 hover:bg-red-50 rounded-lg transition-colors">Logout</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8 overflow-y-auto h-screen">
            <header class="mb-8">
                <h2 class="text-2xl font-bold m-0 mb-2">Manage Orders</h2>
                <p class="text-gray-500 m-0">All customer orders, most recent first.</p>
            </header>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <?php if (empty($orders)): ?>
                    <p class="text-gray-500 text-sm">No orders have been placed yet.</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-[800px]">
                            <thead>
                                <tr>
                                    <th class="p-3 border-b border-gray-200 text-sm font-semibold text-gray-600 bg-gray-50 rounded-tl-lg">Order ID</th>
                                    <th class="p-3 border-b border-gray-200 text-sm font-semibold text-gray-600 bg-gray-50">Customer</th>
                                    <th class="p-3 border-b border-gray-200 text-sm font-semibold text-gray-600 bg-gray-50">Date</th>
                                    <th class="p-3 border-b border-gray-200 text-sm font-semibold text-gray-600 bg-gray-50">Items</th>
                                    <th class="p-3 border-b border-gray-200 text-sm font-semibold text-gray-600 bg-gray-50">Total</th>
                                    <th class="p-3 border-b border-gray-200 text-sm font-semibold text-gray-600 bg-gray-50 rounded-tr-lg">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <?php
                                        $statusClasses = [
                                            'completed' => 'bg-green-100 text-green-700',
                                            'pending'   => 'bg-yellow-100 text-yellow-700',
                                            'cancelled' => 'bg-red-100 text-red-700',
                                        ];
                                        $badgeClass = $statusClasses[$order['status']] ?? 'bg-gray-100 text-gray-600';
                                    ?>
                                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer" onclick="toggleOrderDetails(<?= $order['order_id'] ?>)">
                                        <td class="p-3 text-sm font-medium text-gray-800 align-middle">#<?= $order['order_id'] ?></td>
                                        <td class="p-3 text-sm text-gray-700 align-middle"><?= htmlspecialchars($order['customer_name']) ?></td>
                                        <td class="p-3 text-sm text-gray-700 align-middle"><?= date('M j, Y g:i A', strtotime($order['order_date'])) ?></td>
                                        <td class="p-3 text-sm text-gray-700 align-middle"><?= count($order['items']) ?> item<?= count($order['items']) === 1 ? '' : 's' ?></td>
                                        <td class="p-3 text-sm font-medium text-gray-800 align-middle">₱<?= number_format($order['total_amount'], 2) ?></td>
                                        <td class="p-3 text-sm align-middle">
                                            <span class="<?= $badgeClass ?> px-2 py-1 rounded text-xs font-semibold capitalize"><?= htmlspecialchars($order['status']) ?></span>
                                        </td>
                                    </tr>
                                    <tr id="order-details-<?= $order['order_id'] ?>" class="hidden bg-gray-50">
                                        <td colspan="6" class="p-4">
                                            <table class="w-full text-left">
                                                <thead>
                                                    <tr>
                                                        <th class="pb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Product</th>
                                                        <th class="pb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Qty</th>
                                                        <th class="pb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Price at purchase</th>
                                                        <th class="pb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($order['items'] as $item): ?>
                                                        <tr>
                                                            <td class="py-1 text-sm text-gray-700"><?= htmlspecialchars($item['product_name']) ?></td>
                                                            <td class="py-1 text-sm text-gray-700"><?= $item['quantity'] ?></td>
                                                            <td class="py-1 text-sm text-gray-700">₱<?= number_format($item['price'], 2) ?></td>
                                                            <td class="py-1 text-sm text-gray-700">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        function toggleOrderDetails(orderId) {
            var row = document.getElementById("order-details-" + orderId);
            row.classList.toggle("hidden");
        }
    </script>
</body>

</html>