<?php
require_once 'config.php';
require_once 'core/db.php';
require_once 'core/functions.php';

$product_id = (int)($_GET['id'] ?? 0);

if ($product_id <= 0) {
    header('Location: ' . SITE_URL . '/');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

$product_images = [];
$stmt_images = $conn->prepare("SELECT image_filename FROM product_images WHERE product_id = ? ORDER BY display_order ASC");
$stmt_images->bind_param("i", $product_id);
$stmt_images->execute();
$result_images = $stmt_images->get_result();
while ($row = $result_images->fetch_assoc()) {
    $product_images[] = $row['image_filename'];
}
$stmt_images->close();

// If no specific product images, use the main product image as the only one
if (empty($product_images) && !empty($product['featured_image'])) {
    $product_images[] = $product['featured_image'];
}

$reviews = [];
$stmt_reviews = $conn->prepare("SELECT r.*, CONCAT(u.first_name, ' ', u.last_name) as user_name FROM reviews r LEFT JOIN users u ON r.user_id = u.id WHERE r.product_id = ? AND r.is_approved = 1 ORDER BY r.created_at DESC");
$stmt_reviews->bind_param("i", $product_id);
$stmt_reviews->execute();
$result_reviews = $stmt_reviews->get_result();
while ($row = $result_reviews->fetch_assoc()) {
    $reviews[] = $row;
}
$stmt_reviews->close();

if (!$product) {
    header('Location: ' . SITE_URL . '/');
    exit;
}

$page_title = get_translated_text($product['name'], 'name');
$page_description = get_translated_text($product['description'], 'description');

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="product-image-container">
                <?php if (count($product_images) > 0): ?>
                    <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <?php foreach ($product_images as $index => $image): ?>
                                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                    <img src="<?php echo SITE_URL . '/uploads/media/' . htmlspecialchars($image); ?>" 
                                         class="d-block w-100 rounded shadow-medium product-main-image" 
                                         alt="<?php echo htmlspecialchars($page_title); ?> Image <?php echo $index + 1; ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                    <?php if (count($product_images) > 1): ?>
                        <div class="product-thumbnails d-flex gap-2 mt-3 justify-content-center">
                            <?php foreach ($product_images as $index => $image): ?>
                                <img src="<?php echo SITE_URL . '/uploads/media/' . htmlspecialchars($image); ?>" 
                                     class="img-thumbnail rounded shadow-sm <?php echo $index === 0 ? 'active' : ''; ?>" 
                                     alt="Thumbnail <?php echo $index + 1; ?>" 
                                     data-bs-target="#productCarousel" 
                                     data-bs-slide-to="<?php echo $index; ?>">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <img src="<?php echo SITE_URL . '/uploads/media/default.png'; ?>" 
                         alt="<?php echo htmlspecialchars($page_title); ?>" 
                         class="img-fluid rounded shadow-medium product-main-image">
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="product-details">
                <?php if (is_admin_logged_in()): ?>
                    <a href="<?php echo SITE_URL . '/admin/manage_products.php?action=edit&id=' . $product['id']; ?>" class="btn btn-sm btn-outline-primary float-end">
                        <i class="bi bi-pencil-fill"></i> Edit Product
                    </a>
                <?php endif; ?>
                <h1 class="product-title mb-3"><?php echo htmlspecialchars($page_title); ?></h1>
                
                <?php if ($product['short_description']): ?>
                    <p class="lead text-muted mb-4"><?php echo get_translated_text($product['short_description'], 'short_description'); ?></p>
                <?php endif; ?>
                
                <div class="price-section mb-4">
                    <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                        <div class="d-flex align-items-center gap-3">
                            <span class="h2 text-primary mb-0">$<?php echo number_format($product['sale_price'], 2); ?></span>
                            <span class="h4 text-muted text-decoration-line-through mb-0">$<?php echo number_format($product['price'], 2); ?></span>
                            <span class="badge bg-danger"><?php echo round((($product['price'] - $product['sale_price']) / $product['price']) * 100); ?>% OFF</span>
                        </div>
                    <?php else: ?>
                        <span class="h2 text-primary">$<?php echo number_format($product['price'], 2); ?></span>
                    <?php endif; ?>
                </div>
                
                <?php if ($product['description']): ?>
                    <div class="product-description mb-4">
                        <h5><?php echo __('description'); ?></h5>
                        <div class="description-content">
                            <?php echo get_translated_text($product['description'], 'description'); ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="product-actions mb-4 sticky-product-actions">
                    <form action="cart/add_to_cart.php" method="POST" class="d-flex gap-3 align-items-center">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <div class="quantity-selector input-group">
                            <label for="quantity" class="form-label visually-hidden"><?php echo __('quantity'); ?>:</label>
                            <button class="btn btn-outline-secondary quantity-minus" type="button">-</button>
                            <input type="number" class="form-control text-center" id="quantity" name="quantity" value="1" min="1" max="10">
                            <button class="btn btn-outline-secondary quantity-plus" type="button">+</button>
                        </div>
                        <button type="button" class="btn btn-success btn-lg buy-now-btn flex-grow-1" data-product-id="<?php echo $product['id']; ?>">
                            <i class="bi bi-lightning-charge"></i> <?php echo __('buy_now'); ?>
                        </button>
                    </form>
                </div>
                
                <div class="product-meta">
                    <div class="row">
                        <?php if ($product['sku']): ?>
                            <div class="col-sm-6">
                                <strong><?php echo __('sku'); ?>:</strong> <?php echo htmlspecialchars($product['sku']); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($product['digital_file'])): ?>
                            <div class="col-sm-6">
                                <strong><?php echo 'File'; ?>:</strong> <?php echo htmlspecialchars(basename($product['digital_file'])); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($product['product_type'] === 'digital'): ?>
                            <div class="col-sm-6">
                                <strong><?php echo 'Product Type'; ?>:</strong> <?php echo 'Digital Product'; ?>
                            </div>
                        <?php endif; ?>
                        <div class="col-sm-6">
                            <strong><?php echo 'Availability'; ?>:</strong> 
                            <span class="text-success"><?php echo ucfirst($product['stock_status'] ?? 'instock'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if (!empty($product['meta_keywords'])): ?>
        <div class="row mt-5">
            <div class="col-12">
                <h5><?php echo __('tags'); ?>:</h5>
                <div class="tags">
                    <?php
                    $tags = !empty($product['meta_keywords']) ? array_map('trim', explode(',', $product['meta_keywords'])) : [];
                    if (!empty($tags)) {
                        foreach ($tags as $tag) {
                            echo '<span class="badge bg-secondary me-2 mb-2">' . htmlspecialchars($tag) . '</span>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4"><?php echo __('customer_reviews'); ?></h2>
            
            <!-- Review Submission Form -->
            <div class="card mb-5">
                <div class="card-body">
                    <h5 class="card-title mb-3"><?php echo __('write_a_review'); ?></h5>
                    <form id="reviewForm">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <div class="mb-3">
                            <label for="reviewer_name" class="form-label"><?php echo __('your_name'); ?></label>
                            <input type="text" class="form-control" id="reviewer_name" name="reviewer_name" value="<?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="rating" class="form-label"><?php echo __('your_rating'); ?> <span class="text-danger">*</span></label>
                            <select class="form-select" id="rating" name="rating" required>
                                <option value="">-- <?php echo __('select_rating'); ?> --</option>
                                <option value="5">&#9733;&#9733;&#9733;&#9733;&#9733; (<?php echo __('excellent'); ?>)</option>
                                <option value="4">&#9733;&#9733;&#9733;&#9733; (<?php echo __('very_good'); ?>)</option>
                                <option value="3">&#9733;&#9733;&#9733; (<?php echo __('good'); ?>)</option>
                                <option value="2">&#9733;&#9733; (<?php echo __('fair'); ?>)</option>
                                <option value="1">&#9733; (<?php echo __('poor'); ?>)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="comment" class="form-label"><?php echo __('your_review'); ?> <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="comment" name="comment" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary"><?php echo __('submit_review'); ?></button>
                    </form>
                </div>
            </div>

            <!-- Existing Reviews -->
            <div class="reviews-list">
                <?php if (!empty($reviews)): ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title mb-1">
                                    <?php echo htmlspecialchars($review['guest_name'] ?? $review['user_name'] ?? 'Anonymous'); ?>
                                </h5>
                                <div class="text-muted small mb-2">
                                    <?php for ($i = 0; $i < $review['rating']; $i++): ?><span class="text-warning">&#9733;</span><?php endfor; ?>
                                    <?php for ($i = $review['rating']; $i < 5; $i++): ?><span class="text-muted">&#9733;</span><?php endfor; ?>
                                    - <?php echo date('M j, Y', strtotime($review['created_at'])); ?>
                                </div>
                                <p class="card-text"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        <?php echo __('no_reviews_yet'); ?>. <?php echo __('be_the_first_to_review'); ?>!
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add to cart form submission
    const addToCartForm = document.querySelector('form[action="cart/add_to_cart.php"]');
    if (addToCartForm) {
        addToCartForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<span class="spinner"></span> <?php echo __('adding_to_cart'); ?>...';
            submitBtn.disabled = true;
            
            fetch('cart/add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showAlert('success', data.message);
                    
                    // Update cart count in header
                    const cartBadge = document.querySelector('#main-nav .badge');
                    if (cartBadge) {
                        cartBadge.textContent = data.cart_count;
                    }
                } else {
                    showAlert('danger', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', '<?php echo __('error_adding_to_cart'); ?>');
            })
            .finally(() => {
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }

    // Buy Now button functionality
    const buyNowBtn = document.querySelector('.buy-now-btn');
    if (buyNowBtn) {
        buyNowBtn.addEventListener('click', function(e) {
            e.preventDefault();

            const productId = this.dataset.productId;
            const quantityInput = document.getElementById('quantity');
            const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
            
            const submitBtn = this;
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<span class="spinner"></span> <?php echo __('processing'); ?>...';
            submitBtn.disabled = true;

            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', quantity);

            fetch('cart/buy_now_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reset button state before redirection
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    // Redirect to checkout page
                    window.location.href = 'cart/checkout.php';
                } else {
                    showAlert('danger', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', '<?php echo __('error_processing_request'); ?>');
            })
            .finally(() => {
                // Reset button state if redirection doesn't happen immediately
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
    
    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Insert above the review form
        const reviewFormContainer = document.querySelector('.card.mb-5'); // The card containing the review form
        if (reviewFormContainer) {
            reviewFormContainer.parentNode.insertBefore(alertDiv, reviewFormContainer);
        } else {
            // Fallback if review form container not found
            const productDetails = document.querySelector('.product-details');
            if (productDetails) {
                productDetails.insertBefore(alertDiv, productDetails.firstChild);
            } else {
                document.body.prepend(alertDiv); // Fallback to body
            }
        }
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    // Quantity selector functionality
    const quantityInput = document.getElementById('quantity');
    const quantityMinusBtn = document.querySelector('.quantity-minus');
    const quantityPlusBtn = document.querySelector('.quantity-plus');

    if (quantityInput && quantityMinusBtn && quantityPlusBtn) {
        quantityMinusBtn.addEventListener('click', () => {
            let currentValue = parseInt(quantityInput.value);
            if (currentValue > parseInt(quantityInput.min)) {
                quantityInput.value = currentValue - 1;
            }
        });

        quantityPlusBtn.addEventListener('click', () => {
            let currentValue = parseInt(quantityInput.value);
            if (currentValue < parseInt(quantityInput.max)) {
                quantityInput.value = currentValue + 1;
            }
        });
    }

    // Product thumbnail click functionality
    const productThumbnails = document.querySelectorAll('.product-thumbnails img');
    if (productThumbnails.length > 0) {
        productThumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                // Remove active class from all thumbnails
                productThumbnails.forEach(img => img.classList.remove('active'));
                // Add active class to the clicked thumbnail
                this.classList.add('active');
            });
        });
    }

    // Review form submission
    const reviewForm = document.getElementById('reviewForm');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <?php echo __('submitting'); ?>...';
            submitBtn.disabled = true;

            fetch('api/review_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    reviewForm.reset(); // Clear the form
                    // Optionally, reload reviews or add the new review to the list dynamically
                } else {
                    showAlert('danger', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', '<?php echo __('error_submitting_review'); ?>');
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>