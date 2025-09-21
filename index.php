<?php
require_once 'config.php';
require_once 'core/db.php';
require_once 'core/functions.php';

// This file acts as a fallback homepage if a custom one is not defined in the database.

// Check if a custom home page exists in the database
$stmt = $conn->prepare("SELECT id FROM pages WHERE slug = 'home'");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) { // Re-enabled check for custom homepage
    // If it exists, render it using page.php logic
    include 'page.php';
} else {
    // Otherwise, show the default themed homepage
    $page_title = __('welcome_to_smartprozen');
    include 'includes/header.php';
?>
    <?php if (is_admin_logged_in()): ?>
    <div class="container my-3">
        <div class="alert alert-info text-center">
            <strong>Admin Notice:</strong> You are viewing the default theme homepage. To create a custom, editable homepage, <a href="admin/manage_pages.php?action=add&slug=home" class="alert-link">click here</a>.
        </div>
    </div>
    <?php endif; ?>

<!-- 1. Hero Section -->
<section class="hero-section text-center text-white bg-dark py-5">
    <div class="container py-5">
        <h1 class="display-4 fw-bold mb-3" data-aos="fade-up">Smart Tech, Simplified Living.</h1>
        <p class="lead mb-4" data-aos="fade-up" data-aos-delay="100">Discover our curated collection of smart gadgets and professional accessories designed to elevate your everyday.</p>
        <a href="#best-sellers" class="btn btn-primary btn-lg" data-aos="fade-up" data-aos-delay="200">Shop Now</a>
    </div>
</section>

<!-- 2. Featured Categories Section -->
<section class="featured-categories-section py-5">
    <div class="container">
        <h2 class="text-center fw-bold mb-5">Explore Our Collections</h2>
        <div class="row g-4">
            <?php
            // Fetch categories from database with all missing columns
            $categories_query = $conn->query("SELECT * FROM product_categories WHERE is_active = 1 ORDER BY display_order ASC");
            if ($categories_query && $categories_query->num_rows > 0) {
                $delay = 100;
                while ($category = $categories_query->fetch_assoc()) {
                    $category_image = !empty($category['image']) ? SITE_URL . '/uploads/categories/' . $category['image'] : 'https://placehold.co/600x400/343a40/ffffff?text=' . urlencode($category['name']);
                    $category_url = SITE_URL . '/products_list.php?category=' . $category['slug'];
                    ?>
                    <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                        <div class="card category-card text-white h-100">
                            <img src="<?php echo $category_image; ?>" class="card-img" alt="<?php echo htmlspecialchars($category['name']); ?>" style="height: 250px; object-fit: cover;">
                            <div class="card-img-overlay d-flex flex-column justify-content-end p-4">
                                <h5 class="card-title fw-bold"><?php echo htmlspecialchars($category['name']); ?></h5>
                                <?php if (!empty($category['description'])): ?>
                                    <p class="card-text small"><?php echo htmlspecialchars(substr($category['description'], 0, 80)) . '...'; ?></p>
                                <?php endif; ?>
                                <a href="<?php echo $category_url; ?>" class="btn btn-light btn-sm mt-2">Shop Now</a>
                            </div>
                        </div>
                    </div>
                    <?php
                    $delay += 100;
                }
            } else {
                // Fallback categories if none in database
                $fallback_categories = [
                    ['name' => 'Smart Home Devices', 'description' => 'Transform your home with intelligent devices', 'slug' => 'smart-home'],
                    ['name' => 'Professional Audio', 'description' => 'Premium audio equipment for professionals', 'slug' => 'audio'],
                    ['name' => 'Mobile Accessories', 'description' => 'Essential accessories for mobile devices', 'slug' => 'mobile-accessories'],
                    ['name' => 'Wearable Tech', 'description' => 'Smart watches and fitness trackers', 'slug' => 'wearables']
                ];
                $delay = 100;
                foreach ($fallback_categories as $category) {
                    ?>
                    <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                        <div class="card category-card text-white h-100">
                            <img src="https://placehold.co/600x400/343a40/ffffff?text=<?php echo urlencode($category['name']); ?>" class="card-img" alt="<?php echo htmlspecialchars($category['name']); ?>" style="height: 250px; object-fit: cover;">
                            <div class="card-img-overlay d-flex flex-column justify-content-end p-4">
                                <h5 class="card-title fw-bold"><?php echo htmlspecialchars($category['name']); ?></h5>
                                <p class="card-text small"><?php echo htmlspecialchars($category['description']); ?></p>
                                <a href="<?php echo SITE_URL; ?>/products_list.php?category=<?php echo $category['slug']; ?>" class="btn btn-light btn-sm mt-2">Shop Now</a>
                            </div>
                        </div>
                    </div>
                    <?php
                    $delay += 100;
                }
            }
            ?>
        </div>
    </div>
</section>

<!-- 3. Best Sellers Section -->
<section id="best-sellers" class="best-sellers-section py-5 bg-light">
    <div class="container">
        <h2 class="text-center fw-bold mb-5">Our Most Popular Products</h2>
        <div class="row g-4">
            <?php
            // Fetch featured products from database with proper images
            $products_query = $conn->query("SELECT p.*, pc.name as category_name, pc.slug as category_slug 
                                           FROM products p 
                                           LEFT JOIN product_categories pc ON p.category_id = pc.id 
                                           WHERE p.is_featured = 1 AND p.is_published = 1 
                                           ORDER BY p.created_at DESC LIMIT 6");
            if ($products_query && $products_query->num_rows > 0) {
                $delay = 0;
                while ($product = $products_query->fetch_assoc()) {
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
                                <span class="badge bg-success position-absolute top-0 end-0 m-2">Featured</span>
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
                                    <button class="btn btn-primary w-100 add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>" data-product-name="<?php echo htmlspecialchars($product['name']); ?>" data-product-price="<?php echo $sale_price; ?>">
                                        Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    $delay += 100;
                }
            } else {
                // Fallback products if none in database
                $fallback_products = [
                    ['name' => 'ZenBuds Pro 3', 'price' => 89.99, 'sale_price' => 79.99, 'description' => 'Premium wireless earbuds', 'image' => 'https://placehold.co/600x600/efefef/333?text=ZenBuds+Pro+3'],
                    ['name' => 'SmartGlow Ambient Light', 'price' => 59.99, 'sale_price' => 49.99, 'description' => 'Smart LED light', 'image' => 'https://placehold.co/600x600/efefef/333?text=SmartGlow+Light'],
                    ['name' => 'ProCharge Wireless Stand', 'price' => 45.00, 'sale_price' => null, 'description' => 'Fast wireless charging', 'image' => 'https://placehold.co/600x600/efefef/333?text=ProCharge+Stand']
                ];
                $delay = 0;
                foreach ($fallback_products as $product) {
                    ?>
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                        <div class="card product-card h-100 shadow-sm">
                            <div class="position-relative">
                                <img src="<?php echo $product['image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>" style="height: 250px; object-fit: cover;">
                                <?php if ($product['sale_price']): ?>
                                    <span class="badge bg-danger position-absolute top-0 start-0 m-2">Sale</span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text text-muted small"><?php echo htmlspecialchars($product['description']); ?></p>
                                <div class="mt-auto">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="price fw-bold text-primary fs-5">$<?php echo number_format($product['sale_price'] ?: $product['price'], 2); ?></span>
                                        <?php if ($product['sale_price']): ?>
                                            <span class="text-muted text-decoration-line-through ms-2">$<?php echo number_format($product['price'], 2); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <button class="btn btn-primary w-100 add-to-cart-btn">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    $delay += 100;
                }
            }
            ?>
        </div>
        <div class="text-center mt-5">
            <a href="<?php echo SITE_URL; ?>/products_list.php" class="btn btn-outline-primary">View All Products</a>
        </div>
    </div>
</section>

<!-- 4. Why Shop With Us? Section -->
<section class="why-shop-with-us-section py-5">
    <div class="container">
        <h2 class="text-center fw-bold mb-5">The SmartProZen Advantage</h2>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3 text-center" data-aos="fade-up">
                <div class="feature-icon mb-3"><i class="bi bi-gem fs-2 text-primary"></i></div>
                <h5 class="fw-bold">Premium Quality Guaranteed</h5>
                <p class="text-muted">We source and test every product to ensure it meets our high standards for performance and durability.</p>
            </div>
            <div class="col-md-6 col-lg-3 text-center" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-icon mb-3"><i class="bi bi-truck fs-2 text-primary"></i></div>
                <h5 class="fw-bold">Fast & Free Shipping</h5>
                <p class="text-muted">Get your order delivered to your doorstep quickly. Free shipping on all orders over $50.</p>
            </div>
            <div class="col-md-6 col-lg-3 text-center" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-icon mb-3"><i class="bi bi-shield-check fs-2 text-primary"></i></div>
                <h5 class="fw-bold">Secure Checkout</h5>
                <p class="text-muted">Your privacy and security are our top priority. Shop with confidence using our encrypted payment system.</p>
            </div>
            <div class="col-md-6 col-lg-3 text-center" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-icon mb-3"><i class="bi bi-headset fs-2 text-primary"></i></div>
                <h5 class="fw-bold">24/7 Customer Support</h5>
                <p class="text-muted">Our dedicated support team is here to help you around the clock with any questions or concerns.</p>
            </div>
        </div>
    </div>
</section>

<!-- 5. Customer Reviews Section -->
<section class="customer-reviews-section py-5 bg-light">
    <div class="container">
        <h2 class="text-center fw-bold mb-5">Loved by Professionals Like You</h2>
        <div class="row g-4">
            <?php
            // Fetch testimonials from database with all missing columns
            $testimonials_query = $conn->query("SELECT * FROM testimonials WHERE is_featured = 1 AND is_published = 1 ORDER BY created_at DESC LIMIT 4");
            if ($testimonials_query && $testimonials_query->num_rows > 0) {
                $animation_delay = 0;
                while ($testimonial = $testimonials_query->fetch_assoc()) {
                    $animation_class = $animation_delay % 2 == 0 ? 'fade-right' : 'fade-left';
                    ?>
                    <div class="col-lg-6" data-aos="<?php echo $animation_class; ?>" data-aos-delay="<?php echo $animation_delay * 100; ?>">
                        <div class="card h-100">
                            <div class="card-body p-4">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <?php if (!empty($testimonial['avatar'])): ?>
                                            <img src="<?php echo SITE_URL . '/uploads/avatars/' . $testimonial['avatar']; ?>" class="rounded-circle" width="60" height="60" alt="<?php echo htmlspecialchars($testimonial['name']); ?>">
                                        <?php else: ?>
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                <i class="bi bi-person fs-4"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="bi bi-star-fill text-warning <?php echo $i <= $testimonial['rating'] ? '' : 'text-muted'; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <p class="mb-3">"<?php echo htmlspecialchars($testimonial['testimonial']); ?>"</p>
                                        <footer class="blockquote-footer">
                                            <strong><?php echo htmlspecialchars($testimonial['name']); ?></strong>
                                            <?php if (!empty($testimonial['position']) && !empty($testimonial['company'])): ?>
                                                , <cite title="Source Title"><?php echo htmlspecialchars($testimonial['position']); ?> at <?php echo htmlspecialchars($testimonial['company']); ?></cite>
                                            <?php elseif (!empty($testimonial['position'])): ?>
                                                , <cite title="Source Title"><?php echo htmlspecialchars($testimonial['position']); ?></cite>
                                            <?php elseif (!empty($testimonial['company'])): ?>
                                                , <cite title="Source Title"><?php echo htmlspecialchars($testimonial['company']); ?></cite>
                                            <?php endif; ?>
                                        </footer>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    $animation_delay++;
                }
            } else {
                // Fallback testimonials if none in database
                $fallback_testimonials = [
                    ['name' => 'Mark Thompson', 'position' => 'Audio Engineer', 'company' => 'AudioEngine Studios', 'rating' => 5, 'testimonial' => 'The ZenBuds Pro are the best wireless earbuds I\'ve ever used. The sound quality is incredible for the price, and they are so comfortable.'],
                    ['name' => 'Sarah Kim', 'position' => 'Creative Director', 'company' => 'Design Co.', 'rating' => 5, 'testimonial' => 'My order from SmartProZen arrived in just two days! The packaging was great and the SmartGlow light looks amazing on my desk. 10/10 would shop again.']
                ];
                $animation_delay = 0;
                foreach ($fallback_testimonials as $testimonial) {
                    $animation_class = $animation_delay % 2 == 0 ? 'fade-right' : 'fade-left';
                    ?>
                    <div class="col-lg-6" data-aos="<?php echo $animation_class; ?>" data-aos-delay="<?php echo $animation_delay * 100; ?>">
                        <div class="card h-100">
                            <div class="card-body p-4">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                            <i class="bi bi-person fs-4"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="bi bi-star-fill text-warning <?php echo $i <= $testimonial['rating'] ? '' : 'text-muted'; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <p class="mb-3">"<?php echo htmlspecialchars($testimonial['testimonial']); ?>"</p>
                                        <footer class="blockquote-footer">
                                            <strong><?php echo htmlspecialchars($testimonial['name']); ?></strong>, <cite title="Source Title"><?php echo htmlspecialchars($testimonial['position']); ?> at <?php echo htmlspecialchars($testimonial['company']); ?></cite>
                                        </footer>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    $animation_delay++;
                }
            }
            ?>
        </div>
    </div>
</section>

<!-- 6. Special Offers Section -->
<section class="special-offers-section py-5">
    <div class="container">
        <h2 class="text-center fw-bold mb-5">Special Offers & Coupons</h2>
        <div class="row g-4">
            <?php
            // Fetch active coupons from database with all missing columns
            $coupons_query = $conn->query("SELECT * FROM coupons WHERE is_active = 1 ORDER BY created_at DESC LIMIT 4");
            if ($coupons_query && $coupons_query->num_rows > 0) {
                $delay = 0;
                while ($coupon = $coupons_query->fetch_assoc()) {
                    $is_valid = true;
                    $valid_from = !empty($coupon['valid_from']) ? strtotime($coupon['valid_from']) : null;
                    $valid_until = !empty($coupon['valid_until']) ? strtotime($coupon['valid_until']) : null;
                    $now = time();
                    
                    if ($valid_from && $now < $valid_from) $is_valid = false;
                    if ($valid_until && $now > $valid_until) $is_valid = false;
                    
                    $discount_text = $coupon['discount_type'] == 'percentage' ? 
                        $coupon['discount_value'] . '% OFF' : 
                        '$' . $coupon['discount_value'] . ' OFF';
                    ?>
                    <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                        <div class="card coupon-card h-100 border-success">
                            <div class="card-body text-center">
                                <div class="coupon-code mb-3">
                                    <h4 class="text-success fw-bold"><?php echo htmlspecialchars($coupon['code']); ?></h4>
                                </div>
                                <h5 class="card-title text-primary"><?php echo $discount_text; ?></h5>
                                <?php if (!empty($coupon['description'])): ?>
                                    <p class="card-text small"><?php echo htmlspecialchars($coupon['description']); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($coupon['minimum_amount'])): ?>
                                    <p class="small text-muted">Min. order: $<?php echo number_format($coupon['minimum_amount'], 2); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($coupon['maximum_discount'])): ?>
                                    <p class="small text-muted">Max. discount: $<?php echo number_format($coupon['maximum_discount'], 2); ?></p>
                                <?php endif; ?>
                                <div class="mt-3">
                                    <small class="text-muted">
                                        Used: <?php echo $coupon['used_count']; ?>/<?php echo $coupon['usage_limit'] ?: '∞'; ?> times
                                    </small>
                                </div>
                                <?php if (!$is_valid): ?>
                                    <div class="mt-2">
                                        <span class="badge bg-warning">Expired</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php
                    $delay += 100;
                }
            } else {
                // Fallback coupons if none in database
                $fallback_coupons = [
                    ['code' => 'WELCOME10', 'discount_type' => 'percentage', 'discount_value' => 10, 'description' => 'Welcome discount for new customers', 'minimum_amount' => 50],
                    ['code' => 'SAVE20', 'discount_type' => 'percentage', 'discount_value' => 20, 'description' => 'Save 20% on orders over $100', 'minimum_amount' => 100]
                ];
                $delay = 0;
                foreach ($fallback_coupons as $coupon) {
                    $discount_text = $coupon['discount_type'] == 'percentage' ? 
                        $coupon['discount_value'] . '% OFF' : 
                        '$' . $coupon['discount_value'] . ' OFF';
                    ?>
                    <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                        <div class="card coupon-card h-100 border-success">
                            <div class="card-body text-center">
                                <div class="coupon-code mb-3">
                                    <h4 class="text-success fw-bold"><?php echo htmlspecialchars($coupon['code']); ?></h4>
                                </div>
                                <h5 class="card-title text-primary"><?php echo $discount_text; ?></h5>
                                <p class="card-text small"><?php echo htmlspecialchars($coupon['description']); ?></p>
                                <p class="small text-muted">Min. order: $<?php echo number_format($coupon['minimum_amount'], 2); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php
                    $delay += 100;
                }
            }
            ?>
        </div>
    </div>
