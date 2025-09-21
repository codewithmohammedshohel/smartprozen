<?php
require_once '../config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$action = $_POST['action'] ?? '';
$product_id = (int)($_POST['product_id'] ?? 0);

if (!$product_id) {
    echo json_encode(['success' => false, 'message' => 'Product ID required']);
    exit;
}

switch ($action) {
    case 'add':
        $quantity = (int)($_POST['quantity'] ?? 1);
        if (add_to_cart($product_id, $quantity)) {
            echo json_encode(['success' => true, 'message' => 'Product added to cart']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add product to cart']);
        }
        break;
        
    case 'update':
        $quantity = (int)($_POST['quantity'] ?? 1);
        if (update_cart_quantity($product_id, $quantity)) {
            echo json_encode(['success' => true, 'message' => 'Cart updated']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update cart']);
        }
        break;
        
    case 'remove':
        if (remove_from_cart($product_id)) {
            echo json_encode(['success' => true, 'message' => 'Product removed from cart']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to remove product']);
        }
        break;
        
    case 'clear':
        if (clear_cart()) {
            echo json_encode(['success' => true, 'message' => 'Cart cleared']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to clear cart']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>