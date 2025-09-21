<?php
require_once '../config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

header('Content-Type: application/json'); // Set header for JSON response

$response = ['success' => false, 'message' => '', 'discount_amount' => 0];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['coupon_code'])) {
    $response['message'] = 'Invalid request.';
    echo json_encode($response);
    exit;
}

$coupon_code = strtoupper(trim($_POST['coupon_code']));
$stmt = $conn->prepare("SELECT * FROM coupons WHERE code = ? AND is_active = 1 AND (expires_at IS NULL OR expires_at >= CURDATE())");
$stmt->bind_param("s", $coupon_code);
$stmt->execute();
$coupon = $stmt->get_result()->fetch_assoc();

if ($coupon) {
    // Calculate subtotal
    $subtotal = 0;
    if (!empty($_SESSION['cart'])) {
        $product_ids = array_keys($_SESSION['cart']);
        $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
        $stmt_products = $conn->prepare("SELECT id, price FROM products WHERE id IN ($placeholders)");
        $stmt_products->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
        $stmt_products->execute();
        $result = $stmt_products->get_result();
        while ($product = $result->fetch_assoc()) {
            $subtotal += $product['price'] * $_SESSION['cart'][$product['id']];
        }
        $stmt_products->close();
    }
    
    // Apply discount
    $discount_amount = 0;
    if ($coupon['type'] === 'percentage') {
        $discount_amount = ($subtotal * $coupon['value']) / 100;
    } else { // fixed
        $discount_amount = $coupon['value'];
    }
    
    $_SESSION['coupon_code'] = $coupon_code;
    $_SESSION['discount_amount'] = $discount_amount;
    
    $response['success'] = true;
    $response['message'] = "Coupon '{$coupon_code}' applied successfully.";
    $response['discount_amount'] = $discount_amount;
} else {
    unset($_SESSION['coupon_code']);
    unset($_SESSION['discount_amount']);
    $response['message'] = "Invalid or expired coupon code.";
}

echo json_encode($response);
exit;