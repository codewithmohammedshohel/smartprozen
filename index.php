<?php
require_once 'config.php';
require_once 'core/db.php';
require_once 'core/functions.php';

// Check if home page exists in database
$stmt = $conn->prepare("SELECT id FROM pages WHERE slug = 'home'");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Home page exists, redirect to it
    header('Location: page.php?slug=home');
    exit;
} else {
    // Home page doesn't exist, show a default homepage
    $page_title = get_translated_text('{"en": "Welcome to SmartProZen", "bn": "স্মার্টপ্রোজেনে স্বাগতম"}', 'page_title');
    include 'includes/header.php';
    ?>
    
    <div class="hero-section bg-primary text-white py-5 mb-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4"><?php echo $page_title; ?></h1>
                    <p class="lead mb-4"><?php echo __('your_ultimate_online_shopping_destination'); ?></p>
                    <div class="d-flex gap-3">
                        <a href="/smartprozen/products_list.php" class="btn btn-light btn-lg"><?php echo __('shop_now'); ?></a>
                        <a href="/smartprozen/auth/register.php" class="btn btn-outline-light btn-lg"><?php echo __('get_started'); ?></a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <i class="bi bi-shop display-1 opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="bi bi-lightning-charge text-primary display-4 mb-3"></i>
                        <h3 class="card-title"><?php echo __('fast_delivery'); ?></h3>
                        <p class="card-text"><?php echo __('instant_digital_downloads'); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="bi bi-shield-check text-success display-4 mb-3"></i>
                        <h3 class="card-title"><?php echo __('secure_payment'); ?></h3>
                        <p class="card-text"><?php echo __('safe_and_secure_transactions'); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="bi bi-headset text-info display-4 mb-3"></i>
                        <h3 class="card-title"><?php echo __('customer_support'); ?></h3>
                        <p class="card-text"><?php echo __('24_7_customer_support'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12 text-center">
                <h2 class="mb-4"><?php echo __('featured_products'); ?></h2>
                <div class="row">
                    <?php
                    // Get featured products
                    $featured_stmt = $conn->prepare("SELECT * FROM products ORDER BY created_at DESC LIMIT 6");
                    $featured_stmt->execute();
                    $featured_products = $featured_stmt->get_result();
                    
                    if ($featured_products->num_rows > 0) {
                        while ($product = $featured_products->fetch_assoc()) {
                            ?>
                            <div class="col-md-4 col-lg-2 mb-4">
                                <div class="card product-card h-100">
                                    <a href="product.php?id=<?php echo $product['id']; ?>">
                                        <img src="<?php echo SITE_URL . '/uploads/media/thumb-' . htmlspecialchars($product['image_filename']); ?>" 
                                             class="card-img-top" 
                                             alt="<?php echo htmlspecialchars(get_translated_text($product['name'], 'name')); ?>"
                                             style="height: 150px; object-fit: cover;">
                                    </a>
                                    <div class="card-body d-flex flex-column">
                                        <h6 class="card-title">
                                            <a href="product.php?id=<?php echo $product['id']; ?>" class="text-decoration-none text-dark">
                                                <?php echo htmlspecialchars(get_translated_text($product['name'], 'name')); ?>
                                            </a>
                                        </h6>
                                        <p class="card-text price mt-auto">$<?php echo number_format($product['price'], 2); ?></p>
                                        <div class="d-grid">
                                            <a href="cart/add_to_cart.php?product_id=<?php echo $product['id']; ?>&quantity=1" 
                                               class="btn btn-primary btn-sm"><?php echo __('add_to_cart'); ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        ?>
                        <div class="col-12">
                            <p class="text-muted"><?php echo __('no_products_available'); ?></p>
                        </div>
                        <?php
                    }
                    $featured_stmt->close();
                    ?>
                </div>
                <div class="mt-4">
                    <a href="/smartprozen/products_list.php" class="btn btn-outline-primary"><?php echo __('view_all_products'); ?></a>
                </div>
            </div>
        </div>
    </div>

    <?php
    include 'includes/footer.php';
}