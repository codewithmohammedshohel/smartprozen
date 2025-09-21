<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/functions.php';

if (!is_admin_logged_in() || !has_permission('manage_posts')) {
    header('Location: /smartprozen/admin/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['post_id'] ?? null;
    $title = trim($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';

    // Basic validation
    if (empty($title) || empty($content)) {
        $_SESSION['error_message'] = "Title and content are required.";
        header('Location: manage_posts.php' . ($post_id ? '?action=edit&id=' . $post_id : '?action=add'));
        exit;
    }

    $slug = slugify($title); // Generate slug from title

    if ($post_id) {
        // Update existing post
        $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ?, slug = ?, author_id = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("sssii", $title, $content, $slug, $_SESSION['admin_id'], $post_id);
        $log_action = 'post_update';
        $log_details = "Updated post ID: {$post_id} - {$title}";
    } else {
        // Insert new post
        $stmt = $conn->prepare("INSERT INTO posts (title, content, slug, author_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $title, $content, $slug, $_SESSION['admin_id']);
        $log_action = 'post_add';
        $log_details = "Added new post: {$title}";
    }

    if ($stmt->execute()) {
        log_activity('admin', $_SESSION['admin_id'], $log_action, $log_details);
        $_SESSION['success_message'] = "Post saved successfully.";
        header('Location: manage_posts.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Error saving post: " . $conn->error;
        header('Location: manage_posts.php' . ($post_id ? '?action=edit&id=' . $post_id : '?action=add'));
        exit;
    }
} elseif (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $post_id = (int)$_GET['id'];
    
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param("i", $post_id);

    if ($stmt->execute()) {
        log_activity('admin', $_SESSION['admin_id'], 'post_delete', "Deleted post ID: {$post_id}");
        $_SESSION['success_message'] = "Post deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Error deleting post: " . $conn->error;
    }
    header('Location: manage_posts.php');
    exit;
} else {
    $_SESSION['error_message'] = "Invalid request.";
    header('Location: manage_posts.php');
    exit;
}
?>