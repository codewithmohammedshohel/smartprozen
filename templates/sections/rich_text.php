<?php
// Decode the JSON content string
$content = json_decode($section['content'] ?? '{}', true) ?: [];

// Get the translated text block
$text_content = get_translated_text($content, 'text') ?: '<p>No content available.</p>';
?>

<section class="section-rich-text" data-aos="fade-up">
    <div class="container content-area">
        <?php 
        // We do NOT use htmlspecialchars() here because we want to render the HTML
        // tags that were saved by the WYSIWYG editor. The content should be
        // sanitized on input (TinyMCE does this) to prevent XSS attacks.
        echo $text_content; 
        ?>
    </div>
</section>