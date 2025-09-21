<section class="section-pricing py-5">
    <div class="container">
        <?php
        // Decode the JSON content string from the database
        $content = json_decode($section['content_json'] ?? '{}', true) ?: [];

        $section_title = get_translated_text($content, 'title') ?: 'Simple, Transparent Pricing';
        $section_subtitle = get_translated_text($content, 'subtitle') ?: 'Choose the plan that's right for you.';
        $pricing_plans = $content['pricing_plans'] ?? [];
        ?>

        <div class="text-center mb-5">
            <h2 class="display-4 fw-bold mb-3" data-aos="fade-up"><?php echo htmlspecialchars($section_title); ?></h2>
            <p class="lead text-muted" data-aos="fade-up" data-aos-delay="100"><?php echo htmlspecialchars($section_subtitle); ?></p>
        </div>

        <?php if (!empty($pricing_plans)): ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 justify-content-center">
                <?php foreach ($pricing_plans as $index => $plan): ?>
                    <div class="col" data-aos="fade-up" data-aos-delay="<?php echo 200 + ($index * 100); ?>">
                        <div class="card pricing-card h-100 shadow-sm border-0 <?php echo ($plan['is_popular'] ?? false) ? 'popular-plan' : ''; ?>">
                            <?php if ($plan['is_popular'] ?? false): ?>
                                <div class="popular-badge">Most Popular</div>
                            <?php endif; ?>
                            <div class="card-body p-4 d-flex flex-column">
                                <h3 class="h5 fw-bold mb-3"><?php echo htmlspecialchars(get_translated_text($plan, 'title')); ?></h3>
                                <div class="d-flex align-items-baseline mb-3">
                                    <span class="display-4 fw-bold">$<?php echo htmlspecialchars($plan['price']); ?></span>
                                    <span class="text-muted">/<?php echo htmlspecialchars(get_translated_text($plan, 'billing_period')); ?></span>
                                </div>
                                <p class="text-muted mb-4"><?php echo htmlspecialchars(get_translated_text($plan, 'description')); ?></p>
                                <ul class="list-unstyled flex-grow-1">
                                    <?php foreach (($plan['features'] ?? []) as $feature): ?>
                                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i><?php echo htmlspecialchars(get_translated_text($feature, 'text')); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <a href="<?php echo htmlspecialchars($plan['button_link'] ?? '#'); ?>" class="btn btn-primary mt-4 pricing-cta-btn"><?php echo htmlspecialchars(get_translated_text($plan, 'button_text') ?: 'Choose Plan'); ?></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center" data-aos="fade-up">
                No pricing plans defined for this section yet. Please add some from the admin panel.
            </div>
        <?php endif; ?>
    </div>
</section>
