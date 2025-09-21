<section class="section-testimonials py-5 bg-light">
    <div class="container">
        <?php
        // Decode the JSON content string from the database
        $content = json_decode($section['content_json'] ?? '{}', true) ?: [];

        $section_title = get_translated_text($content, 'title') ?: 'What Our Customers Say';
        $section_subtitle = get_translated_text($content, 'subtitle') ?: 'Hear from our happy clients.';
        $testimonial_ids = $content['testimonial_ids'] ?? [];

        $testimonials = [];
        if (!empty($testimonial_ids)) {
            $ids_placeholder = implode(',', array_fill(0, count($testimonial_ids), '?'));
            $stmt = $conn->prepare("SELECT * FROM testimonials WHERE id IN ($ids_placeholder) AND is_approved = 1 ORDER BY FIELD(id, $ids_placeholder)");
            $types = str_repeat('i', count($testimonial_ids));
            $stmt->bind_param($types, ...$testimonial_ids);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $testimonials[] = $row;
            }
            $stmt->close();
        }
        ?>

        <div class="text-center mb-5">
            <h2 class="display-4 fw-bold mb-3" data-aos="fade-up"><?php echo htmlspecialchars($section_title); ?></h2>
            <p class="lead text-muted" data-aos="fade-up" data-aos-delay="100"><?php echo htmlspecialchars($section_subtitle); ?></p>
        </div>

        <?php if (!empty($testimonials)): ?>
            <div id="testimonialsCarousel" class="carousel slide" data-bs-ride="carousel" data-aos="fade-up" data-aos-delay="200">
                <div class="carousel-inner pb-5">
                    <?php foreach ($testimonials as $index => $testimonial): ?>
                        <div class="carousel-item <?php echo ($index === 0) ? 'active' : ''; ?>">
                            <div class="d-flex justify-content-center">
                                <div class="testimonial-card text-center p-4 mx-auto">
                                    <img src="<?php echo SITE_URL . '/uploads/media/' . htmlspecialchars($testimonial['author_image'] ?? 'placeholder_user.png'); ?>" class="rounded-circle mb-3 shadow-sm" alt="<?php echo htmlspecialchars($testimonial['author_name']); ?>" width="90" height="90">
                                    <div class="star-rating mb-3">
                                        <?php for ($i = 0; $i < 5; $i++): ?>
                                            <i class="bi <?php echo ($i < $testimonial['rating']) ? 'bi-star-fill text-warning' : 'bi-star'; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <p class="lead fst-italic mb-3">"<?php echo htmlspecialchars($testimonial['testimonial_text']); ?>"</p>
                                    <h5 class="fw-bold mb-0"><?php echo htmlspecialchars($testimonial['author_name']); ?></h5>
                                    <p class="text-muted"><?php echo htmlspecialchars($testimonial['author_title']); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
                <div class="carousel-indicators position-relative mt-4">
                    <?php foreach ($testimonials as $index => $testimonial): ?>
                        <button type="button" data-bs-target="#testimonialsCarousel" data-bs-slide-to="<?php echo $index; ?>" class="<?php echo ($index === 0) ? 'active' : ''; ?>" aria-current="true" aria-label="Slide <?php echo $index + 1; ?>"></button>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center" data-aos="fade-up">
                No testimonials found or approved yet. Please add some from the admin panel.
            </div>
        <?php endif; ?>
    </div>
</section>
