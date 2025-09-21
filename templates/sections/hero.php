<?php
// Decode the JSON content string from the database
$content = json_decode($section['content'] ?? '{}', true) ?: [];

// Access the content, providing default values if not set
$title = $content['title'] ?? 'Welcome';
$subtitle = $content['subtitle'] ?? 'Your ultimate online shopping destination';
$button_text = $content['button_text'] ?? 'Get Started';
$button_link = $content['button_link'] ?? '#';

// Get media files
$image_filename = $content['image_filename'] ?? '';
$video_url = $content['video_url'] ?? '';

// Logic to create a video embed if a URL is provided
$video_embed_html = '';
if (!empty($video_url)) {
    // Check for YouTube URL
    if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video_url, $match)) {
        $youtube_id = $match[1];
        $video_embed_html = '<iframe src="https://www.youtube.com/embed/'.$youtube_id.'?autoplay=1&mute=1&loop=1&playlist='.$youtube_id.'&controls=0&showinfo=0&autohide=1" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>';
    }
    // Add similar logic for Vimeo if needed
}
?>

<section class="section-hero" data-aos="fade-in">
    <div class="hero-background">
        <?php if (!empty($video_embed_html)): ?>
            <div class="video-background-container">
                <?php echo $video_embed_html; ?>
            </div>
        <?php elseif (!empty($image_filename)): ?>
            <div class="image-background" style="background-image: url('<?php echo SITE_URL . '/uploads/media/' . htmlspecialchars($image_filename); ?>');"></div>
        <?php endif; ?>
        <div class="hero-overlay"></div>
    </div>
    <div class="hero-content container">
        <h1 class="hero-title" data-aos="fade-up" data-aos-delay="100"><?php echo htmlspecialchars($title); ?></h1>
        <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="200"><?php echo htmlspecialchars($subtitle); ?></p>
        <?php if (!empty($button_text)): ?>
            <a href="<?php echo htmlspecialchars($button_link); ?>" class="btn btn-hero" data-aos="fade-up" data-aos-delay="300"><?php echo htmlspecialchars($button_text); ?></a>
        <?php endif; ?>
    </div>
    <!-- Placeholder for product mockups - User should update this with actual images via admin panel -->
    <div class="hero-mockups d-none d-lg-block">
        <img src="<?php echo SITE_URL; ?>/uploads/media/default.png" alt="Product Mockup" class="img-fluid">
    </div>
</section>