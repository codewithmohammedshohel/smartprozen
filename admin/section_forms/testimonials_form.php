<?php
// This file provides the form for managing the 'testimonials' section content.
// It will be included in the admin panel when editing a 'testimonials' section.

// Ensure $section_data is available, passed from handle_section.php
$section_title = get_translated_text($section_data, 'title') ?? '';
$section_subtitle = get_translated_text($section_data, 'subtitle') ?? '';
$testimonial_ids_str = implode(', ', $section_data['testimonial_ids'] ?? []);

// Get available languages from config or settings
$available_languages = ['en', 'bn']; // Assuming English and Bengali are available

?>

<div class="card mb-4">
    <div class="card-header">
        Testimonials Section Configuration
    </div>
    <div class="card-body">
        <div class="mb-3">
            <label for="sectionTitle" class="form-label">Section Title (JSON)</label>
            <textarea class="form-control" id="sectionTitle" name="content_json[title]" rows="2" required><?php echo htmlspecialchars(json_encode($section_data['title'] ?? ['en' => ''], JSON_UNESCAPED_UNICODE)); ?></textarea>
            <small class="form-text text-muted">Enter title as JSON, e.g., {"en": "What Our Customers Say", "bn": "আমাদের গ্রাহকরা কী বলেন"}</small>
        </div>
        <div class="mb-3">
            <label for="sectionSubtitle" class="form-label">Section Subtitle (JSON)</label>
            <textarea class="form-control" id="sectionSubtitle" name="content_json[subtitle]" rows="2"><?php echo htmlspecialchars(json_encode($section_data['subtitle'] ?? ['en' => ''], JSON_UNESCAPED_UNICODE)); ?></textarea>
            <small class="form-text text-muted">Enter subtitle as JSON, e.g., {"en": "Hear from our happy clients.", "bn": "আমাদের খুশি গ্রাহকদের কাছ থেকে শুনুন।"}</small>
        </div>

        <hr>
        <h5>Select Testimonials</h5>
        <div class="mb-3">
            <label for="testimonialIds" class="form-label">Testimonial IDs (Comma Separated)</label>
            <input type="text" class="form-control" id="testimonialIds" name="content_json[testimonial_ids]" value="<?php echo htmlspecialchars($testimonial_ids_str); ?>">
            <small class="form-text text-muted">Enter a comma-separated list of testimonial IDs to display (e.g., 1, 3, 5). Only approved testimonials will be shown.</small>
        </div>
    </div>
</div>
