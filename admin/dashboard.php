<?php
require "../includes/auth.php";
require "../db/db.php";
require "../db/inventory_alerts.php";
requireAdmin();

$myDB = new myDB();

$myDB->select('products', '*');
$totalProducts = $myDB->res->num_rows;

$myDB2 = new myDB();
$myDB2->select('orders', '*');
$totalOrders = $myDB2->res->num_rows;

$myDB3 = new myDB();
$myDB3->select('users', '*', ['role' => 'customer']);
$totalCustomers = $myDB3->res->num_rows;

$myDB4 = new myDB();
$adminAlerts = getInventoryAlerts($myDB4);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/tailwind.css">
    <style>
        #toast-container {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 0.625rem;
        }

        .toast {
            background: white;
            border-left: 4px solid var(--color-primary);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            color: #374151;
            opacity: 0;
            transform: translateY(8px);
            transition: opacity 0.2s ease, transform 0.2s ease;
            max-width: 320px;
        }

        .toast.toast-expired {
            border-left-color: #ef4444;
        }

        .toast.toast-expiring_soon {
            border-left-color: #eab308;
        }

        .toast.toast-low_stock {
            border-left-color: #f97316;
        }

        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>

<body class="font-sans bg-[#f4f7f6] m-0 text-[#333]">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white h-screen shadow-md flex flex-col">
            <div class="p-6 border-b border-gray-100">
                <h1 class="text-xl font-bold text-primary m-0">RxStock Admin</h1>
            </div>
            <nav class="flex-1 p-4 flex flex-col gap-2">
                <a href="dashboard.php" class="bg-primary/10 text-primary px-4 py-3 rounded-lg font-medium no-underline">Dashboard</a>
                <a href="products.php" class="text-gray-600 px-4 py-3 rounded-lg font-medium no-underline hover:bg-gray-50 transition-colors">Manage Products</a>
                <a href="orders.php" class="text-gray-600 px-4 py-3 rounded-lg font-medium no-underline hover:bg-gray-50 transition-colors">Manage Orders</a>
            </nav>
            <div class="p-4 border-t border-gray-100">
                <a href="../auth/logout.php" class="text-red-500 font-medium no-underline flex items-center gap-2 px-4 py-2 hover:bg-red-50 rounded-lg transition-colors">Logout</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <header class="mb-8 flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold m-0 mb-2">Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?></h2>
                    <p class="text-gray-500 m-0">Here's what's happening with your store today.</p>
                </div>
                <button id="enable-notifications-btn" class="bg-white border border-gray-200 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                    Enable Browser Notifications
                </button>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-gray-500 font-medium m-0 mb-2 text-sm uppercase tracking-wider">Total Products</h3>
                    <p class="text-3xl font-bold m-0"><?= $totalProducts ?></p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-gray-500 font-medium m-0 mb-2 text-sm uppercase tracking-wider">Total Orders</h3>
                    <p class="text-3xl font-bold m-0"><?= $totalOrders ?></p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-gray-500 font-medium m-0 mb-2 text-sm uppercase tracking-wider">Total Customers</h3>
                    <p class="text-3xl font-bold m-0"><?= $totalCustomers ?></p>
                </div>
            </div>

            <?php if (!empty($adminAlerts)): ?>
                <div id="admin-notification-list" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-8">
                    <h3 class="font-bold text-lg m-0 mb-4">Admin Alerts</h3>
                    <ul class="m-0 p-0 list-none flex flex-col gap-3">
                        <?php foreach ($adminAlerts as $alert): ?>
                            <?php
                            $borderColor = [
                                'expired'        => 'border-red-500 bg-red-50',
                                'expiring_soon'  => 'border-yellow-500 bg-yellow-50',
                                'low_stock'      => 'border-orange-500 bg-orange-50',
                            ][$alert['type']] ?? 'border-gray-400 bg-gray-50';
                            ?>
                            <li class="border-l-4 <?= $borderColor ?> p-3 rounded-lg text-sm text-gray-700">
                                <strong><?= htmlspecialchars($alert['title']) ?>:</strong>
                                <?= htmlspecialchars($alert['message']) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-lg m-0 mb-4">Quick Links</h3>
                <div class="flex gap-4">
                    <a href="../customer/shop.php" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg font-medium no-underline hover:bg-gray-200 transition-colors">View Shop</a>
                    <a href="products.php" class="bg-primary text-white px-4 py-2 rounded-lg font-medium no-underline hover:bg-primary-hover transition-colors">Add New Product</a>
                </div>
            </div>
        </main>
    </div>

    <div id="toast-container"></div>

    <!-- Alerts already shown above (server-rendered) are passed in so
         notifications.js only toasts/notifies about genuinely NEW alerts
         found on later polls, instead of repeating what's already visible. -->
    <script>
        const initialAlertKeys = <?= json_encode(array_map(fn($a) => $a['type'] . '|' . $a['product'], $adminAlerts)) ?>;
    </script>
    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/notifications.js"></script>
</body>

</html>