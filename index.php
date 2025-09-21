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
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                <div class="card category-card text-white">
                    <img src="https://placehold.co/600x400/343a40/ffffff?text=Smart+Home" class="card-img" alt="Smart Home Devices">
                    <div class="card-img-overlay d-flex flex-column justify-content-end p-4">
                        <h5 class="card-title fw-bold">Smart Home Devices</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                <div class="card category-card text-white">
                    <img src="https://placehold.co/600x400/343a40/ffffff?text=Audio" class="card-img" alt="Professional Audio">
                    <div class="card-img-overlay d-flex flex-column justify-content-end p-4">
                        <h5 class="card-title fw-bold">Professional Audio</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                <div class="card category-card text-white">
                    <img src="https://placehold.co/600x400/343a40/ffffff?text=Mobile" class="card-img" alt="Mobile Accessories">
                    <div class="card-img-overlay d-flex flex-column justify-content-end p-4">
                        <h5 class="card-title fw-bold">Mobile Accessories</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                <div class="card category-card text-white">
                    <img src="https://placehold.co/600x400/343a40/ffffff?text=Wearables" class="card-img" alt="Wearable Tech">
                    <div class="card-img-overlay d-flex flex-column justify-content-end p-4">
                        <h5 class="card-title fw-bold">Wearable Tech</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. Best Sellers Section -->
<section id="best-sellers" class="best-sellers-section py-5 bg-light">
    <div class="container">
        <h2 class="text-center fw-bold mb-5">Our Most Popular Products</h2>
        <div class="row g-4">
            <!-- Product 1 -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up">
                <div class="card product-card h-100 shadow-sm">
                    <img src="https://placehold.co/600x600/efefef/333?text=ZenBuds+Pro+3" class="card-img-top" alt="ZenBuds Pro 3">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">ZenBuds Pro 3</h5>
                        <p class="card-text price fw-bold text-primary fs-5 mt-auto">$89.99</p>
                        <button class="btn btn-primary mt-2 add-to-cart-btn">Add to Cart</button>
                    </div>
                </div>
            </div>
            <!-- Product 2 -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card product-card h-100 shadow-sm">
                    <img src="https://placehold.co/600x600/efefef/333?text=SmartGlow+Light" class="card-img-top" alt="SmartGlow Ambient Light">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">SmartGlow Ambient Light</h5>
                        <p class="card-text price fw-bold text-primary fs-5 mt-auto">$59.99</p>
                        <button class="btn btn-primary mt-2 add-to-cart-btn">Add to Cart</button>
                    </div>
                </div>
            </div>
            <!-- Product 3 -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card product-card h-100 shadow-sm">
                    <img src="https://placehold.co/600x600/efefef/333?text=ProCharge+Stand" class="card-img-top" alt="ProCharge Wireless Stand">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">ProCharge Wireless Stand</h5>
                        <p class="card-text price fw-bold text-primary fs-5 mt-auto">$45.00</p>
                        <button class="btn btn-primary mt-2 add-to-cart-btn">Add to Cart</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-5">
            <a href="<?php echo SITE_URL; ?>/products_list.php" class="btn btn-outline-primary">View All Best Sellers</a>
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
            <div class="col-lg-6" data-aos="fade-right">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex">
                            <i class="bi bi-quote fs-1 text-primary me-3"></i>
                            <div>
                                <p class="mb-3">"The ZenBuds Pro are the best wireless earbuds I've ever used. The sound quality is incredible for the price, and they are so comfortable."</p>
                                <footer class="blockquote-footer">Mark T., <cite title="Source Title">Audio Engineer</cite></footer>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex">
                            <i class="bi bi-quote fs-1 text-primary me-3"></i>
                            <div>
                                <p class="mb-3">"My order from SmartProZen arrived in just two days! The packaging was great and the SmartGlow light looks amazing on my desk. 10/10 would shop again."</p>
                                <footer class="blockquote-footer">Sarah K., <cite title="Source Title">Designer</cite></footer>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

<?php
    include 'includes/footer.php';
} // Closes the else block for the default homepage
?>