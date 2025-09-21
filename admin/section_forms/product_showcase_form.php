<?php
// This file provides the form for managing the 'product_showcase' section content.
// It will be included in the admin panel when editing a 'product_showcase' section.

// Ensure $section_data is available, passed from handle_section.php
$section_title = get_translated_text($section_data, 'title') ?? '';
$section_subtitle = get_translated_text($section_data, 'subtitle') ?? '';
$showcase_items = $section_data['showcase_items'] ?? [];

// Get available languages from config or settings
$available_languages = ['en', 'bn']; // Assuming English and Bengali are available

?>

<div class="card mb-4">
    <div class="card-header">
        Product Showcase Section Configuration
    </div>
    <div class="card-body">
        <div class="mb-3">
            <label for="sectionTitle" class="form-label">Section Title (JSON)</label>
            <textarea class="form-control" id="sectionTitle" name="content_json[title]" rows="2" required><?php echo htmlspecialchars(json_encode($section_data['title'] ?? ['en' => ''], JSON_UNESCAPED_UNICODE)); ?></textarea>
            <small class="form-text text-muted">Enter title as JSON, e.g., {"en": "Our Product Showcase", "bn": "আমাদের পণ্যের প্রদর্শনী"}</small>
        </div>
        <div class="mb-3">
            <label for="sectionSubtitle" class="form-label">Section Subtitle (JSON)</label>
            <textarea class="form-control" id="sectionSubtitle" name="content_json[subtitle]" rows="2"><?php echo htmlspecialchars(json_encode($section_data['subtitle'] ?? ['en' => ''], JSON_UNESCAPED_UNICODE)); ?></textarea>
            <small class="form-text text-muted">Enter subtitle as JSON, e.g., {"en": "See our products in action", "bn": "আমাদের পণ্যগুলি কার্যক্ষেত্রে দেখুন"}</small>
        </div>

        <hr>
        <h5>Showcase Items</h5>
        <div id="showcase-items-list">
            <?php if (!empty($showcase_items)): ?>
                <?php foreach ($showcase_items as $index => $item): ?>
                    <div class="showcase-item card mb-3" data-index="<?php echo $index; ?>">
                        <div class="card-body">
                            <h6 class="card-title">Showcase Item #<span class="item-index-display"><?php echo $index + 1; ?></span></h6>
                            <div class="mb-3">
                                <label for="itemImage_<?php echo $index; ?>" class="form-label">Main Image Filename</label>
                                <input type="text" class="form-control" id="itemImage_<?php echo $index; ?>" name="content_json[showcase_items][<?php echo $index; ?>][image_filename]" value="<?php echo htmlspecialchars($item['image_filename'] ?? ''); ?>">
                                <small class="form-text text-muted">e.g., product_screenshot_1.png (from media library)</small>
                            </div>
                            <?php foreach ($available_languages as $lang): ?>
                                <div class="mb-3">
                                    <label for="itemTitle_<?php echo $index; ?>_<?php echo $lang; ?>" class="form-label">Item Title (<?php echo strtoupper($lang); ?>)</label>
                                    <input type="text" class="form-control" id="itemTitle_<?php echo $index; ?>_<?php echo $lang; ?>" name="content_json[showcase_items][<?php echo $index; ?>][title][<?php echo $lang; ?>]" value="<?php echo htmlspecialchars($item['title'][$lang] ?? ''); ?>" required>
                                </div>
                            <?php endforeach; ?>
                            <?php foreach ($available_languages as $lang): ?>
                                <div class="mb-3">
                                    <label for="itemDescription_<?php echo $index; ?>_<?php echo $lang; ?>" class="form-label">Item Description (<?php echo strtoupper($lang); ?>)</label>
                                    <textarea class="form-control" id="itemDescription_<?php echo $index; ?>_<?php echo $lang; ?>" name="content_json[showcase_items][<?php echo $index; ?>][description][<?php echo $lang; ?>]" rows="3" required><?php echo htmlspecialchars($item['description'][$lang] ?? ''); ?></textarea>
                                </div>
                            <?php endforeach; ?>
                            <div class="mb-3">
                                <label for="itemBeforeImage_<?php echo $index; ?>" class="form-label">Before Image Filename (Optional)</label>
                                <input type="text" class="form-control" id="itemBeforeImage_<?php echo $index; ?>" name="content_json[showcase_items][<?php echo $index; ?>][before_image_filename]" value="<?php echo htmlspecialchars($item['before_image_filename'] ?? ''); ?>">
                                <small class="form-text text-muted">e.g., before_cms.png</small>
                            </div>
                            <div class="mb-3">
                                <label for="itemAfterImage_<?php echo $index; ?>" class="form-label">After Image Filename (Optional)</label>
                                <input type="text" class="form-control" id="itemAfterImage_<?php echo $index; ?>" name="content_json[showcase_items][<?php echo $index; ?>][after_image_filename]" value="<?php echo htmlspecialchars($item['after_image_filename'] ?? ''); ?>">
                                <small class="form-text text-muted">e.g., after_cms.png</small>
                            </div>
                            <button type="button" class="btn btn-danger btn-sm remove-showcase-item">Remove Item</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <button type="button" class="btn btn-primary mt-3" id="add-showcase-item">Add New Showcase Item</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const showcaseItemsList = document.getElementById('showcase-items-list');
    const addShowcaseItemBtn = document.getElementById('add-showcase-item');

    let itemIndex = showcaseItemsList.children.length;

    function updateItemIndices() {
        Array.from(showcaseItemsList.children).forEach((item, idx) => {
            item.setAttribute('data-index', idx);
            item.querySelector('.item-index-display').textContent = idx + 1;
            item.querySelectorAll('[name^="content_json[showcase_items]"]').forEach(input => {
                const name = input.getAttribute('name');
                input.setAttribute('name', name.replace(/content_json\[showcase_items\]\[\d+\]/, `content_json[showcase_items][${idx}]`));
                input.setAttribute('id', input.getAttribute('id').replace(/_\d+_/, `_${idx}_`));
            });
        });
    }

    addShowcaseItemBtn.addEventListener('click', function() {
        const newItemHtml = `
            <div class="showcase-item card mb-3" data-index="${itemIndex}">
                <div class="card-body">
                    <h6 class="card-title">Showcase Item #<span class="item-index-display">${itemIndex + 1}</span></h6>
                    <div class="mb-3">
                        <label for="itemImage_${itemIndex}" class="form-label">Main Image Filename</label>
                        <input type="text" class="form-control" id="itemImage_${itemIndex}" name="content_json[showcase_items][${itemIndex}][image_filename]" value="">
                        <small class="form-text text-muted">e.g., product_screenshot_1.png (from media library)</small>
                    </div>
                    <?php foreach ($available_languages as $lang): ?>
                        <div class="mb-3">
                            <label for="itemTitle_${itemIndex}_<?php echo $lang; ?>" class="form-label">Item Title (<?php echo strtoupper($lang); ?>)</label>
                            <input type="text" class="form-control" id="itemTitle_${itemIndex}_<?php echo $lang; ?>" name="content_json[showcase_items][${itemIndex}][title][<?php echo $lang; ?>]" value="" required>
                        </div>
                    <?php endforeach; ?>
                    <?php foreach ($available_languages as $lang): ?>
                        <div class="mb-3">
                            <label for="itemDescription_${itemIndex}_<?php echo $lang; ?>" class="form-label">Item Description (<?php echo strtoupper($lang); ?>)</label>
                            <textarea class="form-control" id="itemDescription_${itemIndex}_<?php echo $lang; ?>" name="content_json[showcase_items][${itemIndex}][description][<?php echo $lang; ?>]" rows="3" required></textarea>
                        </div>
                    <?php endforeach; ?>
                    <div class="mb-3">
                        <label for="itemBeforeImage_${itemIndex}" class="form-label">Before Image Filename (Optional)</label>
                        <input type="text" class="form-control" id="itemBeforeImage_${itemIndex}" name="content_json[showcase_items][${itemIndex}][before_image_filename]" value="">
                        <small class="form-text text-muted">e.g., before_cms.png</small>
                    </div>
                    <div class="mb-3">
                        <label for="itemAfterImage_${itemIndex}" class="form-label">After Image Filename (Optional)</label>
                        <input type="text" class="form-control" id="itemAfterImage_${itemIndex}" name="content_json[showcase_items][${itemIndex}][after_image_filename]" value="">
                        <small class="form-text text-muted">e.g., after_cms.png</small>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm remove-showcase-item">Remove Item</button>
                </div>
            </div>
        `;
        showcaseItemsList.insertAdjacentHTML('beforeend', newItemHtml);
        itemIndex++;
        updateItemIndices();
    });

    showcaseItemsList.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-showcase-item')) {
            e.target.closest('.showcase-item').remove();
            updateItemIndices();
        }
    });

    // Initial update of indices in case of pre-existing items
    updateItemIndices();
});
</script>
