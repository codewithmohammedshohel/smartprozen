<?php
require_once '../config.php';
header('Content-Type: application/json'); // Set header for JSON response

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['product_id'])) {
    $response['message'] = 'Invalid request.';
    echo json_encode($response);
    exit;
}

$product_id = (int)$_POST['product_id'];
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if ($quantity < 1) $quantity = 1;

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id] += $quantity;
} else {
    $_SESSION['cart'][$product_id] = $quantity;
}

$response['success'] = true;
$response['message'] = "Product added to cart!";
$total_items = 0;
foreach ($_SESSION['cart'] as $qty) {
    $total_items += $qty;
}
$response['cart_count'] = $total_items; // Return updated cart count
$response['product_quantity'] = $_SESSION['cart'][$product_id]; // Return quantity of this specific product

echo json_encode($response);
exit;