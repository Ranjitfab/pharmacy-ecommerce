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
</head>

<body>
    <h1>Admin Dashboard</h1>
    <p>Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?> | <a href="../auth/logout.php">Logout</a></p>

    <nav>
        <a href="products.php">Manage Products</a>
    </nav>

    <h2>Overview</h2>
    <ul>
        <li>Total Products: <?= $totalProducts ?></li>
        <li>Total Orders: <?= $totalOrders ?></li>
        <li>Total Customers: <?= $totalCustomers ?></li>
    </ul>

    <!-- Low stock / expiration notifications will be added here later -->
</body>

</html>