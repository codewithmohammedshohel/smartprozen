<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/functions.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'submit_review':
        if (!is_logged_in()) {
            $response['message'] = 'Please log in to submit a review.';
            echo json_encode($response);
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $product_id = $_POST['product_id'] ?? null;
        $rating = $_POST['rating'] ?? null;
        $comment = $_POST['comment'] ?? '';

        if (!$product_id || !$rating || empty($comment)) {
            $response['message'] = 'Product ID, rating, and comment are required.';
            echo json_encode($response);
            exit;
        }

        // Basic validation for rating
        $rating = (int)$rating;
        if ($rating < 1 || $rating > 5) {
            $response['message'] = 'Rating must be between 1 and 5.';
            echo json_encode($response);
            exit;
        }

        // Check if user has already reviewed this product
        $check_stmt = $conn->prepare("SELECT id FROM reviews WHERE user_id = ? AND product_id = ?");
        $check_stmt->bind_param("ii", $user_id, $product_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        if ($check_stmt->num_rows > 0) {
            $response['message'] = 'You have already submitted a review for this product.';
            echo json_encode($response);
            exit;
        }
        $check_stmt->close();

        // Insert review (is_approved = 0 by default for admin approval)!
        $stmt = $conn->prepare("INSERT INTO reviews (product_id, user_id, rating, comment, is_approved) VALUES (?, ?, ?, ?, 0)");
        $stmt->bind_param("iiis", $product_id, $user_id, $rating, $comment);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Review submitted successfully and is awaiting admin approval.';
        } else {
            $response['message'] = 'Failed to submit review: ' . $conn->error;
        }
        $stmt->close();
        break;

    case 'approve_review':
    case 'reject_review':
    case 'delete_review':
        // These actions will be handled by the admin panel
        $response['message'] = 'Admin action not implemented here directly.';
        break;

    default:
        $response['message'] = 'Invalid action.';
        break;
}

echo json_encode($response);
$conn->close();
?>