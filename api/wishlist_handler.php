<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/functions.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if (!is_logged_in()) {
    $response['message'] = 'Please log in to manage your wishlist.';
    echo json_encode($response);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'] ?? null;
$action = $_POST['action'] ?? '';

if (!$product_id) {
    $response['message'] = 'Product ID is required.';
    echo json_encode($response);
    exit;
}

switch ($action) {
    case 'add':
        $stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $product_id);
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Product added to wishlist.';
        } else {
            // Check for duplicate entry error
            if ($conn->errno == 1062) { // MySQL error code for duplicate entry
                $response['message'] = 'Product is already in your wishlist.';
            } else {
                $response['message'] = 'Failed to add product to wishlist: ' . $conn->error;
            }
        }
        $stmt->close();
        break;

    case 'remove':
        $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Product removed from wishlist.';
        } else {
            $response['message'] = 'Failed to remove product from wishlist: ' . $conn->error;
        }
        $stmt->close();
        break;

    default:
        $response['message'] = 'Invalid action.';
        break;
}

echo json_encode($response);
$conn->close();
?>