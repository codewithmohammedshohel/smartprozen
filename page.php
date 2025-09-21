<?php
require_once 'config.php';
require_once __DIR__ . '/includes/header.php';

$slug = $_GET['slug'] ?? 'home'; // Default to 'home' slug if not provided

$stmt = $conn->prepare("SELECT * FROM pages WHERE slug = ?");
$stmt->bind_param("s", $slug);
$stmt->execute();
$page = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$page) {
    // Handle 404 - Page not found
    header('HTTP/1.0 404 Not Found');
    $page_title = 'Page Not Found';
    echo '<div class="container mt-5"><div class="alert alert-danger">The page you are looking for does not exist.</div></div>';
    exit;
}

$page_title = json_decode($page['title'], true)['en'] ?? 'Untitled Page'; // For header title

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
            <?php
            $section_data = json_decode($section['content_json'] ?? '{}', true) ?: [];
            $section_type = $section['section_type'];
            $section_template_path = __DIR__ . '/templates/sections/' . $section_type . '.php';

            if (file_exists($section_template_path)) {
                // Pass section_data to the partial
                include $section_template_path;
            } else {
                // Fallback for unknown section types
                echo '<div class="alert alert-warning">Unknown section type: ' . htmlspecialchars($section_type) . '</div>';
            }
            ?>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="container mt-5">
            <div class="alert alert-info">No content sections defined for this page yet.</div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>