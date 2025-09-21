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

// Debug: Current Page ID
echo "<!-- Debug: Current Page ID: " . $page_id . " -->";

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

// Debug: Number of sections found
echo "<!-- Debug: Number of sections found: " . $sections->num_rows . " -->";
$stmt->close();

$section_types = [
    'hero' => 'Hero Section',
    'rich_text' => 'Rich Text Block',
    'featured_products' => 'Featured Products',
    'faq' => 'FAQ Section',
    'testimonials' => 'Testimonials',
    'custom_html' => 'Custom HTML Block'
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
        
        <div class="card mb-4">
            <div class="card-header">
                <h2>Add New Section</h2>
            </div>
            <div class="card-body">
                <form action="handle_section.php" method="POST" class="form-inline">
                    <input type="hidden" name="page_id" value="<?php echo $page_id; ?>">
                    <div class="form-group">
                        <label for="section_type">Section Type:</label>
                        <select name="section_type" id="section_type" class="form-control">
                            <?php foreach ($section_types as $slug => $name): ?>
                                <option value="<?php echo $slug; ?>"><?php echo $name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Section</button>
                </form>
            </div>
        </div>

        <div class="sections-container" id="sortable-sections">
            <h2>Page Sections</h2>
            <?php 
            // Debug: Entering sections loop
            echo "<!-- Debug: Entering sections loop -->";
            while($section = $sections->fetch_assoc()): 
                echo "<!-- Debug: Section ID: " . $section['id'] . ", Type: " . $section['section_type'] . " -->";
                $content = json_decode($section['content_json'] ?? '{}', true);
            ?>
                <div class="card section-item mb-4 sortable-item" data-id="<?php echo $section['id']; ?>">
                    <form action="handle_section.php" method="POST">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">
                                <i class="bi bi-grip-vertical drag-handle"></i> 
                                Editing: <?php echo htmlspecialchars($section_types[$section['section_type']]); ?>
                            </h3>
                            <div class="section-actions">
                                <input type="hidden" name="page_id" value="<?php echo $page_id; ?>">
                                <input type="hidden" name="section_id" value="<?php echo $section['id']; ?>">
                                <input type="hidden" name="display_order" class="section-display-order" value="<?php echo $section['display_order']; ?>">
                                <button type="submit" class="btn btn-success btn-sm">Save</button>
                                <a href="handle_section.php?delete=<?php echo $section['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this section?');">Delete</a>
                                <button type="button" class="btn btn-secondary btn-sm toggle-collapse" data-bs-toggle="collapse" data-bs-target="#collapseSection<?php echo $section['id']; ?>" aria-expanded="true" aria-controls="collapseSection<?php echo $section['id']; ?>">
                                    <i class="bi bi-chevron-down"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body collapse show" id="collapseSection<?php echo $section['id']; ?>">
                            <?php if ($section['section_type'] === 'hero'): ?>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Title (EN):</label>
                                        <input type="text" name="content[title_en]" class="form-control" value="<?php echo htmlspecialchars($content['title_en'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Title (BN):</label>
                                        <input type="text" name="content[title_bn]" class="form-control" value="<?php echo htmlspecialchars($content['title_bn'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Subtitle (EN):</label>
                                        <input type="text" name="content[subtitle_en]" class="form-control" value="<?php echo htmlspecialchars($content['subtitle_en'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Subtitle (BN):</label>
                                        <input type="text" name="content[subtitle_bn]" class="form-control" value="<?php echo htmlspecialchars($content['subtitle_bn'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Video URL (YouTube/Vimeo, optional):</label>
                                    <input type="text" name="content[video_url]" class="form-control" value="<?php echo htmlspecialchars($content['video_url'] ?? ''); ?>">
                                </div>
                            <?php elseif ($section['section_type'] === 'rich_text'): ?>
                                <div class="form-group">
                                    <label>Content (EN):</label>
                                    <textarea class="tinymce" name="content[text_en]"><?php echo htmlspecialchars($content['text_en'] ?? ''); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Content (BN):</label>
                                    <textarea class="tinymce" name="content[text_bn]"><?php echo htmlspecialchars($content['text_bn'] ?? ''); ?></textarea>
                                </div>
                            <?php elseif ($section['section_type'] === 'custom_html'): ?>
                                <div class="form-group">
                                    <label>Custom HTML (EN):</label>
                                    <textarea class="tinymce" name="content[html_en]"><?php echo htmlspecialchars($content['html_en'] ?? ''); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Custom HTML (BN):</label>
                                    <textarea class="tinymce" name="content[html_bn]"><?php echo htmlspecialchars($content['html_bn'] ?? ''); ?></textarea>
                                </div>
                            <?php endif; ?>
                            <div class="form-group">
                                <label>Display Order:</label>
                                <input type="number" name="display_order" class="form-control" value="<?php echo $section['display_order']; ?>" style="width: 100px;">
                            </div>
                        </div>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<script>
$(function() {
    // Initialize Sortable
    $("#sortable-sections").sortable({
        handle: ".drag-handle",
        placeholder: "ui-state-highlight",
        update: function(event, ui) {
            var sectionOrder = $(this).sortable('toArray', { attribute: 'data-id' });
            var pageId = <?php echo $page_id; ?>;

            $.ajax({
                url: 'handle_section_actions.php',
                type: 'POST',
                data: {
                    action: 'reorder_sections',
                    page_id: pageId,
                    section_order: sectionOrder
                },
                success: function(response) {
                    console.log("Reorder successful:", response);
                    // Optionally, show a success message to the user
                },
                error: function(xhr, status, error) {
                    console.error("Reorder failed:", status, error);
                    // Optionally, show an error message
                }
            });
        }
    });
    $("#sortable-sections").disableSelection();

    // Section Collapse/Expand with Local Storage
    $('.toggle-collapse').on('click', function() {
        var targetId = $(this).data('bs-target');
        var isCollapsed = $(targetId).hasClass('show');
        var sectionId = $(this).closest('.section-item').data('id');

        if (isCollapsed) {
            localStorage.setItem('collapseState-' + sectionId, 'collapsed');
            $(this).find('i').removeClass('bi-chevron-up').addClass('bi-chevron-down');
        } else {
            localStorage.setItem('collapseState-' + sectionId, 'expanded');
            $(this).find('i').removeClass('bi-chevron-down').addClass('bi-chevron-up');
        }
    });

    // Restore collapse state on page load
    $('.section-item').each(function() {
        var sectionId = $(this).data('id');
        var collapseState = localStorage.getItem('collapseState-' + sectionId);
        var targetId = '#collapseSection' + sectionId;
        var toggleButton = $(this).find('.toggle-collapse');

        if (collapseState === 'collapsed') {
            $(targetId).removeClass('show');
            toggleButton.find('i').removeClass('bi-chevron-up').addClass('bi-chevron-down');
        } else {
            $(targetId).addClass('show');
            toggleButton.find('i').removeClass('bi-chevron-down').addClass('bi-chevron-up');
        }
    });
});
</script>

<?php require_once '../includes/admin_footer.php'; ?>