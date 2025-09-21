<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/functions.php';

header('Content-Type: application/json');

if (!is_admin_logged_in() || !has_permission('manage_pages')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? '';
$page_id = $_POST['page_id'] ?? null;

if (!$page_id) {
    echo json_encode(['success' => false, 'message' => 'Page ID is required.']);
    exit;
}

switch ($action) {
    case 'add_new_section':
        // Logic to add a new blank section
        $section_type = $_POST['section_type'] ?? 'rich_text'; // Default to rich_text
        $display_order = $_POST['display_order'] ?? 0;
        $content_json = '{ "en": "New Section Content" }'; // Default content

        $stmt = $conn->prepare("INSERT INTO page_sections (page_id, section_type, content_json, display_order) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $page_id, $section_type, $content_json, $display_order);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Section added successfully.', 'section_id' => $stmt->insert_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add section.']);
        }
        $stmt->close();
        break;

    case 'add_section_from_template':
        // Logic to add a section from a template
        $template_id = $_POST['template_id'] ?? null;
        $display_order = $_POST['display_order'] ?? 0;

        if (!$template_id) {
            echo json_encode(['success' => false, 'message' => 'Template ID is required.']);
            exit;
        }

        $template_stmt = $conn->prepare("SELECT template_type, default_content_json FROM section_templates WHERE id = ?");
        $template_stmt->bind_param("i", $template_id);
        $template_stmt->execute();
        $template_result = $template_stmt->get_result()->fetch_assoc();
        $template_stmt->close();

        if ($template_result) {
            $section_type = $template_result['template_type'];
            $content_json = $template_result['default_content_json'];

            $stmt = $conn->prepare("INSERT INTO page_sections (page_id, section_template_id, section_type, content_json, display_order) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iisss", $page_id, $template_id, $section_type, $content_json, $display_order);
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Section added from template successfully.', 'section_id' => $stmt->insert_id]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add section from template.']);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Template not found.']);
        }
        break;

    case 'update_section_content':
        // Logic to update section content
        $section_id = $_POST['section_id'] ?? null;
        $section_type = $_POST['section_type'] ?? null; // Get section type
        $content_data = $_POST['content_json'] ?? []; // This will be an array

        if (!$section_id) {
            echo json_encode(['success' => false, 'message' => 'Section ID is required.']);
            exit;
        }

        // Re-encode the content_data array to a JSON string
        $content_json_string = json_encode($content_data);

        $stmt = $conn->prepare("UPDATE page_sections SET content_json = ? WHERE id = ? AND page_id = ?");
        $stmt->bind_param("sii", $content_json_string, $section_id, $page_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Section content updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update section content.']);
        }
        $stmt->close();
        break;

    case 'delete_section':
        // Logic to delete a section
        $section_id = $_POST['section_id'] ?? null;

        if (!$section_id) {
            echo json_encode(['success' => false, 'message' => 'Section ID is required.']);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM page_sections WHERE id = ? AND page_id = ?");
        $stmt->bind_param("ii", $section_id, $page_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Section deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete section.']);
        }
        $stmt->close();
        break;

    case 'reorder_sections':
        // Logic to reorder sections
        $section_orders = $_POST['section_orders'] ?? []; // Array of {id: section_id, order: new_order}

        if (empty($section_orders)) {
            echo json_encode(['success' => false, 'message' => 'No section orders provided.']);
            exit;
        }

        $conn->begin_transaction();
        $success = true;
        foreach ($section_orders as $item) {
            $section_id = $item['id'] ?? null;
            $new_order = $item['order'] ?? null;

            if ($section_id === null || $new_order === null) {
                $success = false;
                break;
            }

            $stmt = $conn->prepare("UPDATE page_sections SET display_order = ? WHERE id = ? AND page_id = ?");
            $stmt->bind_param("iii", $new_order, $section_id, $page_id);
            if (!$stmt->execute()) {
                $success = false;
                break;
            }
            $stmt->close();
        }

        if ($success) {
            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Sections reordered successfully.']);
        } else {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Failed to reorder sections.']);
        }
        break;

    case 'get_templates':
        $templates = [];
        $templates_query = $conn->query("SELECT id, name, template_type FROM section_templates ORDER BY name ASC");
        if ($templates_query) {
            while ($row = $templates_query->fetch_assoc()) {
                $templates[] = $row;
            }
        }
        echo json_encode(['success' => true, 'templates' => $templates]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
        break;
}

$conn->close();
?>