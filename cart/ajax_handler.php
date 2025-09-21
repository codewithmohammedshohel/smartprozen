<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/functions.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';
$response = [
    'success' => false,
    'message' => 'Invalid action.',
];

if ($action === 'add') {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 1);
    if ($product_id > 0 && $quantity > 0) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
        $response = ['success' => true, 'message' => 'Product added to cart.'];
    }
} elseif ($action === 'update') {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 0);
    if ($product_id > 0) {
        if ($quantity > 0) {
            $_SESSION['cart'][$product_id] = $quantity;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
        $response = ['success' => true, 'message' => 'Cart updated.'];
    }
} elseif ($action === 'remove') {
    $product_id = (int)($_POST['product_id'] ?? 0);
    if ($product_id > 0) {
        unset($_SESSION['cart'][$product_id]);
        $response = ['success' => true, 'message' => 'Product removed from cart.'];
    }
}

// Recalculate cart totals
$subtotal = 0;
$cart_items_count = 0;
if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $stmt = $conn->prepare("SELECT id, price FROM products WHERE id IN ($placeholders)");
    $stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($product = $result->fetch_assoc()) {
        $quantity = $_SESSION['cart'][$product['id']];
        $subtotal += $product['price'] * $quantity;
        $cart_items_count += $quantity;
    }
}

$discount = $_SESSION['discount_amount'] ?? 0;
$total = $subtotal - $discount;

$response['cart'] = [
    'subtotal' => number_format($subtotal, 2),
    'total' => number_format($total, 2),
    'item_count' => $cart_items_count,
];

echo json_encode($response);
exit;
?>