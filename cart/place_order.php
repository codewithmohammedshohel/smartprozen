<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/functions.php';

header('Content-Type: application/json'); // Set header for JSON response

$response = ['success' => false, 'message' => '', 'order_id' => null];

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
$shipping_contact_number = $_POST['contact_number'] ?? '';
$shipping_whatsapp_number = $_POST['whatsapp_number'] ?? '';
$payment_method = $_POST['payment_method'] ?? '';

// Basic validation
if (empty($shipping_name) || empty($shipping_email) || empty($shipping_address) || empty($shipping_city) || empty($shipping_zip) || empty($shipping_contact_number) || empty($payment_method)) {
    $response['message'] = 'All shipping and payment fields are required.';
    echo json_encode($response);
    exit;
}

$user_id = null;
if (is_logged_in()) {
    $user_id = $_SESSION['user_id'];
} else {
    // Guest user, check if user exists or create a new one
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $shipping_email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user_id = $user['id'];
    } else {
        // Create a new user
        $password = bin2hex(random_bytes(8)); // Generate a random password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, address, contact_number, whatsapp_number) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $shipping_name, $shipping_email, $hashed_password, $shipping_address, $shipping_contact_number, $shipping_whatsapp_number);
        $stmt->execute();
        $user_id = $stmt->insert_id;

        // Log the new user in
        $_SESSION['user_id'] = $user_id;
    }
}

$user_id = $_SESSION['user_id'];
$cart_items = [];
$subtotal = 0;
$all_digital = true;
$any_manual = false;

$product_ids = array_keys($_SESSION['cart']);
$placeholders = implode(',', array_fill(0, count($product_ids), '?'));
$stmt = $conn->prepare("SELECT id, name, price, image_filename, is_digital, delivery_type FROM products WHERE id IN ($placeholders)");
$stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
$stmt->execute();
$result = $stmt->get_result();
while ($product = $result->fetch_assoc()) {
    $product['quantity'] = $_SESSION['cart'][$product['id']];
    $cart_items[] = $product;
    $subtotal += $product['price'] * $product['quantity'];
    $all_digital = $product['is_digital'] && $all_digital;
    if ($product['delivery_type'] === 'manual') {
        $any_manual = true;
    }
}
$stmt->close();

$discount = $_SESSION['discount_amount'] ?? 0;
$total = $subtotal - $discount;

if ($any_manual) {
    $order_status = 'Processing';
} elseif ($all_digital) {
    $order_status = 'Completed';
} else {
    $order_status = 'Pending';
}

// Start transaction
$conn->begin_transaction();

try {
    // Generate a unique order number
    $order_number = 'ORD-' . strtoupper(uniqid()) . rand(1000, 9999);

    // Create the order
    $stmt = $conn->prepare("INSERT INTO orders (order_number, user_id, total_amount, status, shipping_name, shipping_email, shipping_address, shipping_city, shipping_zip, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sidsssssss", $order_number, $user_id, $total, $order_status, $shipping_name, $shipping_email, $shipping_address, $shipping_city, $shipping_zip, $payment_method);
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