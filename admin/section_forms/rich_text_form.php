<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../core/db.php';
require_once __DIR__ . '/../../core/functions.php';

if (!is_admin_logged_in() || !has_permission('manage_pages')) {
    exit('Unauthorized');
}

$section_id = $_GET['section_id'] ?? null;
$page_id = $_GET['page_id'] ?? null;
$section_data = [];

if ($section_id) {
    $stmt = $conn->prepare("SELECT content_json FROM page_sections WHERE id = ? AND page_id = ?");
    $stmt->bind_param("ii", $section_id, $page_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($section = $result->fetch_assoc()) {
        $section_data = json_decode($section['content_json'], true);
    }
    $stmt->close();
}

$text_content = $section_data['text']['en'] ?? ''; // Assuming English for now

?>

<form id="richTextSectionForm">
    <input type="hidden" name="section_id" value="<?php echo htmlspecialchars($section_id); ?>">
    <input type="hidden" name="page_id" value="<?php echo htmlspecialchars($page_id); ?>">
    <input type="hidden" name="section_type" value="rich_text">

    <div class="mb-3">
        <label for="richTextContent" class="form-label">Content (HTML/Rich Text)</label>
        <textarea class="form-control tinymce" id="richTextContent" name="content_json[text][en]" rows="10"><?php echo htmlspecialchars($text_content); ?></textarea>
    </div>
</form>

<script>
    // Initialize TinyMCE for this textarea
    tinymce.remove(); // Remove any existing TinyMCE instances
    tinymce.init({
        selector: '#richTextContent',
        plugins: 'advlist autolink lists link image charmap print preview anchor \
                  searchreplace visualblocks code fullscreen insertdatetime media table paste code help wordcount',
        toolbar: 'undo redo | formatselect | bold italic backcolor | \
                  alignleft aligncenter alignright alignjustify | \
                  bullist numlist outdent indent | removeformat | help',
        height: 300
    });
</script>