<?php
require_once 'config.php';
require_once 'core/functions.php';
require_once __DIR__ . '/includes/header.php';

$slug = $_GET['slug'] ?? 'home'; // Default to 'home' slug if not provided

$stmt = $conn->prepare("SELECT * FROM pages WHERE slug = ?");
$stmt->bind_param("s", $slug);
$stmt->execute();
$page = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$page) {
    http_response_code(404);
    $page_title = 'Page Not Found';
    echo '<div class="container mt-5"><div class="alert alert-danger">The page you are looking for does not exist.</div></div>';
    exit;
}

$page_title = $page['title']; // Use the raw title

// Fetch sections for this page
$sections_stmt = $conn->prepare("SELECT * FROM page_sections WHERE page_id = ? ORDER BY display_order ASC");
$sections_stmt->bind_param("i", $page['id']);
$sections_stmt->execute();
$page_sections = $sections_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$sections_stmt->close();

?>

<div class="page-content">

    <?php if (!empty($page_sections)): ?>
        <?php foreach ($page_sections as $section): ?>
            <div class="editable-section">
                <?php if (is_admin_logged_in()): ?>
                    <a href="<?php echo SITE_URL . '/admin/page_builder.php?page_id=' . $page['id'] . '#section-' . $section['id']; ?>" class="btn btn-sm btn-primary edit-btn" title="Edit Section">
                        <i class="bi bi-pencil-fill"></i> Edit
                    </a>
                <?php endif; ?>
                <?php
                $section_data = json_decode($section['content_json'] ?? '{}', true) ?: [];
                $section_type = $section['section_type'];
                $section_template_path = __DIR__ . '/templates/sections/' . $section_type . '.php';

                if (file_exists($section_template_path)) {
                    include $section_template_path;
                } else {
                    echo '<div class="container"><div class="alert alert-warning">Unknown section type: ' . htmlspecialchars($section_type) . '</div></div>';
                }
                ?>
            </div>
        <?php endforeach; ?>
    <?php elseif (is_admin_logged_in()): ?>
        <div class="container mt-5 text-center">
            <div class="alert alert-info">This page has no content yet. <a href="<?php echo SITE_URL . '/admin/page_builder.php?page_id=' . $page['id']; ?>" class="alert-link">Click here to start building it!</a></div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
