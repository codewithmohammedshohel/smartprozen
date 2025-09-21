<?php
require_once 'config.php';
require_once 'core/db.php';
require_once 'core/functions.php';

if (!is_logged_in()) {
    $_SESSION['error_message'] = "Access Denied. Please log in to download.";
    header('Location: /smartprozen/auth/login.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "Invalid product ID.";
    header('Location: /smartprozen/user/downloads.php');
    exit;
}

$product_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Check if the user has purchased this product
$user_has_purchased = false;
$check_purchase_stmt = $conn->prepare("SELECT o.id FROM orders o JOIN order_items oi ON o.id = oi.order_id WHERE o.user_id = ? AND oi.product_id = ? AND o.status = 'Completed' LIMIT 1");
$check_purchase_stmt->bind_param("ii", $user_id, $product_id);
$check_purchase_stmt->execute();
if ($check_purchase_stmt->get_result()->num_rows > 0) {
    $user_has_purchased = true;
}

if (!$user_has_purchased) {
    $_SESSION['error_message'] = "Access Denied. You must purchase this product to download it.";
    header('Location: /smartprozen/index.php');
    exit;
}

// Fetch the digital file path for the product
$stmt = $conn->prepare("SELECT digital_file_path FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if ($product && !empty($product['digital_file_path'])) {
    $file_path = __DIR__ . '/uploads/files/' . $product['digital_file_path'];
    if (file_exists($file_path)) {
        log_activity('user', $user_id, 'file_download', "Downloaded product ID: {$product_id}");
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit;
    } else {
        $_SESSION['error_message'] = "File not found.";
        header('Location: /smartprozen/user/downloads.php');
        exit;
    }
} else {
    $_SESSION['error_message'] = "Invalid product or file path.";
    header('Location: /smartprozen/user/downloads.php');
    exit;
}
?>