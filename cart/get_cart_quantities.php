<?php
require_once '../config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$items = [];
$total_items = 0;

foreach ($_SESSION['cart'] as $product_id => $item) {
    $items[] = [
        'product_id' => $product_id,
        'name' => $item['name'],
        'price' => $item['price'],
        'sale_price' => $item['sale_price'] ?? null,
        'quantity' => $item['quantity']
    ];
    $total_items += $item['quantity'];
}

echo json_encode([
    'success' => true,
    'items' => $items,
    'total_items' => $total_items,
    'total_amount' => get_cart_total()
]);
?>