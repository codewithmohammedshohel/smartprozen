<?php
require_once '../config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

header('Content-Type: application/json');

if (!is_admin_logged_in() || !has_permission('manage_pages')) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'reorder_sections':
            $page_id = (int)$_POST['page_id'];
            $section_order = $_POST['section_order']; // Array of section IDs in new order

            if (!is_array($section_order) || empty($section_order)) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid section order data']);
                exit;
            }

            $conn->begin_transaction();
            $success = true;

            foreach ($section_order as $index => $section_id) {
                $display_order = $index + 1; // Start display order from 1
                $stmt = $conn->prepare("UPDATE page_sections SET display_order = ? WHERE id = ? AND page_id = ?");
                $stmt->bind_param("iii", $display_order, $section_id, $page_id);
                if (!$stmt->execute()) {
                    $success = false;
                    break;
                }
                $stmt->close();
            }

            if ($success) {
                $conn->commit();

                // Update the page's updated_at timestamp
                $update_page_stmt = $conn->prepare("UPDATE pages SET updated_at = NOW() WHERE id = ?");
                $update_page_stmt->bind_param("i", $page_id);
                $update_page_stmt->execute();
                $update_page_stmt->close();

                echo json_encode(['status' => 'success', 'message' => 'Sections reordered successfully']);
            } else {
                $conn->rollback();
                echo json_encode(['status' => 'error', 'message' => 'Failed to reorder sections']);
            }
            break;

        // Other section-related AJAX actions can be added here in the future

        default:
            echo json_encode(['status' => 'error', 'message' => 'Unknown action']);
            break;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>