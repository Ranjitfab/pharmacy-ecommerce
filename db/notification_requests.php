<?php
require "../includes/auth.php";
require "db.php";
requireAdmin();

header('Content-Type: application/json');

$today = new DateTimeImmutable('today');
$notifications = [];

$myDB = new myDB();
$myDB->select('products', '*');
$products = $myDB->res->fetch_all(MYSQLI_ASSOC);

foreach ($products as $product) {
    $quantity = (int) $product['quantity'];
    $reorderLevel = (int) $product['reorder_level'];
    $expirationDate = $product['expiration_date'] ? new DateTimeImmutable($product['expiration_date']) : null;

    if ($quantity <= $reorderLevel) {
        $notifications[] = [
            'type' => 'low_stock',
            'product' => $product['product_name'],
            'message' => $product['product_name'] . ' is low in stock (' . $quantity . ' left).',
        ];
    }

    if ($expirationDate) {
        $daysLeft = (int) $today->diff($expirationDate)->format('%r%a');

        if ($daysLeft <= 30) {
            $notifications[] = [
                'type' => 'expiring_soon',
                'product' => $product['product_name'],
                'message' => $product['product_name'] . ' expires in ' . $daysLeft . ' day(s).',
            ];
        }
    }
}

echo json_encode([
    'notifications' => $notifications,
]);
