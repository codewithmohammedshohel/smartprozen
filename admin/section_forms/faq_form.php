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

$faq_items = $section_data['items'] ?? [];

?>

<form id="faqSectionForm">
    <input type="hidden" name="section_id" value="<?php echo htmlspecialchars($section_id); ?>">
    <input type="hidden" name="page_id" value="<?php echo htmlspecialchars($page_id); ?>">
    <input type="hidden" name="section_type" value="faq">

    <div id="faqItemsContainer">
        <?php if (!empty($faq_items)): ?>
            <?php foreach ($faq_items as $index => $item): ?>
                <div class="faq-item card mb-3 p-3">
                    <div class="mb-3">
                        <label for="question_<?php echo $index; ?>" class="form-label">Question (English)</label>
                        <input type="text" class="form-control" id="question_<?php echo $index; ?>" name="content_json[items][<?php echo $index; ?>][question][en]" value="<?php echo htmlspecialchars($item['question']['en'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="answer_<?php echo $index; ?>" class="form-label">Answer (English)</label>
                        <textarea class="form-control" id="answer_<?php echo $index; ?>" name="content_json[items][<?php echo $index; ?>][answer][en]" rows="3"><?php echo htmlspecialchars($item['answer']['en'] ?? ''); ?></textarea>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm remove-faq-item">Remove</button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <button type="button" class="btn btn-primary btn-sm" id="addFaqItem">Add FAQ Item</button>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let faqItemIndex = <?php echo count($faq_items); ?>;

        document.getElementById('addFaqItem').addEventListener('click', function() {
            addFaqItem();
        });

        document.getElementById('faqItemsContainer').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-faq-item')) {
                e.target.closest('.faq-item').remove();
            }
        });

        function addFaqItem(question = '', answer = '') {
            const container = document.getElementById('faqItemsContainer');
            const newItemHtml = `
                <div class="faq-item card mb-3 p-3">
                    <div class="mb-3">
                        <label for="question_${faqItemIndex}" class="form-label">Question (English)</label>
                        <input type="text" class="form-control" id="question_${faqItemIndex}" name="content_json[items][${faqItemIndex}][question][en]" value="${question}">
                    </div>
                    <div class="mb-3">
                        <label for="answer_${faqItemIndex}" class="form-label">Answer (English)</label>
                        <textarea class="form-control" id="answer_${faqItemIndex}" name="content_json[items][${faqItemIndex}][answer][en]" rows="3">${answer}</textarea>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm remove-faq-item">Remove</button>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', newItemHtml);
            faqItemIndex++;
        }
    });
</script>