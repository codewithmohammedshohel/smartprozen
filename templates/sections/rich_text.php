<?php
// Rich Text Section Template
$content = json_decode($section['content_json'] ?? '{}', true) ?: [];

$section_title = $content['title'] ?? 'Content Section';
$section_content = $content['content'] ?? '<p>This section is waiting for content.</p>';
?>

<section class="section-rich-text py-5">
    <div class="container">
        <?php if (!empty($section_title)): ?>
        <div class="text-center mb-5">
            <h2 class="display-4 fw-bold mb-3" data-aos="fade-up"><?php echo htmlspecialchars($section_title); ?></h2>
        </div>
        <?php endif; ?>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="content-body" data-aos="fade-up" data-aos-delay="100">
                    <?php echo $section_content; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.section-rich-text .content-body {
    font-size: 1.1rem;
    line-height: 1.7;
}

.section-rich-text .content-body h1,
.section-rich-text .content-body h2,
.section-rich-text .content-body h3,
.section-rich-text .content-body h4,
.section-rich-text .content-body h5,
.section-rich-text .content-body h6 {
    margin-top: 2rem;
    margin-bottom: 1rem;
    font-weight: 600;
}

.section-rich-text .content-body p {
    margin-bottom: 1.5rem;
}

.section-rich-text .content-body ul,
.section-rich-text .content-body ol {
    margin-bottom: 1.5rem;
    padding-left: 2rem;
}

.section-rich-text .content-body blockquote {
    border-left: 4px solid var(--bs-primary);
    padding-left: 1.5rem;
    margin: 2rem 0;
    font-style: italic;
    background-color: #f8f9fa;
    padding: 1.5rem;
    border-radius: 0.5rem;
}
</style>