<?php
// This file provides the form for managing the 'features' section content.
// It will be included in the admin panel when editing a 'features' section.

// Ensure $section_data is available, passed from handle_section.php
$section_title = get_translated_text($section_data, 'title') ?? '';
$section_subtitle = get_translated_text($section_data, 'subtitle') ?? '';
$features = $section_data['features'] ?? [];

// Get available languages from config or settings
$available_languages = ['en', 'bn']; // Assuming English and Bengali are available

?>

<div class="card mb-4">
    <div class="card-header">
        Features Section Configuration
    </div>
    <div class="card-body">
        <div class="mb-3">
            <label for="sectionTitle" class="form-label">Section Title (JSON)</label>
            <textarea class="form-control" id="sectionTitle" name="content_json[title]" rows="2" required><?php echo htmlspecialchars(json_encode($section_data['title'] ?? ['en' => ''], JSON_UNESCAPED_UNICODE)); ?></textarea>
            <small class="form-text text-muted">Enter title as JSON, e.g., {"en": "Our Features", "bn": "আমাদের বৈশিষ্ট্য"}</small>
        </div>
        <div class="mb-3">
            <label for="sectionSubtitle" class="form-label">Section Subtitle (JSON)</label>
            <textarea class="form-control" id="sectionSubtitle" name="content_json[subtitle]" rows="2"><?php echo htmlspecialchars(json_encode($section_data['subtitle'] ?? ['en' => ''], JSON_UNESCAPED_UNICODE)); ?></textarea>
            <small class="form-text text-muted">Enter subtitle as JSON, e.g., {"en": "Discover what makes us unique", "bn": "আমাদের অনন্যতা আবিষ্কার করুন"}</small>
        </div>

        <hr>
        <h5>Features List</h5>
        <div id="features-list">
            <?php if (!empty($features)): ?>
                <?php foreach ($features as $index => $feature): ?>
                    <div class="feature-item card mb-3" data-index="<?php echo $index; ?>">
                        <div class="card-body">
                            <h6 class="card-title">Feature #<span class="feature-index-display"><?php echo $index + 1; ?></span></h6>
                            <div class="mb-3">
                                <label for="featureIcon_<?php echo $index; ?>" class="form-label">Icon Class (e.g., bi bi-star)</label>
                                <input type="text" class="form-control" id="featureIcon_<?php echo $index; ?>" name="content_json[features][<?php echo $index; ?>][icon]" value="<?php echo htmlspecialchars($feature['icon'] ?? ''); ?>">
                                <small class="form-text text-muted">Use Bootstrap Icons, FontAwesome, etc.</small>
                            </div>
                            <?php foreach ($available_languages as $lang): ?>
                                <div class="mb-3">
                                    <label for="featureTitle_<?php echo $index; ?>_<?php echo $lang; ?>" class="form-label">Feature Title (<?php echo strtoupper($lang); ?>)</label>
                                    <input type="text" class="form-control" id="featureTitle_<?php echo $index; ?>_<?php echo $lang; ?>" name="content_json[features][<?php echo $index; ?>][title][<?php echo $lang; ?>]" value="<?php echo htmlspecialchars($feature['title'][$lang] ?? ''); ?>" required>
                                </div>
                            <?php endforeach; ?>
                            <?php foreach ($available_languages as $lang): ?>
                                <div class="mb-3">
                                    <label for="featureDescription_<?php echo $index; ?>_<?php echo $lang; ?>" class="form-label">Feature Description (<?php echo strtoupper($lang); ?>)</label>
                                    <textarea class="form-control" id="featureDescription_<?php echo $index; ?>_<?php echo $lang; ?>" name="content_json[features][<?php echo $index; ?>][description][<?php echo $lang; ?>]" rows="3" required><?php echo htmlspecialchars($feature['description'][$lang] ?? ''); ?></textarea>
                                </div>
                            <?php endforeach; ?>
                            <button type="button" class="btn btn-danger btn-sm remove-feature">Remove Feature</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <button type="button" class="btn btn-primary mt-3" id="add-feature">Add New Feature</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const featuresList = document.getElementById('features-list');
    const addFeatureBtn = document.getElementById('add-feature');

    let featureIndex = featuresList.children.length;

    function updateFeatureIndices() {
        Array.from(featuresList.children).forEach((item, idx) => {
            item.setAttribute('data-index', idx);
            item.querySelector('.feature-index-display').textContent = idx + 1;
            item.querySelectorAll('[name^="content_json[features]"]').forEach(input => {
                const name = input.getAttribute('name');
                input.setAttribute('name', name.replace(/content_json\[features\]\[\d+\]/, `content_json[features][${idx}]`));
                input.setAttribute('id', input.getAttribute('id').replace(/_\d+_/, `_${idx}_`));
            });
        });
    }

    addFeatureBtn.addEventListener('click', function() {
        const newFeatureHtml = `
            <div class="feature-item card mb-3" data-index="${featureIndex}">
                <div class="card-body">
                    <h6 class="card-title">Feature #<span class="feature-index-display">${featureIndex + 1}</span></h6>
                    <div class="mb-3">
                        <label for="featureIcon_${featureIndex}" class="form-label">Icon Class (e.g., bi bi-star)</label>
                        <input type="text" class="form-control" id="featureIcon_${featureIndex}" name="content_json[features][${featureIndex}][icon]" value="">
                        <small class="form-text text-muted">Use Bootstrap Icons, FontAwesome, etc.</small>
                    </div>
                    <?php foreach ($available_languages as $lang): ?>
                        <div class="mb-3">
                            <label for="featureTitle_${featureIndex}_<?php echo $lang; ?>" class="form-label">Feature Title (<?php echo strtoupper($lang); ?>)</label>
                            <input type="text" class="form-control" id="featureTitle_${featureIndex}_<?php echo $lang; ?>" name="content_json[features][${featureIndex}][title][<?php echo $lang; ?>]" value="" required>
                        </div>
                    <?php endforeach; ?>
                    <?php foreach ($available_languages as $lang): ?>
                        <div class="mb-3">
                            <label for="featureDescription_${featureIndex}_<?php echo $lang; ?>" class="form-label">Feature Description (<?php echo strtoupper($lang); ?>)</label>
                            <textarea class="form-control" id="featureDescription_${featureIndex}_<?php echo $lang; ?>" name="content_json[features][${featureIndex}][description][<?php echo $lang; ?>]" rows="3" required></textarea>
                        </div>
                    <?php endforeach; ?>
                    <button type="button" class="btn btn-danger btn-sm remove-feature">Remove Feature</button>
                </div>
            </div>
        `;
        featuresList.insertAdjacentHTML('beforeend', newFeatureHtml);
        featureIndex++;
        updateFeatureIndices();
    });

    featuresList.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-feature')) {
            e.target.closest('.feature-item').remove();
            updateFeatureIndices();
        }
    });

    // Initial update of indices in case of pre-existing items
    updateFeatureIndices();
});
</script>
