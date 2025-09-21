<?php
require_once '../config.php';
require_once '../core/db.php'; // Added for database access
require_once '../core/functions.php'; // Added for get_translated_text

header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'cart_items' => [], 'cart_total' => 0];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method.';
    echo json_encode($response);
    exit;
}

$action = $_POST['action'] ?? '';
$product_id = (int)($_POST['product_id'] ?? 0);
$quantity = (int)($_POST['quantity'] ?? 0);

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

switch ($action) {
    case 'remove':
        if ($product_id > 0 && isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            $response['success'] = true;
            $response['message'] = 'Product removed from cart.';
        } else {
            $response['message'] = 'Product not found in cart.';
        }
        break;

    case 'update':
        if ($product_id > 0) {
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id] = $quantity;
                $response['success'] = true;
                $response['message'] = 'Cart updated.';
            } else {
                // If quantity is 0, remove the item
                unset($_SESSION['cart'][$product_id]);
                $response['success'] = true;
                $response['message'] = 'Product removed from cart.';
            }
        } else {
            $response['message'] = 'Invalid product ID.';
        }
        break;

    case 'update_multiple': // For updating multiple quantities at once (e.g., from cart page)
        if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
            foreach ($_POST['quantities'] as $p_id => $qty) {
                $p_id = (int)$p_id;
                $qty = (int)$qty;
                if ($p_id > 0) {
                    if ($qty > 0) {
                        $_SESSION['cart'][$p_id] = $qty;
                    } else {
                        unset($_SESSION['cart'][$p_id]);
                    }
                }
            }
            $response['success'] = true;
            $response['message'] = 'Cart updated.';
        } else {
            $response['message'] = 'No quantities provided for update.';
        }
        break;

    default:
        $response['message'] = 'Invalid action.';
        break;
}

// Recalculate cart items and total for response
if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    
    // Use prepared statement for fetching product details
    $stmt = $conn->prepare("SELECT id, name, price, image_filename FROM products WHERE id IN ($placeholders)");
    $types = str_repeat('i', count($product_ids));
    $stmt->bind_param($types, ...$product_ids);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $cart_total = 0;
    while ($product = $result->fetch_assoc()) {
        $quantity = $_SESSION['cart'][$product['id']];
        $item_total = $product['price'] * $quantity;
        $cart_total += $item_total;
        $response['cart_items'][] = [
            'id' => $product['id'],
            'name' => get_translated_text($product['name'], 'name'),
            'price' => $product['price'],
            'image_filename' => $product['image_filename'],
            'quantity' => $quantity,
            'item_total' => $item_total
        ];
    }
    $stmt->close();
}

$response['cart_total'] = $cart_total;
$total_items_in_cart = 0;
foreach ($_SESSION['cart'] as $qty) {
    $total_items_in_cart += $qty;
}
$response['cart_count'] = $total_items_in_cart;

echo json_encode($response);
exit;