</section>

<!-- 6. Newsletter Signup Section -->
<section class="newsletter-signup-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 text-center">
                <h2 class="fw-bold mb-3">Join the Zen Zone</h2>
                <p class="text-muted mb-4">Sign up for our newsletter to get 10% OFF your first order, plus exclusive access to new product drops and special deals.</p>
                <form class="d-flex">
                    <input type="email" class="form-control form-control-lg me-2" placeholder="Enter your email address">
                    <button type="submit" class="btn btn-primary">Subscribe</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- 7. Newsletter Signup Section -->
<section class="newsletter-signup-section py-5 bg-primary text-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 text-center">
                <h2 class="fw-bold mb-3">Join the Zen Zone</h2>
                <p class="mb-4">Sign up for our newsletter to get 10% OFF your first order, plus exclusive access to new product drops and special deals.</p>
                <form class="d-flex" action="<?php echo SITE_URL; ?>/newsletter/subscribe.php" method="POST">
                    <input type="email" name="email" class="form-control form-control-lg me-2" placeholder="Enter your email address" required>
                    <button type="submit" class="btn btn-light">Subscribe</button>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
/* Enhanced Homepage Styles */
.category-card {
    transition: transform 0.3s ease;
    border: none;
    overflow: hidden;
}

.category-card:hover {
    transform: translateY(-5px);
}

