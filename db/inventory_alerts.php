<?php
// Reusable Inventory Alert logic

function getInventoryAlerts($myDB)
{
    $today = new DateTimeImmutable('today');
    $alerts = [];

    $myDB->select('products', '*');
    $products = $myDB->res->fetch_all(MYSQLI_ASSOC);

    foreach ($products as $product) {
        $quantity      = (int) $product['quantity'];
        $reorderLevel  = (int) $product['reorder_level'];
        $expirationDate = $product['expiration_date'] ? new DateTimeImmutable($product['expiration_date']) : null;

        if ($quantity <= $reorderLevel) {
            $alerts[] = [
                'type'    => 'low_stock',
                'title'   => 'Low Stock',
                'product' => $product['product_name'],
                'message' => $product['product_name'] . ' is low in stock (' . $quantity . ' left).',
            ];
        }

        if ($expirationDate) {
            $daysLeft = (int) $today->diff($expirationDate)->format('%r%a');

            if ($daysLeft < 0) {
                // Expired products are labeled as expired
                $alerts[] = [
                    'type'    => 'expired',
                    'title'   => 'Expired',
                    'product' => $product['product_name'],
                    'message' => $product['product_name'] . ' expired ' . abs($daysLeft) . ' day(s) ago.',
                ];
            } elseif ($daysLeft <= 30) {
                $alerts[] = [
                    'type'    => 'expiring_soon',
                    'title'   => 'Expiring Soon',
                    'product' => $product['product_name'],
                    'message' => $product['product_name'] . ' expires in ' . $daysLeft . ' day(s).',
                ];
            }
        }
    }

    return $alerts;
}
