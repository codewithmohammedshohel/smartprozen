<?php
require_once '../config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

if (!is_admin_logged_in() || !has_permission('manage_pages')) {
    header('Location: /smartprozen/admin/login.php');
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $section_id = (int)$_GET['delete'];
    // Get page_id before deleting for redirect
    $page_id = null;
$stmt = $conn->prepare("SELECT page_id FROM page_sections WHERE id = ?");
$stmt->bind_param("i", $section_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $page_id = $row['page_id'];
}
$stmt->close();

    $page_slug = 'home'; // Default slug
    $slug_stmt = $conn->prepare("SELECT slug FROM pages WHERE id = ?");
    $slug_stmt->bind_param("i", $page_id);
    $slug_stmt->execute();
    $slug_result = $slug_stmt->get_result();
    if ($slug_row = $slug_result->fetch_assoc()) {
        $page_slug = $slug_row['slug'];
    }
    $slug_stmt->close();

    $stmt = $conn->prepare("DELETE FROM page_sections WHERE id = ?");
    $stmt->bind_param("i", $section_id);
    $stmt->execute();

    // Update the page's updated_at timestamp
    $update_page_stmt = $conn->prepare("UPDATE pages SET updated_at = NOW() WHERE id = ?");
    $update_page_stmt->bind_param("i", $page_id);
    $update_page_stmt->execute();
    $update_page_stmt->close();

    $_SESSION['success_message'] = "Section deleted. <a href=\"" . SITE_URL . "/" . htmlspecialchars($page_slug) . "\" target=\"_blank\">View Page</a>";
    header("Location: page_builder.php?page_id=$page_id");
    exit;
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $page_id = (int)$_POST['page_id'];

    $page_slug = 'home'; // Default slug
    $slug_stmt = $conn->prepare("SELECT slug FROM pages WHERE id = ?");
    $slug_stmt->bind_param("i", $page_id);
    $slug_stmt->execute();
    $slug_result = $slug_stmt->get_result();
    if ($slug_row = $slug_result->fetch_assoc()) {
        $page_slug = $slug_row['slug'];
    }
    $slug_stmt->close();

    $section_id = $_POST['section_id'] ?? null;
    $section_type = $_POST['section_type'] ?? null;
    $content_array = $_POST['content'] ?? [];
    $display_order = (int)($_POST['display_order'] ?? 0);

    // Handle file uploads for sections
    // This is a complex part where you'd check $_FILES, move them to /uploads/sections/,
    // and add the filename to the $content_array.

    $content_json = json_encode($content_array);

    if ($section_id) { // Update existing section
        $stmt = $conn->prepare("UPDATE page_sections SET content_json = ?, display_order = ? WHERE id = ?");
        $stmt->bind_param("sii", $content_json, $display_order, $section_id);
    } else { // Insert new section
        $stmt = $conn->prepare("INSERT INTO page_sections (page_id, section_type, content_json, display_order) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("issi", $page_id, $section_type, $content_json, $display_order);
    }
    $stmt->execute();

    // Update the page's updated_at timestamp
    $update_page_stmt = $conn->prepare("UPDATE pages SET updated_at = NOW() WHERE id = ?");
    $update_page_stmt->bind_param("i", $page_id);
    $update_page_stmt->execute();
    $update_page_stmt->close();
    
    $_SESSION['success_message'] = "Section saved successfully. <a href=\"" . SITE_URL . "/" . htmlspecialchars($page_slug) . "\" target=\"_blank\">View Page</a>";
    header("Location: page_builder.php?page_id=$page_id");
    exit;
}
?>