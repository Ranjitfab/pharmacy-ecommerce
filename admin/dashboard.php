<?php
require "../includes/auth.php";
require "../db/db.php";
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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/tailwind.css">
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
                <!-- <a href="orders.php" class="text-gray-600 px-4 py-3 rounded-lg font-medium no-underline hover:bg-gray-50 transition-colors">Manage Orders</a> -->
            </nav>
            <div class="p-4 border-t border-gray-100">
                <a href="../auth/logout.php" class="text-red-500 font-medium no-underline flex items-center gap-2 px-4 py-2 hover:bg-red-50 rounded-lg transition-colors">Logout</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <header class="mb-8">
                <h2 class="text-2xl font-bold m-0 mb-2">Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?></h2>
                <p class="text-gray-500 m-0">Here's what's happening with your store today.</p>
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

<<<<<<< HEAD
=======
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-lg m-0 mb-4">Quick Links</h3>
                <div class="flex gap-4">
                    <a href="../customer/shop.php" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg font-medium no-underline hover:bg-gray-200 transition-colors">View Shop</a>
                    <a href="products.php" class="bg-primary text-white px-4 py-2 rounded-lg font-medium no-underline hover:bg-primary-hover transition-colors">Add New Product</a>
                </div>
            </div>
        </main>
    </div>
>>>>>>> 270316e99ab4e14fb3342b04cec5a6abd8dbf750
</body>

</html>