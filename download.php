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

// Fetch the digital file path, download limit, and expiry for the product
$stmt = $conn->prepare("SELECT digital_file_path, download_limit, download_expiry_days FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if ($product && !empty($product['digital_file_path'])) {
    $file_path = __DIR__ . '/../uploads/files/' . $product['digital_file_path'];

    // Check download limits and expiry
    $download_record_stmt = $conn->prepare("SELECT download_count, expires_at FROM downloads WHERE user_id = ? AND product_id = ?");
    $download_record_stmt->bind_param("ii", $user_id, $product_id);
    $download_record_stmt->execute();
    $download_record = $download_record_stmt->get_result()->fetch_assoc();
    $download_record_stmt->close();

    $can_download = true;
    $error_message = '';

    if ($download_record) {
        // Check download limit
        if ($product['download_limit'] !== null && $download_record['download_count'] >= $product['download_limit']) {
            $can_download = false;
            $error_message = "Download limit exceeded for this product.";
        }

        // Check expiry date
        if ($product['download_expiry_days'] !== null && $download_record['expires_at'] !== null && strtotime($download_record['expires_at']) < time()) {
            $can_download = false;
            $error_message = "Download link has expired.";
        }
    } else {
        // If no download record exists, create one (assuming purchase implies first download attempt)
        // This part needs to be linked to order completion, but for now, we'll create a basic one.
        // In a real system, this record should be created when the order is completed.
        $expires_at = null;
        if ($product['download_expiry_days'] !== null) {
            $expires_at = date('Y-m-d H:i:s', strtotime('+{$product['download_expiry_days']} days'));
        }
        $insert_download_stmt = $conn->prepare("INSERT INTO downloads (user_id, product_id, download_count, max_downloads, expires_at) VALUES (?, ?, 0, ?, ?)");
        $insert_download_stmt->bind_param("iiis", $user_id, $product_id, $product['download_limit'], $expires_at);
        $insert_download_stmt->execute();
        $insert_download_stmt->close();
        
        // Re-fetch the record after insertion
        $download_record_stmt = $conn->prepare("SELECT download_count, expires_at FROM downloads WHERE user_id = ? AND product_id = ?");
        $download_record_stmt->bind_param("ii", $user_id, $product_id);
        $download_record_stmt->execute();
        $download_record = $download_record_stmt->get_result()->fetch_assoc();
        $download_record_stmt->close();
    }

    if (!$can_download) {
        $_SESSION['error_message'] = $error_message;
        header('Location: /smartprozen/user/downloads.php');
        exit;
    }

    if (file_exists($file_path)) {
        // Increment download count
        $update_download_stmt = $conn->prepare("UPDATE downloads SET download_count = download_count + 1, last_downloaded_at = CURRENT_TIMESTAMP WHERE user_id = ? AND product_id = ?");
        $update_download_stmt->bind_param("ii", $user_id, $product_id);
        $update_download_stmt->execute();
        $update_download_stmt->close();

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