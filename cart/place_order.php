<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/functions.php';

header('Content-Type: application/json'); // Set header for JSON response

$response = ['success' => false, 'message' => '', 'order_id' => null];

if (!is_logged_in()) {
    $response['message'] = 'Please log in to place an order.';
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method.';
    echo json_encode($response);
    exit;
}

if (empty($_SESSION['cart'])) {
    $response['message'] = 'Your cart is empty.';
    echo json_encode($response);
    exit;
}

// Get shipping and payment info from POST
$shipping_name = $_POST['name'] ?? '';
$shipping_email = $_POST['email'] ?? '';
$shipping_address = $_POST['address'] ?? '';
$shipping_city = $_POST['city'] ?? '';
$shipping_zip = $_POST['zip'] ?? '';
$payment_method = $_POST['payment_method'] ?? '';

// Basic validation
if (empty($shipping_name) || empty($shipping_email) || empty($shipping_address) || empty($shipping_city) || empty($shipping_zip) || empty($payment_method)) {
    $response['message'] = 'All shipping and payment fields are required.';
    echo json_encode($response);
    exit;
}

$user_id = $_SESSION['user_id'];
$cart_items = [];
$subtotal = 0;

$product_ids = array_keys($_SESSION['cart']);
$placeholders = implode(',', array_fill(0, count($product_ids), '?'));
$stmt = $conn->prepare("SELECT id, name, price, image_filename FROM products WHERE id IN ($placeholders)");
$stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
$stmt->execute();
$result = $stmt->get_result();
while ($product = $result->fetch_assoc()) {
    $product['quantity'] = $_SESSION['cart'][$product['id']];
    $cart_items[] = $product;
    $subtotal += $product['price'] * $product['quantity'];
}
$stmt->close();

$discount = $_SESSION['discount_amount'] ?? 0;
$total = $subtotal - $discount;

// Start transaction
$conn->begin_transaction();

try {
    // Create the order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status, shipping_name, shipping_email, shipping_address, shipping_city, shipping_zip, payment_method) VALUES (?, ?, 'Pending', ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("idssssss", $user_id, $total, $shipping_name, $shipping_email, $shipping_address, $shipping_city, $shipping_zip, $payment_method);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // Insert order items
    $stmt_items = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)");
    foreach ($cart_items as $item) {
        $stmt_items->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
        $stmt_items->execute();
    }
    $stmt_items->close();

    // Clear the cart
    unset($_SESSION['cart']);
    unset($_SESSION['coupon_code']);
    unset($_SESSION['discount_amount']);

    $conn->commit(); // Commit transaction

    $response['success'] = true;
    $response['message'] = 'Order placed successfully!';
    $response['order_id'] = $order_id;

} catch (mysqli_sql_exception $e) {
    $conn->rollback(); // Rollback transaction on error
    $response['message'] = 'Failed to place order: ' . $e->getMessage();
}

echo json_encode($response);
exit;