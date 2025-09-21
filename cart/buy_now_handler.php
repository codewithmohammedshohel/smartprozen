<?php
require_once '../config.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['product_id'])) {
    $response['message'] = 'Invalid request.';
    echo json_encode($response);
    exit;
}

$product_id = (int)$_POST['product_id'];
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if ($quantity < 1) $quantity = 1;

// Clear existing cart
$_SESSION['cart'] = [];

// Add only the current product to the cart
$_SESSION['cart'][$product_id] = $quantity;

$response['success'] = true;
$response['message'] = "Product added for direct checkout!";
$response['cart_count'] = $quantity; // For buy now, cart count is just this item's quantity

echo json_encode($response);
exit;
