<section class="section-product-showcase py-5">
    <div class="container">
        <?php
        // Decode the JSON content string from the database
        $content = json_decode($section['content_json'] ?? '{}', true) ?: [];

        $section_title = get_translated_text($content, 'title') ?: 'Product Showcase';
        $section_subtitle = get_translated_text($content, 'subtitle') ?: 'See our products in action.';
        $showcase_items = $content['showcase_items'] ?? [];
        ?>

        <div class="text-center mb-5">
            <h2 class="display-4 fw-bold mb-3" data-aos="fade-up"><?php echo htmlspecialchars($section_title); ?></h2>
            <p class="lead text-muted" data-aos="fade-up" data-aos-delay="100"><?php echo htmlspecialchars($section_subtitle); ?></p>
        </div>

        <?php if (!empty($showcase_items)): ?>
            <div id="productShowcaseCarousel" class="carousel slide" data-bs-ride="carousel" data-aos="fade-up" data-aos-delay="200">
                <div class="carousel-inner">
                    <?php foreach ($showcase_items as $index => $item): ?>
                        <div class="carousel-item <?php echo ($index === 0) ? 'active' : ''; ?>">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <img src="<?php echo SITE_URL . '/uploads/media/' . htmlspecialchars($item['image_filename'] ?? ''); ?>" class="d-block w-100 rounded shadow-lg" alt="<?php echo htmlspecialchars(get_translated_text($item, 'title')); ?>">
                                </div>
                                <div class="col-md-6 mt-4 mt-md-0">
                                    <h3 class="h4 fw-bold mb-3"><?php echo htmlspecialchars(get_translated_text($item, 'title')); ?></h3>
                                    <p class="text-muted"><?php echo htmlspecialchars(get_translated_text($item, 'description')); ?></p>
                                    <?php if (!empty($item['before_image_filename']) && !empty($item['after_image_filename'])): ?>
                                        <div class="comparison-visual mt-4">
                                            <h5 class="fw-bold">Before & After</h5>
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <img src="<?php echo SITE_URL . '/uploads/media/' . htmlspecialchars($item['before_image_filename']); ?>" class="img-fluid rounded" alt="Before">
                                                    <small class="d-block text-center mt-1">Before</small>
                                                </div>
                                                <div class="col-6">
                                                    <img src="<?php echo SITE_URL . '/uploads/media/' . htmlspecialchars($item['after_image_filename']); ?>" class="img-fluid rounded" alt="After">
                                                    <small class="d-block text-center mt-1">After</small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#productShowcaseCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#productShowcaseCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center" data-aos="fade-up">
                No product showcase items defined for this section yet. Please add some from the admin panel.
            </div>
        <?php endif; ?>
    </div>
</section>
