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

if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($product_ids) - 1) . '?';
    $stmt = $conn->prepare("SELECT id, name, price, sale_price FROM products WHERE id IN ($placeholders)");
    $stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
    $stmt->execute();
    $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    $products_by_id = [];
    foreach ($products as $product) {
        $products_by_id[$product['id']] = $product;
    }
    
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $product = $products_by_id[$product_id] ?? null;
        if ($product) {
            $items[] = [
                'product_id' => $product_id,
                'name' => $product['name'],
                'price' => $product['price'],
                'sale_price' => $product['sale_price'] ?? null,
                'quantity' => $quantity
            ];
            $total_items += $quantity;
        }
    }
}

echo json_encode([
    'success' => true,
    'items' => $items,
    'total_items' => $total_items,
    'total_amount' => get_cart_total()
]);
?>