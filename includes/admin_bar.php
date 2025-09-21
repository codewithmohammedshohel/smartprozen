<?php
// This file is included in the footer and displays a context-aware admin bar.

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// We need the is_admin_logged_in function, but let's not re-declare it.
// It should be available from the initial page load.
if (!function_exists('is_admin_logged_in')) {
    // If this file is somehow loaded without the function, exit gracefully.
    return;
}

if (is_admin_logged_in()) {
    $edit_link = null;
    $page_id = null;

    // Determine the current page and create the appropriate edit link
    $current_script = basename($_SERVER['SCRIPT_NAME']);

    // Connect to DB if it's not already connected.
    global $conn;
    if (!isset($conn) || !$conn) {
        require_once __DIR__ . '/../core/db.php';
    }

    switch ($current_script) {
        case 'page.php':
            if (isset($_GET['slug'])) {
                $slug = $_GET['slug'];
                $stmt = $conn->prepare("SELECT id, updated_at FROM pages WHERE slug = ?");
                $stmt->bind_param("s", $slug);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                if ($result) {
                    $page_id = $result['id'];
                    $page_updated_at = $result['updated_at'];
                    $edit_link = SITE_URL . '/admin/page_builder.php?page_id=' . $page_id;
                }
                $stmt->close();
            }
            break;

        case 'index.php':
            // Assuming the homepage is a page with slug 'home'
            $slug = 'home';
            $stmt = $conn->prepare("SELECT id, updated_at FROM pages WHERE slug = ?");
            $stmt->bind_param("s", $slug);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            if ($result) {
                $page_id = $result['id'];
                $page_updated_at = $result['updated_at'];
                $edit_link = SITE_URL . '/admin/page_builder.php?page_id=' . $page_id;
            }
            $stmt->close();
            break;

        // Future cases for products, posts, etc. can be added here
        // case 'product.php':
        //     // Logic to get product ID and link to product editor
        //     break;
    }

    if ($edit_link) {
        echo '<div class="admin-bar">';
        echo '    <div class="container">';
        echo '        <p>You are logged in as an admin. <a href="' . $edit_link . '">Click here to manage this page\'s content.</a>';
        if (isset($page_updated_at)) {
            echo ' <span class="text-muted">(Last updated: ' . date('Y-m-d H:i:s', strtotime($page_updated_at)) . ')</span>';
        }
        echo '</p>';
        echo '    </div>';
        echo '</div>';
    }
}
?>
