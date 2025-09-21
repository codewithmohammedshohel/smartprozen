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
    $title_en = trim($_POST['title_en'] ?? '');
    $title_bn = trim($_POST['title_bn'] ?? '');
    $content_en = $_POST['content_en'] ?? '';
    $content_bn = $_POST['content_bn'] ?? '';

    // Basic validation
    if (empty($title_en) || empty($content_en)) {
        $_SESSION['error_message'] = "English title and content are required.";
        header('Location: manage_posts.php' . ($post_id ? '?action=edit&id=' . $post_id : '?action=add'));
        exit;
    }

    $slug = slugify($title_en); // Generate slug from English title

    // Prepare multilingual JSON data
    $title_json = json_encode(['en' => $title_en, 'bn' => $title_bn]);
    $content_json = json_encode(['en' => $content_en, 'bn' => $content_bn]);

    if ($post_id) {
        // Update existing post
        $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ?, slug = ?, author_id = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("sssii", $title_json, $content_json, $slug, $_SESSION['admin_id'], $post_id);
        $log_action = 'post_update';
        $log_details = "Updated post ID: {$post_id} - {$title_en}";
    } else {
        // Insert new post
        $stmt = $conn->prepare("INSERT INTO posts (title, content, slug, author_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $title_json, $content_json, $slug, $_SESSION['admin_id']);
        $log_action = 'post_add';
        $log_details = "Added new post: {$title_en}";
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
} else {
    $_SESSION['error_message'] = "Invalid request.";
    header('Location: manage_posts.php');
    exit;
}
?>