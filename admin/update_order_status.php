<?php
require_once '../config.php';
require_once '../core/db.php';
require_once '../core/functions.php';
require_once '../core/whatsapp_handler.php';
require_once '../core/email_handler.php';

if (!is_admin_logged_in() || !has_permission('manage_orders')) {
    header('Location: /smartprozen/admin/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['order_id']) || !isset($_POST['status'])) {
    header('Location: /smartprozen/admin/view_orders.php');
    exit;
}

$order_id = (int)$_POST['order_id'];
$status = $_POST['status'];

// Update order status
$stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $order_id);
$stmt->execute();
$stmt->close();

log_activity('admin', $_SESSION['admin_id'], 'order_status_update', "Updated order #$order_id status to '$status'");

// If status is "Completed", grant download access and notify customer
if ($status === 'Completed') {
    $stmt = $conn->prepare("SELECT user_id FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_info = $stmt->get_result()->fetch_assoc();
$stmt->close();
    $user_id = $order_info['user_id'];
    
    $stmt = $conn->prepare("SELECT name, email, whatsapp_number FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
    $user_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    $whatsapp_number = $user_info['whatsapp_number'];
    
    $stmt = $conn->prepare("SELECT p.name, p.id as product_id FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $order_items = $stmt->get_result();
    $stmt->close();
    
    $download_links = [];
    while ($item = $order_items->fetch_assoc()) {
        $download_links[] = get_translated_text($item['name'], 'name') . ": " . SITE_URL . '/download.php?id=' . $item['product_id'];
    }
    
    if (!empty($whatsapp_number)) {
        $message = "Your order #$order_id is complete!\n\nDownload your products:\n" . implode("\n", $download_links);
        send_whatsapp_message($whatsapp_number, $message);
    }
    
    $download_stmt = $conn->prepare("INSERT INTO downloads (user_id, product_id, order_id) VALUES (?, ?, ?)");    
    while ($item = $order_items->fetch_assoc()) {
        $product_id = $item['product_id'];
        // Check if a download link already exists for this user/product/order to prevent duplicates
        $stmt = $conn->prepare("SELECT id FROM downloads WHERE user_id = ? AND product_id = ? AND order_id = ?");
$stmt->bind_param("iii", $user_id, $product_id, $order_id);
$stmt->execute();
$check_exists = $stmt->get_result()->num_rows;
$stmt->close();
        if($check_exists == 0) {
            // $token = bin2hex(random_bytes(32)); // No longer needed if download_token column is removed
            $download_stmt->bind_param("iii", $user_id, $product_id, $order_id); // Changed "iiis" to "iii"
            $download_stmt->execute();
        }
    }
    $download_stmt->close();

    // Send "Order Completed" email
    send_email_template('order_completed', $user_info['email'], [
        'customer_name' => $user_info['name'],
        'order_id' => $order_id
    ]);
}

$_SESSION['success_message'] = "Order #$order_id status updated to $status.";
header('Location: /smartprozen/admin/view_orders.php');
exit;
?>