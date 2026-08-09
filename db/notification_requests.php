<?php
require "../includes/auth.php";
require "db.php";
require "inventory_alerts.php";
requireAdmin();

header('Content-Type: application/json');

$myDB = new myDB();
$alerts = getInventoryAlerts($myDB);

echo json_encode([
    'notifications' => $alerts,
]);