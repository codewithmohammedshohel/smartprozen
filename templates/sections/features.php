<section class="section-features py-5">
    <div class="container">
        <?php
        // Decode the JSON content string from the database
        $content = json_decode($section['content_json'] ?? '{}', true) ?: [];

        $section_title = get_translated_text($content, 'title') ?: 'Our Awesome Features';
        $section_subtitle = get_translated_text($content, 'subtitle') ?: 'Discover what makes us stand out.';
        $features = $content['features'] ?? [];
        ?>

        <div class="text-center mb-5">
            <h2 class="display-4 fw-bold mb-3" data-aos="fade-up"><?php echo htmlspecialchars($section_title); ?></h2>
            <p class="lead text-muted" data-aos="fade-up" data-aos-delay="100"><?php echo htmlspecialchars($section_subtitle); ?></p>
        </div>

        <?php if (!empty($features)): ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($features as $index => $feature): ?>
                    <div class="col" data-aos="fade-up" data-aos-delay="<?php echo 200 + ($index * 100); ?>">
                        <div class="card feature-card h-100 shadow-sm border-0">
                            <div class="card-body text-center p-4">
                                <?php if (!empty($feature['icon'])): ?>
                                    <div class="feature-icon mb-3 mx-auto d-flex align-items-center justify-content-center">
                                        <i class="<?php echo htmlspecialchars($feature['icon']); ?>"></i>
                                    </div>
                                <?php endif; ?>
                                <h3 class="h5 fw-bold mb-2"><?php echo htmlspecialchars(get_translated_text($feature, 'title')); ?></h3>
                                <p class="text-muted mb-0"><?php echo htmlspecialchars(get_translated_text($feature, 'description')); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center" data-aos="fade-up">
                No features defined for this section yet. Please add some from the admin panel.
            </div>
        <?php endif; ?>
    </div>
</section>