.category-card .card-img {
    transition: transform 0.3s ease;
}

.category-card:hover .card-img {
    transform: scale(1.1);
}

.product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}

.coupon-card {
    transition: transform 0.3s ease;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.coupon-card:hover {
    transform: translateY(-3px);
}

.coupon-code {
    background: #28a745;
    color: white;
    padding: 10px;
    border-radius: 5px;
    margin: -15px -15px 15px -15px;
}

.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.hero-section .container {
    position: relative;
    z-index: 2;
}

.badge {
    font-size: 0.75rem;
}

/* Animation delays for staggered effects */
[data-aos] {
    transition-duration: 0.8s;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .hero-section h1 {
        font-size: 2.5rem;
    }
    
    .hero-section .lead {
        font-size: 1.1rem;
    }
}
</style>

<script>
// Add to cart functionality
document.addEventListener('DOMContentLoaded', function() {
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');
            const productPrice = this.getAttribute('data-product-price');
            
            if (productId && productName && productPrice) {
                // Add to cart via AJAX
                fetch('<?php echo SITE_URL; ?>/cart/add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `product_id=${productId}&quantity=1`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        this.innerHTML = '<i class="bi bi-check"></i> Added!';
                        this.classList.remove('btn-primary');
                        this.classList.add('btn-success');
                        
                        // Update cart count in header if exists
                        const cartCount = document.querySelector('.cart-count');
                        if (cartCount) {
                            cartCount.textContent = data.cart_count;
                        }
                        
                        // Reset button after 2 seconds
                        setTimeout(() => {
                            this.innerHTML = 'Add to Cart';
                            this.classList.remove('btn-success');
                            this.classList.add('btn-primary');
                        }, 2000);
                    } else {
                        alert('Error adding to cart: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error adding to cart. Please try again.');
                });
            } else {
                // Fallback for static buttons
                this.innerHTML = '<i class="bi bi-check"></i> Added!';
                this.classList.remove('btn-primary');
                this.classList.add('btn-success');
                
                setTimeout(() => {
                    this.innerHTML = 'Add to Cart';
                    this.classList.remove('btn-success');
                    this.classList.add('btn-primary');
                }, 2000);
            }
        });
    });
    
    // Newsletter form submission
    const newsletterForm = document.querySelector('.newsletter-signup-section form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = this.querySelector('input[type="email"]').value;
            const button = this.querySelector('button[type="submit"]');
            
            // Disable button and show loading
            button.disabled = true;
            button.innerHTML = '<i class="bi bi-hourglass-split"></i> Subscribing...';
            
            fetch('<?php echo SITE_URL; ?>/newsletter/subscribe.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `email=${encodeURIComponent(email)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    button.innerHTML = '<i class="bi bi-check"></i> Subscribed!';
                    button.classList.remove('btn-light');
                    button.classList.add('btn-success');
                    this.querySelector('input[type="email"]').value = '';
                } else {
                    button.innerHTML = '<i class="bi bi-exclamation"></i> Error';
                    button.classList.remove('btn-light');
                    button.classList.add('btn-danger');
                }
                
                // Reset button after 3 seconds
                setTimeout(() => {
                    button.disabled = false;
                    button.innerHTML = 'Subscribe';
                    button.classList.remove('btn-success', 'btn-danger');
                    button.classList.add('btn-light');
                }, 3000);
            })
            .catch(error => {
                console.error('Error:', error);
                button.innerHTML = '<i class="bi bi-exclamation"></i> Error';
                button.classList.remove('btn-light');
                button.classList.add('btn-danger');
                
                setTimeout(() => {
                    button.disabled = false;
                    button.innerHTML = 'Subscribe';
                    button.classList.remove('btn-danger');
                    button.classList.add('btn-light');
                }, 3000);
            });
        });
    }
});
</script>

<?php
    include 'includes/footer.php';
} // Closes the else block for the default homepage
?>