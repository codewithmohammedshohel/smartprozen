<?php
// Template for Custom HTML Block section
// $section_data contains 'html_en' and 'html_bn'

// Determine current language (example, assuming a global $current_lang variable or similar)
$current_lang = defined('CURRENT_LANG') ? CURRENT_LANG : 'en'; // Default to English

$html_content = '';
if ($current_lang === 'bn' && !empty($section_data['html_bn'])) {
    $html_content = $section_data['html_bn'];
} elseif (!empty($section_data['html_en'])) {
    $html_content = $section_data['html_en'];
} else {
    // Fallback if no content for selected language, or if both are empty
    $html_content = $section_data['html_bn'] ?? $section_data['html_en'] ?? '';
}

if (!empty($html_content)) {
    echo '<div class="container my-5 custom-html-section">';
    echo $html_content; // Output the raw HTML
    echo '</div>';
}
?>