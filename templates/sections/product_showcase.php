<?php
// Product Showcase Section Template
$content = json_decode($section['content_json'] ?? '{}', true) ?: [];

$section_title = $content['title'] ?? 'Featured Products';
$section_subtitle = $content['subtitle'] ?? 'Discover our most popular items';
$product_count = $content['product_count'] ?? 6;
$show_featured_only = $content['show_featured_only'] ?? true;
?>

<section class="section-product-showcase py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-4 fw-bold mb-3" data-aos="fade-up"><?php echo htmlspecialchars($section_title); ?></h2>
            <p class="lead text-muted" data-aos="fade-up" data-aos-delay="100"><?php echo htmlspecialchars($section_subtitle); ?></p>
        </div>

        <?php
        // Fetch products
        $where_clause = $show_featured_only ? "AND is_featured = 1" : "";
        $products_query = $conn->query("SELECT p.*, pc.name as category_name, pc.slug as category_slug 
                                       FROM products p 
                                       LEFT JOIN product_categories pc ON p.category_id = pc.id 
                                       WHERE p.is_published = 1 $where_clause
                                       ORDER BY p.created_at DESC LIMIT $product_count");
        
        if ($products_query && $products_query->num_rows > 0):
        ?>
        <div class="row g-4">
            <?php
            $delay = 0;
            while ($product = $products_query->fetch_assoc()):
                $product_image = !empty($product['featured_image']) ? 
                    SITE_URL . '/uploads/media/thumb-' . $product['featured_image'] : 
                    'https://placehold.co/600x600/efefef/333?text=' . urlencode($product['name']);
                $product_url = SITE_URL . '/product.php?id=' . $product['id'];
                $sale_price = !empty($product['sale_price']) ? $product['sale_price'] : $product['price'];
                $original_price = !empty($product['sale_price']) ? $product['price'] : null;
            ?>
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                <div class="card product-card h-100 shadow-sm">
                    <div class="position-relative">
                        <a href="<?php echo $product_url; ?>">
                            <img src="<?php echo $product_image; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>" style="height: 250px; object-fit: cover;">
                        </a>
                        <?php if ($original_price): ?>
                            <span class="badge bg-danger position-absolute top-0 start-0 m-2">Sale</span>
                        <?php endif; ?>
                        <?php if ($product['is_featured']): ?>
                            <span class="badge bg-success position-absolute top-0 end-0 m-2">Featured</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="mb-2">
                            <?php if (!empty($product['category_name'])): ?>
                                <small class="text-muted"><?php echo htmlspecialchars($product['category_name']); ?></small>
                            <?php endif; ?>
                        </div>
                        <h5 class="card-title">
                            <a href="<?php echo $product_url; ?>" class="text-decoration-none text-dark"><?php echo htmlspecialchars($product['name']); ?></a>
                        </h5>
                        <?php if (!empty($product['short_description'])): ?>
                            <p class="card-text text-muted small"><?php echo htmlspecialchars(substr($product['short_description'], 0, 80)) . '...'; ?></p>
                        <?php endif; ?>
                        <div class="mt-auto">
                            <div class="d-flex align-items-center mb-2">
                                <span class="price fw-bold text-primary fs-5">$<?php echo number_format($sale_price, 2); ?></span>
                                <?php if ($original_price): ?>
                                    <span class="text-muted text-decoration-line-through ms-2">$<?php echo number_format($original_price, 2); ?></span>
                                <?php endif; ?>
                            </div>
                            <button class="btn btn-primary w-100 add-to-cart-btn" 
                                    data-product-id="<?php echo $product['id']; ?>" 
                                    data-product-name="<?php echo htmlspecialchars($product['name']); ?>" 
                                    data-product-price="<?php echo $sale_price; ?>">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $delay += 100;
            endwhile;
            ?>
        </div>
        
        <div class="text-center mt-5">
            <a href="<?php echo SITE_URL; ?>/products_list.php" class="btn btn-outline-primary btn-lg">View All Products</a>
        </div>
        
        <?php else: ?>
        <div class="text-center py-5">
            <div class="alert alert-info">
                <h4>No products found</h4>
                <p>No products are currently available. Please check back later or contact us for more information.</p>
                <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-primary">Contact Us</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<style>
.product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.product-card .card-img-top {
    transition: transform 0.3s ease;
}

.product-card:hover .card-img-top {
    transform: scale(1.05);
}

.add-to-cart-btn {
    transition: all 0.3s ease;
}

.add-to-cart-btn:hover {
    transform: translateY(-2px);
}
</style>