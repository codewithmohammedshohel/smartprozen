<?php
require_once '../config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = __('invalid_request');
    echo json_encode($response);
    exit;
}

$product_id = (int)($_POST['product_id'] ?? 0);
$reviewer_name = trim($_POST['reviewer_name'] ?? '');
$rating = (int)($_POST['rating'] ?? 0);
$comment = trim($_POST['comment'] ?? '');
$user_id = $_SESSION['user_id'] ?? NULL; // Assuming user_id is stored in session if logged in

// Validation
if ($product_id <= 0) {
    $response['message'] = __('invalid_product');
} elseif ($rating < 1 || $rating > 5) {
    $response['message'] = __('please_provide_a_rating');
} elseif (empty($comment)) {
    $response['message'] = __('review_comment_cannot_be_empty');
} else {
    // If user is logged in, use their name, otherwise use provided name or 'Anonymous'
    if ($user_id) {
        $stmt_user = $conn->prepare("SELECT name FROM users WHERE id = ?");
        $stmt_user->bind_param("i", $user_id);
        $stmt_user->execute();
        $user_result = $stmt_user->get_result()->fetch_assoc();
        $reviewer_name = $user_result['name'] ?? __('anonymous');
        $stmt_user->close();
    } elseif (empty($reviewer_name)) {
        $reviewer_name = __('anonymous');
    }

    $stmt = $conn->prepare("INSERT INTO reviews (product_id, user_id, rating, comment, reviewer_name, is_approved) VALUES (?, ?, ?, ?, ?, 0)");
    $stmt->bind_param("iiisi", $product_id, $user_id, $rating, $comment, $reviewer_name);
    
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = __('review_submitted_for_moderation');
    } else {
        $response['message'] = __('error_submitting_review') . ": " . $conn->error;
    }
    $stmt->close();
}

echo json_encode($response);
exit;
?>