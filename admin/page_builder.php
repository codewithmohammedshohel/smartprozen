<?php
require_once '../config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

if (!is_admin_logged_in() || !has_permission('manage_pages')) {
    header('Location: /smartprozen/admin/login.php');
    exit;
}

if (!isset($_GET['page_id'])) {
    header('Location: manage_pages.php');
    exit;
}
$page_id = (int)$_GET['page_id'];
$stmt = $conn->prepare("SELECT * FROM pages WHERE id = ?");
$stmt->bind_param("i", $page_id);
$stmt->execute();
$page = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Handle add/edit section logic here (omitted for brevity, would be a large POST handler)

$stmt = $conn->prepare("SELECT * FROM page_sections WHERE page_id = ? ORDER BY display_order ASC");
$stmt->bind_param("i", $page_id);
$stmt->execute();
$sections = $stmt->get_result();
$stmt->close();

$section_types = [
    'hero' => 'Hero Section',
    'rich_text' => 'Rich Text Block',
    'featured_products' => 'Featured Products',
    'faq' => 'FAQ Section',
    'testimonials' => 'Testimonials'
];

require_once '../includes/admin_header.php';
?>
<div class="dashboard-layout">
    <?php require_once '../includes/admin_sidebar.php'; ?>
    <div class="dashboard-content">
        <div class="dashboard-header">
            <h1>Page Builder: <?php echo htmlspecialchars($page['title']); ?></h1>
            <a href="manage_pages.php" class="btn btn-secondary">Back to Pages</a>
        </div>
        
        <div class="form-container">
            <h2>Add New Section</h2>
            <form action="handle_section.php" method="POST"> <input type="hidden" name="page_id" value="<?php echo $page_id; ?>">
                <label for="section_type">Section Type:</label>
                <select name="section_type" id="section_type">
                    <?php foreach ($section_types as $slug => $name): ?>
                        <option value="<?php echo $slug; ?>"><?php echo $name; ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn">Add Section</button>
            </form>
        </div>

        <div class="sections-container">
            <h2>Page Sections</h2>
            <?php while($section = $sections->fetch_assoc()): 
                $content = json_decode($section['content'], true);
            ?>
                <div class="section-item form-container">
                    <h3>Editing: <?php echo $section_types[$section['section_type']]; ?></h3>
                    <form action="handle_section.php" method="POST">
                        <input type="hidden" name="section_id" value="<?php echo $section['id']; ?>">
                        <?php if ($section['section_type'] === 'hero'): ?>
                            <label>Title (EN):</label>
                            <input type="text" name="content[title_en]" value="<?php echo htmlspecialchars($content['title_en'] ?? ''); ?>">
                            <label>Title (BN):</label>
                            <input type="text" name="content[title_bn]" value="<?php echo htmlspecialchars($content['title_bn'] ?? ''); ?>">
                             <label>Subtitle (EN):</label>
                            <input type="text" name="content[subtitle_en]" value="<?php echo htmlspecialchars($content['subtitle_en'] ?? ''); ?>">
                            <label>Subtitle (BN):</label>
                            <input type="text" name="content[subtitle_bn]" value="<?php echo htmlspecialchars($content['subtitle_bn'] ?? ''); ?>">
                            <label>Video URL (YouTube/Vimeo, optional):</label>
                            <input type="text" name="content[video_url]" value="<?php echo htmlspecialchars($content['video_url'] ?? ''); ?>">
                            <?php elseif ($section['section_type'] === 'rich_text'): ?>
                            <label>Content (EN):</label>
                            <textarea class="tinymce" name="content[text_en]"><?php echo htmlspecialchars($content['text_en'] ?? ''); ?></textarea>
                            <label>Content (BN):</label>
                            <textarea class="tinymce" name="content[text_bn]"><?php echo htmlspecialchars($content['text_bn'] ?? ''); ?></textarea>
                        <?php endif; ?>
                        <label>Display Order:</label>
                        <input type="number" name="display_order" value="<?php echo $section['display_order']; ?>">
                        <button type="submit" class="btn">Save Section</button>
                        <a href="handle_section.php?delete=<?php echo $section['id']; ?>" class="btn btn-danger">Delete</a>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>
<?php require_once '../includes/admin_footer.php'; ?>