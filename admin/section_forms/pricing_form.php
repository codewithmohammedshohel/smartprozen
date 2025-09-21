<?php
// This file provides the form for managing the 'pricing' section content.
// It will be included in the admin panel when editing a 'pricing' section.

// Ensure $section_data is available, passed from handle_section.php
$section_title = get_translated_text($section_data, 'title') ?? '';
$section_subtitle = get_translated_text($section_data, 'subtitle') ?? '';
$pricing_plans = $section_data['pricing_plans'] ?? [];

// Get available languages from config or settings
$available_languages = ['en', 'bn']; // Assuming English and Bengali are available

?>

<div class="card mb-4">
    <div class="card-header">
        Pricing Section Configuration
    </div>
    <div class="card-body">
        <div class="mb-3">
            <label for="sectionTitle" class="form-label">Section Title (JSON)</label>
            <textarea class="form-control" id="sectionTitle" name="content_json[title]" rows="2" required><?php echo htmlspecialchars(json_encode($section_data['title'] ?? ['en' => ''], JSON_UNESCAPED_UNICODE)); ?></textarea>
            <small class="form-text text-muted">Enter title as JSON, e.g., {"en": "Simple, Transparent Pricing", "bn": "সহজ, স্বচ্ছ মূল্য"}</small>
        </div>
        <div class="mb-3">
            <label for="sectionSubtitle" class="form-label">Section Subtitle (JSON)</label>
            <textarea class="form-control" id="sectionSubtitle" name="content_json[subtitle]" rows="2"><?php echo htmlspecialchars(json_encode($section_data['subtitle'] ?? ['en' => ''], JSON_UNESCAPED_UNICODE)); ?></textarea>
            <small class="form-text text-muted">Enter subtitle as JSON, e.g., {"en": "Choose the plan that's right for you.", "bn": "আপনার জন্য সঠিক পরিকল্পনাটি বেছে নিন।"}</small>
        </div>

        <hr>
        <h5>Pricing Plans</h5>
        <div id="pricing-plans-list">
            <?php if (!empty($pricing_plans)): ?>
                <?php foreach ($pricing_plans as $index => $plan): ?>
                    <div class="pricing-plan-item card mb-3" data-index="<?php echo $index; ?>">
                        <div class="card-body">
                            <h6 class="card-title">Plan #<span class="plan-index-display"><?php echo $index + 1; ?></span></h6>
                            <?php foreach ($available_languages as $lang): ?>
                                <div class="mb-3">
                                    <label for="planTitle_<?php echo $index; ?>_<?php echo $lang; ?>" class="form-label">Plan Title (<?php echo strtoupper($lang); ?>)</label>
                                    <input type="text" class="form-control" id="planTitle_<?php echo $index; ?>_<?php echo $lang; ?>" name="content_json[pricing_plans][<?php echo $index; ?>][title][<?php echo $lang; ?>]" value="<?php echo htmlspecialchars($plan['title'][$lang] ?? ''); ?>" required>
                                </div>
                            <?php endforeach; ?>
                            <div class="mb-3">
                                <label for="planPrice_<?php echo $index; ?>" class="form-label">Price</label>
                                <input type="number" step="0.01" class="form-control" id="planPrice_<?php echo $index; ?>" name="content_json[pricing_plans][<?php echo $index; ?>][price]" value="<?php echo htmlspecialchars($plan['price'] ?? ''); ?>" required>
                            </div>
                            <?php foreach ($available_languages as $lang): ?>
                                <div class="mb-3">
                                    <label for="planBillingPeriod_<?php echo $index; ?>_<?php echo $lang; ?>" class="form-label">Billing Period (<?php echo strtoupper($lang); ?>)</label>
                                    <input type="text" class="form-control" id="planBillingPeriod_<?php echo $index; ?>_<?php echo $lang; ?>" name="content_json[pricing_plans][<?php echo $index; ?>][billing_period][<?php echo $lang; ?>]" value="<?php echo htmlspecialchars($plan['billing_period'][$lang] ?? ''); ?>" required>
                                </div>
                            <?php endforeach; ?>
                            <?php foreach ($available_languages as $lang): ?>
                                <div class="mb-3">
                                    <label for="planDescription_<?php echo $index; ?>_<?php echo $lang; ?>" class="form-label">Description (<?php echo strtoupper($lang); ?>)</label>
                                    <textarea class="form-control" id="planDescription_<?php echo $index; ?>_<?php echo $lang; ?>" name="content_json[pricing_plans][<?php echo $index; ?>][description][<?php echo $lang; ?>]" rows="2" required><?php echo htmlspecialchars($plan['description'][$lang] ?? ''); ?></textarea>
                                </div>
                            <?php endforeach; ?>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="planIsPopular_<?php echo $index; ?>" name="content_json[pricing_plans][<?php echo $index; ?>][is_popular]" value="1" <?php echo ($plan['is_popular'] ?? false) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="planIsPopular_<?php echo $index; ?>">Mark as Most Popular</label>
                            </div>
                            <div class="mb-3">
                                <label for="planButtonText_<?php echo $index; ?>" class="form-label">Button Text (JSON)</label>
                                <textarea class="form-control" id="planButtonText_<?php echo $index; ?>" name="content_json[pricing_plans][<?php echo $index; ?>][button_text]" rows="1" required><?php echo htmlspecialchars(json_encode($plan['button_text'] ?? ['en' => 'Choose Plan'], JSON_UNESCAPED_UNICODE)); ?></textarea>
                                <small class="form-text text-muted">e.g., {"en": "Choose Plan", "bn": "পরিকল্পনা নির্বাচন করুন"}</small>
                            </div>
                            <div class="mb-3">
                                <label for="planButtonLink_<?php echo $index; ?>" class="form-label">Button Link</label>
                                <input type="text" class="form-control" id="planButtonLink_<?php echo $index; ?>" name="content_json[pricing_plans][<?php echo $index; ?>][button_link]" value="<?php echo htmlspecialchars($plan['button_link'] ?? ''); ?>" required>
                            </div>

                            <h6>Plan Features</h6>
                            <div id="plan-features-list-<?php echo $index; ?>">
                                <?php if (!empty($plan['features'])): ?>
                                    <?php foreach ($plan['features'] as $feature_index => $feature): ?>
                                        <div class="plan-feature-item input-group mb-2">
                                            <?php foreach ($available_languages as $lang): ?>
                                                <input type="text" class="form-control" placeholder="Feature (<?php echo strtoupper($lang); ?>)" name="content_json[pricing_plans][<?php echo $index; ?>][features][<?php echo $feature_index; ?>][text][<?php echo $lang; ?>]" value="<?php echo htmlspecialchars($feature['text'][$lang] ?? ''); ?>" required>
                                            <?php endforeach; ?>
                                            <button type="button" class="btn btn-outline-danger remove-plan-feature">Remove</button>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <button type="button" class="btn btn-sm btn-success mt-2 add-plan-feature" data-plan-index="<?php echo $index; ?>">Add Feature</button>

                            <button type="button" class="btn btn-danger btn-sm remove-pricing-plan mt-3">Remove Plan</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <button type="button" class="btn btn-primary mt-3" id="add-pricing-plan">Add New Pricing Plan</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const pricingPlansList = document.getElementById('pricing-plans-list');
    const addPricingPlanBtn = document.getElementById('add-pricing-plan');

    let planIndex = pricingPlansList.children.length;

    function updatePlanIndices() {
        Array.from(pricingPlansList.children).forEach((planItem, pIdx) => {
            planItem.setAttribute('data-index', pIdx);
            planItem.querySelector('.plan-index-display').textContent = pIdx + 1;
            planItem.querySelectorAll('[name^="content_json[pricing_plans]"]').forEach(input => {
                const name = input.getAttribute('name');
                input.setAttribute('name', name.replace(/content_json\[pricing_plans\]\[\d+\]/, `content_json[pricing_plans][${pIdx}]`));
                input.setAttribute('id', input.getAttribute('id').replace(/_\d+_/, `_${pIdx}_`));
            });
            planItem.querySelector('.add-plan-feature').setAttribute('data-plan-index', pIdx);

            // Update feature indices within each plan
            const planFeaturesList = planItem.querySelector(`#plan-features-list-${pIdx}`);
            if (planFeaturesList) {
                Array.from(planFeaturesList.children).forEach((featureItem, fIdx) => {
                    featureItem.querySelectorAll('[name^="content_json[pricing_plans]"]').forEach(input => {
                        const name = input.getAttribute('name');
                        input.setAttribute('name', name.replace(/content_json\[pricing_plans\]\[\d+\]\[features\]\[\d+\]/, `content_json[pricing_plans][${pIdx}][features][${fIdx}]`));
                    });
                });
            }
        });
    }

    addPricingPlanBtn.addEventListener('click', function() {
        const newPlanHtml = `
            <div class="pricing-plan-item card mb-3" data-index="${planIndex}">
                <div class="card-body">
                    <h6 class="card-title">Plan #<span class="plan-index-display">${planIndex + 1}</span></h6>
                    <?php foreach ($available_languages as $lang): ?>
                        <div class="mb-3">
                            <label for="planTitle_${planIndex}_<?php echo $lang; ?>" class="form-label">Plan Title (<?php echo strtoupper($lang); ?>)</label>
                            <input type="text" class="form-control" id="planTitle_${planIndex}_<?php echo $lang; ?>" name="content_json[pricing_plans][${planIndex}][title][<?php echo $lang; ?>]" value="" required>
                        </div>
                    <?php endforeach; ?>
                    <div class="mb-3">
                        <label for="planPrice_${planIndex}" class="form-label">Price</label>
                        <input type="number" step="0.01" class="form-control" id="planPrice_${planIndex}" name="content_json[pricing_plans][${planIndex}][price]" value="" required>
                    </div>
                    <?php foreach ($available_languages as $lang): ?>
                        <div class="mb-3">
                            <label for="planBillingPeriod_${planIndex}_<?php echo $lang; ?>" class="form-label">Billing Period (<?php echo strtoupper($lang); ?>)</label>
                            <input type="text" class="form-control" id="planBillingPeriod_${planIndex}_<?php echo $lang; ?>" name="content_json[pricing_plans][${planIndex}][billing_period][<?php echo $lang; ?>]" value="" required>
                        </div>
                    <?php endforeach; ?>
                    <?php foreach ($available_languages as $lang): ?>
                        <div class="mb-3">
                            <label for="planDescription_${planIndex}_<?php echo $lang; ?>" class="form-label">Description (<?php echo strtoupper($lang); ?>)</label>
                            <textarea class="form-control" id="planDescription_${planIndex}_<?php echo $lang; ?>" name="content_json[pricing_plans][${planIndex}][description][<?php echo $lang; ?>]" rows="2" required></textarea>
                        </div>
                    <?php endforeach; ?>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="planIsPopular_${planIndex}" name="content_json[pricing_plans][${planIndex}][is_popular]" value="1">
                        <label class="form-check-label" for="planIsPopular_${planIndex}">Mark as Most Popular</label>
                    </div>
                    <div class="mb-3">
                        <label for="planButtonText_${planIndex}" class="form-label">Button Text (JSON)</label>
                        <textarea class="form-control" id="planButtonText_${planIndex}" name="content_json[pricing_plans][${planIndex}][button_text]" rows="1" required>{\"en\": \"Choose Plan\"}</textarea>
                        <small class="form-text text-muted">e.g., {\"en\": \"Choose Plan\", \"bn\": \"পরিকল্পনা নির্বাচন করুন\"}</small>
                    </div>
                    <div class="mb-3">
                        <label for="planButtonLink_${planIndex}" class="form-label">Button Link</label>
                        <input type="text" class="form-control" id="planButtonLink_${planIndex}" name="content_json[pricing_plans][${planIndex}][button_link]" value="#" required>
                    </div>

                    <h6>Plan Features</h6>
                    <div id="plan-features-list-${planIndex}">
                        <!-- Features will be added here dynamically -->
                    </div>
                    <button type="button" class="btn btn-sm btn-success mt-2 add-plan-feature" data-plan-index="${planIndex}">Add Feature</button>

                    <button type="button" class="btn btn-danger btn-sm remove-pricing-plan mt-3">Remove Plan</button>
                </div>
            </div>
        `;
        pricingPlansList.insertAdjacentHTML('beforeend', newPlanHtml);
        planIndex++;
        updatePlanIndices();
    });

    pricingPlansList.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-pricing-plan')) {
            e.target.closest('.pricing-plan-item').remove();
            updatePlanIndices();
        } else if (e.target.classList.contains('add-plan-feature')) {
            const currentPlanIndex = e.target.dataset.planIndex;
            const planFeaturesContainer = document.getElementById(`plan-features-list-${currentPlanIndex}`);
            let featureCount = planFeaturesContainer.children.length;
            const newFeatureHtml = `
                <div class="plan-feature-item input-group mb-2">
                    <?php foreach ($available_languages as $lang): ?>
                        <input type="text" class="form-control" placeholder="Feature (<?php echo strtoupper($lang); ?>)" name="content_json[pricing_plans][${currentPlanIndex}][features][${featureCount}][text][<?php echo $lang; ?>]" value="" required>
                    <?php endforeach; ?>
                    <button type="button" class="btn btn-outline-danger remove-plan-feature">Remove</button>
                </div>
            `;
            planFeaturesContainer.insertAdjacentHTML('beforeend', newFeatureHtml);
        } else if (e.target.classList.contains('remove-plan-feature')) {
            e.target.closest('.plan-feature-item').remove();
            updatePlanIndices(); // Re-index all plans and their features
        }
    });

    // Initial update of indices in case of pre-existing items
    updatePlanIndices();
});
</script>