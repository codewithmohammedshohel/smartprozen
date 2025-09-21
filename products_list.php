<?php
require_once 'config.php';
require_once 'core/db.php';
require_once 'core/functions.php';

$page_title = get_translated_text('All Products', 'page_title');
$page_description = get_translated_text('Browse all our amazing products.', 'page_description');

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section bg-gradient-primary text-white py-5 mb-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3" data-aos="fade-up"><?php echo htmlspecialchars($page_title); ?></h1>
                <p class="lead mb-4" data-aos="fade-up" data-aos-delay="100"><?php echo htmlspecialchars($page_description); ?></p>
                <div class="d-flex gap-3" data-aos="fade-up" data-aos-delay="200">
                    <a href="#products" class="btn btn-light btn-lg">
                        <i class="bi bi-arrow-down me-2"></i>Browse Products
                    </a>
                </div>
            </div>
            <div class="col-lg-4 text-center" data-aos="fade-left">
                <i class="bi bi-shop display-1 opacity-75"></i>
            </div>
        </div>
    </div>
</section>

<main id="products">
    <div class="container">

        <!-- Search and Filter Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="GET" action="products_list.php" class="row g-3 align-items-end">
                            <div class="col-md-8">
                                <label for="search" class="form-label fw-semibold">Search Products</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text" class="form-control form-control-lg" id="search" name="search" 
                                           value="<?php echo htmlspecialchars($search_term ?? ''); ?>" 
                                           placeholder="Search for products...">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-search me-2"></i>Search
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
            <?php
            $search_term = $_GET['search'] ?? '';
            $sql = "SELECT * FROM products WHERE 1=1";
            $params = [];
            $types = '';

            if (!empty($search_term)) {
                $sql .= " AND (name LIKE ? OR description LIKE ?)";
                $params[] = '%' . $search_term . '%';
                $params[] = '%' . $search_term . '%';
                $types .= 'ss';
            }

            $sql .= " ORDER BY created_at DESC";

            $products_stmt = $conn->prepare($sql);
            if (!empty($params)) {
                $products_stmt->bind_param($types, ...$params);
            }
            $products_stmt->execute();
            $all_products = $products_stmt->get_result();

            if ($all_products->num_rows > 0) {
                while ($product = $all_products->fetch_assoc()) {
                    ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4" data-aos="fade-up" data-aos-delay="<?php echo ($all_products->current_field % 4) * 100; ?>">
                        <div class="card h-100 product-card shadow-sm border-0 position-relative overflow-hidden">
                            <!-- Product Image Section -->
                            <div class="position-relative product-image-container">
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="text-decoration-none d-block">
                                    <img loading="lazy" 
                                         src="<?php echo SITE_URL . '/uploads/media/thumb-' . htmlspecialchars($product['featured_image'] ?? 'default-product.jpg'); ?>" 
                                         class="card-img-top product-image" 
                                         alt="<?php echo htmlspecialchars(get_translated_text($product['name'], 'name')); ?>"
                                         onerror="this.src='https://via.placeholder.com/300x300/f8f9fa/6c757d?text=No+Image'">
                                </a>
                                
                                <!-- Product Badges -->
                                <div class="product-badges">
                                    <?php if ($product['is_featured']): ?>
                                        <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-2">
                                            <i class="bi bi-star-fill me-1"></i>Featured
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                                        <?php 
                                        $discount_percent = round((($product['price'] - $product['sale_price']) / $product['price']) * 100);
                                        ?>
                                        <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                                            <i class="bi bi-percent me-1"></i><?php echo $discount_percent; ?>% OFF
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-success position-absolute top-0 end-0 m-2">
                                            <i class="bi bi-check-circle me-1"></i>New
                                        </span>
                                    <?php endif; ?>
                                    
                                    <!-- Cart Quantity Badge -->
                                    <span class="badge bg-info position-absolute bottom-0 end-0 m-2 product-cart-quantity-badge" 
                                          id="product-quantity-<?php echo $product['id']; ?>" 
                                          style="display: none;">
                                        <i class="bi bi-cart-check me-1"></i><span class="quantity-text">0</span>
                                    </span>
                                </div>
                                
                                <!-- Quick View Overlay -->
                                <div class="product-overlay">
                                    <div class="overlay-content">
                                        <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-light btn-sm">
                                            <i class="bi bi-eye me-1"></i>Quick View
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Product Content -->
                            <div class="card-body d-flex flex-column p-3">
                                <!-- Product Title -->
                                <h6 class="card-title mb-2">
                                    <a href="product.php?id=<?php echo $product['id']; ?>" 
                                       class="text-decoration-none text-dark fw-semibold product-title"
                                       title="<?php echo htmlspecialchars(get_translated_text($product['name'], 'name')); ?>">
                                        <?php echo htmlspecialchars(get_translated_text($product['name'], 'name')); ?>
                                    </a>
                                </h6>
                                
                                <!-- Product Description -->
                                <p class="card-text text-muted small mb-2 product-description">
                                    <?php 
                                    $description = strip_tags(get_translated_text($product['description'], 'description'));
                                    echo htmlspecialchars(strlen($description) > 80 ? substr($description, 0, 80) . '...' : $description); 
                                    ?>
                                </p>
                                
                                <!-- Product SKU -->
                                <?php if (!empty($product['sku'])): ?>
                                    <small class="text-muted mb-2 d-block">
                                        <i class="bi bi-tag me-1"></i>SKU: <?php echo htmlspecialchars($product['sku']); ?>
                                    </small>
                                <?php endif; ?>
                                
                                <!-- Stock Status -->
                                <div class="stock-status mb-2">
                                    <?php if ($product['stock_status'] === 'instock'): ?>
                                        <small class="text-success">
                                            <i class="bi bi-check-circle-fill me-1"></i>In Stock
                                        </small>
                                    <?php elseif ($product['stock_status'] === 'outofstock'): ?>
                                        <small class="text-danger">
                                            <i class="bi bi-x-circle-fill me-1"></i>Out of Stock
                                        </small>
                                    <?php else: ?>
                                        <small class="text-warning">
                                            <i class="bi bi-clock-fill me-1"></i>Backorder
                                        </small>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Price Section -->
                                <div class="price-section mb-3">
                                    <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                                        <div class="d-flex align-items-center">
                                            <span class="h5 text-danger fw-bold mb-0 me-2">$<?php echo number_format($product['sale_price'], 2); ?></span>
                                            <span class="text-muted text-decoration-line-through small">$<?php echo number_format($product['price'], 2); ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="h5 text-primary fw-bold mb-0">$<?php echo number_format($product['price'], 2); ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Quantity and Actions -->
                                <div class="product-actions mt-auto">
                                    <!-- Quantity Selector -->
                                    <div class="quantity-selector mb-2">
                                        <label class="form-label small text-muted mb-1">Quantity:</label>
                                        <div class="input-group input-group-sm">
                                            <button class="btn btn-outline-secondary quantity-minus" 
                                                    type="button" 
                                                    data-product-id="<?php echo $product['id']; ?>"
                                                    title="Decrease quantity">
                                                <i class="bi bi-dash"></i>
                                            </button>
                                            <input type="number" 
                                                   class="form-control text-center product-quantity-input" 
                                                   value="1" 
                                                   min="1" 
                                                   max="99"
                                                   data-product-id="<?php echo $product['id']; ?>"
                                                   title="Quantity">
                                            <button class="btn btn-outline-secondary quantity-plus" 
                                                    type="button" 
                                                    data-product-id="<?php echo $product['id']; ?>"
                                                    title="Increase quantity">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                    </div>
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="action-buttons">
                                        <button type="button" 
                                                class="btn btn-primary btn-sm w-100 mb-1 add-to-cart-btn" 
                                                data-product-id="<?php echo $product['id']; ?>"
                                                <?php echo $product['stock_status'] === 'outofstock' ? 'disabled' : ''; ?>>
                                            <i class="bi bi-cart-plus me-1"></i>Add to Cart
                                        </button>
                                        <button type="button" 
                                                class="btn btn-success btn-sm w-100 buy-now-btn" 
                                                data-product-id="<?php echo $product['id']; ?>"
                                                <?php echo $product['stock_status'] === 'outofstock' ? 'disabled' : ''; ?>>
                                            <i class="bi bi-lightning-charge me-1"></i>Buy Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="col-12">
                    <div class="text-center py-5">
                        <div class="empty-state">
                            <i class="bi bi-box display-1 text-muted mb-4"></i>
                            <h3 class="text-muted mb-3">No Products Found</h3>
                            <p class="text-muted mb-4"><?php echo !empty($search_term) ? 'No products match your search criteria.' : 'No products available at the moment. Please check back later!'; ?></p>
                            <?php if (!empty($search_term)): ?>
                                <a href="products_list.php" class="btn btn-primary">
                                    <i class="bi bi-arrow-left me-2"></i>View All Products
                                </a>
                            <?php else: ?>
                                <a href="/smartprozen/" class="btn btn-primary">
                                    <i class="bi bi-house me-2"></i>Go Home
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php
            }
            $products_stmt->close();
            ?>
        </div>
    </div>
</main>

<style>
/* Enhanced Product Cards Styles */
.product-card {
    transition: all 0.3s ease;
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.product-image-container {
    position: relative;
    overflow: hidden;
    border-radius: 12px 12px 0 0;
}

.product-image {
    width: 100%;
    height: 250px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.product-badges {
    z-index: 2;
}

.product-badges .badge {
    font-size: 0.75rem;
    padding: 0.4rem 0.6rem;
    border-radius: 6px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: 12px 12px 0 0;
}

.product-card:hover .product-overlay {
    opacity: 1;
}

.overlay-content .btn {
    border-radius: 20px;
    padding: 0.5rem 1rem;
    font-weight: 500;
}

.product-title {
    font-size: 1rem;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    min-height: 2.6rem;
}

.product-title:hover {
    color: #0d6efd !important;
}

.product-description {
    font-size: 0.85rem;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    min-height: 2.8rem;
}

.stock-status small {
    font-weight: 500;
}

.price-section {
    border-top: 1px solid #f1f3f4;
    padding-top: 0.75rem;
}

.quantity-selector {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 0.5rem;
}

.quantity-selector .input-group {
    border-radius: 6px;
    overflow: hidden;
}

.quantity-selector .btn {
    border: 1px solid #dee2e6;
    background: #fff;
    color: #6c757d;
    padding: 0.375rem 0.5rem;
    transition: all 0.2s ease;
}

.quantity-selector .btn:hover {
    background: #e9ecef;
    color: #495057;
}

.quantity-selector .form-control {
    border: 1px solid #dee2e6;
    background: #fff;
    font-weight: 600;
    color: #495057;
}

.quantity-selector .form-control:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.action-buttons .btn {
    border-radius: 8px;
    font-weight: 500;
    padding: 0.5rem 0.75rem;
    transition: all 0.2s ease;
    text-transform: none;
    letter-spacing: 0.025em;
}

.action-buttons .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.action-buttons .btn:active {
    transform: translateY(0);
}

.add-to-cart-btn {
    background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
    border: none;
}

.add-to-cart-btn:hover {
    background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
}

.buy-now-btn {
    background: linear-gradient(135deg, #198754 0%, #146c43 100%);
    border: none;
}

.buy-now-btn:hover {
    background: linear-gradient(135deg, #146c43 0%, #0f5132 100%);
}

.product-cart-quantity-badge {
    animation: bounceIn 0.5s ease;
    font-weight: 600;
}

@keyframes bounceIn {
    0% { transform: scale(0.3); opacity: 0; }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); opacity: 1; }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .product-image {
        height: 200px;
    }
    
    .product-title {
        font-size: 0.9rem;
        min-height: 2.3rem;
    }
    
    .product-description {
        font-size: 0.8rem;
        min-height: 2.4rem;
    }
    
    .action-buttons .btn {
        font-size: 0.8rem;
        padding: 0.4rem 0.6rem;
    }
}

@media (max-width: 576px) {
    .product-card {
        margin-bottom: 1.5rem;
    }
    
    .product-image {
        height: 180px;
    }
}

/* Loading animation for buttons */
.btn.loading {
    position: relative;
    color: transparent !important;
}

.btn.loading::after {
    content: "";
    position: absolute;
    width: 16px;
    height: 16px;
    top: 50%;
    left: 50%;
    margin-left: -8px;
    margin-top: -8px;
    border: 2px solid transparent;
    border-top-color: #ffffff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Success animation for cart updates */
.btn.success {
    background: #198754 !important;
    color: white !important;
}

.btn.success::before {
    content: "✓";
    margin-right: 0.5rem;
}

/* Disabled state for out of stock */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
    box-shadow: none !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quantity adjustment for product cards
    document.querySelectorAll('.quantity-minus').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const quantityInput = document.querySelector(`.product-quantity-input[data-product-id="${productId}"]`);
            if (quantityInput && parseInt(quantityInput.value) > 1) {
                quantityInput.value = parseInt(quantityInput.value) - 1;
            }
        });
    });

    document.querySelectorAll('.quantity-plus').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const quantityInput = document.querySelector(`.product-quantity-input[data-product-id="${productId}"]`);
            if (quantityInput) {
                quantityInput.value = parseInt(quantityInput.value) + 1;
            }
        });
    });

    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    const cartBadge = document.querySelector('#main-nav .badge');

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const productId = this.dataset.productId;
            const quantityInput = document.querySelector(`.product-quantity-input[data-product-id="${productId}"]`);
            const quantity = quantityInput ? parseInt(quantityInput.value) : 1; // Read quantity from input
            const submitBtn = this;
            const originalIcon = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            submitBtn.disabled = true;
            
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', quantity);

            fetch('cart/add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    if (cartBadge) {
                        cartBadge.textContent = data.cart_count;
                    }
                    const productQuantityBadge = document.getElementById(`product-quantity-${productId}`);
                    if (productQuantityBadge) {
                        productQuantityBadge.textContent = data.product_quantity;
                        productQuantityBadge.style.display = data.product_quantity > 0 ? 'inline-block' : 'none';
                    }
                    const productQuantityBadge = document.getElementById(`product-quantity-${productId}`);
                    if (productQuantityBadge) {
                        productQuantityBadge.textContent = data.product_quantity;
                        productQuantityBadge.style.display = data.product_quantity > 0 ? 'inline-block' : 'none';
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
                submitBtn.innerHTML = originalIcon;
                submitBtn.disabled = false;
            });
        });
    });

    // Buy Now button functionality for product list
    const buyNowButtons = document.querySelectorAll('.buy-now-btn');
    buyNowButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const productId = this.dataset.productId;
            const quantityInput = document.querySelector(`.product-quantity-input[data-product-id="${productId}"]`);
            const quantity = quantityInput ? parseInt(quantityInput.value) : 1; // Read quantity from input
            
            const submitBtn = this;
            const originalIcon = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
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
                    submitBtn.innerHTML = originalIcon;
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
                submitBtn.innerHTML = originalIcon;
                submitBtn.disabled = false;
            });
        });
    });

    function showAlert(type, message) {
        const alertContainer = document.getElementById('flash-messages') || document.querySelector('main .container');
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show mt-3`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        if (alertContainer) {
            alertContainer.prepend(alertDiv);
        } else {
            document.body.prepend(alertDiv);
        }
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    // Fetch initial cart quantities and update badges on page load
    fetch('cart/get_cart_quantities.php')
        .then(response => response.json())
        .then(cartQuantities => {
            for (const productId in cartQuantities) {
                if (cartQuantities.hasOwnProperty(productId)) {
                    const quantity = cartQuantities[productId];
                    const productQuantityBadge = document.getElementById(`product-quantity-${productId}`);
                    if (productQuantityBadge) {
                        productQuantityBadge.textContent = quantity;
                        productQuantityBadge.style.display = quantity > 0 ? 'inline-block' : 'none';
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error fetching initial cart quantities:', error);
        });
});
</script>

<?php include 'includes/footer.php'; ?>

    font-size: 1rem;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    min-height: 2.6rem;
}

.product-title:hover {
    color: #0d6efd !important;
}

.product-description {
    font-size: 0.85rem;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    min-height: 2.8rem;
}

.stock-status small {
    font-weight: 500;
}

.price-section {
    border-top: 1px solid #f1f3f4;
    padding-top: 0.75rem;
}

.quantity-selector {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 0.5rem;
}

.quantity-selector .input-group {
    border-radius: 6px;
    overflow: hidden;
}

.quantity-selector .btn {
    border: 1px solid #dee2e6;
    background: #fff;
    color: #6c757d;
    padding: 0.375rem 0.5rem;
    transition: all 0.2s ease;
}

.quantity-selector .btn:hover {
    background: #e9ecef;
    color: #495057;
}

.quantity-selector .form-control {
    border: 1px solid #dee2e6;
    background: #fff;
    font-weight: 600;
    color: #495057;
}

.quantity-selector .form-control:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.action-buttons .btn {
    border-radius: 8px;
    font-weight: 500;
    padding: 0.5rem 0.75rem;
    transition: all 0.2s ease;
    text-transform: none;
    letter-spacing: 0.025em;
}

.action-buttons .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.action-buttons .btn:active {
    transform: translateY(0);
}

.add-to-cart-btn {
    background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
    border: none;
}

.add-to-cart-btn:hover {
    background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
}

.buy-now-btn {
    background: linear-gradient(135deg, #198754 0%, #146c43 100%);
    border: none;
}

.buy-now-btn:hover {
    background: linear-gradient(135deg, #146c43 0%, #0f5132 100%);
}

.product-cart-quantity-badge {
    animation: bounceIn 0.5s ease;
    font-weight: 600;
}

@keyframes bounceIn {
    0% { transform: scale(0.3); opacity: 0; }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); opacity: 1; }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .product-image {
        height: 200px;
    }
    
    .product-title {
        font-size: 0.9rem;
        min-height: 2.3rem;
    }
    
    .product-description {
        font-size: 0.8rem;
        min-height: 2.4rem;
    }
    
    .action-buttons .btn {
        font-size: 0.8rem;
        padding: 0.4rem 0.6rem;
    }
}

@media (max-width: 576px) {
    .product-card {
        margin-bottom: 1.5rem;
    }
    
    .product-image {
        height: 180px;
    }
}

/* Loading animation for buttons */
.btn.loading {
    position: relative;
    color: transparent !important;
}

.btn.loading::after {
    content: "";
    position: absolute;
    width: 16px;
    height: 16px;
    top: 50%;
    left: 50%;
    margin-left: -8px;
    margin-top: -8px;
    border: 2px solid transparent;
    border-top-color: #ffffff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Success animation for cart updates */
.btn.success {
    background: #198754 !important;
    color: white !important;
}

.btn.success::before {
    content: "✓";
    margin-right: 0.5rem;
}

/* Disabled state for out of stock */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
    box-shadow: none !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quantity adjustment for product cards
    document.querySelectorAll('.quantity-minus').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const quantityInput = document.querySelector(`.product-quantity-input[data-product-id="${productId}"]`);
            if (quantityInput && parseInt(quantityInput.value) > 1) {
                quantityInput.value = parseInt(quantityInput.value) - 1;
            }
        });
    });

    document.querySelectorAll('.quantity-plus').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const quantityInput = document.querySelector(`.product-quantity-input[data-product-id="${productId}"]`);
            if (quantityInput) {
                quantityInput.value = parseInt(quantityInput.value) + 1;
            }
        });
    });

    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    const cartBadge = document.querySelector('#main-nav .badge');

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const productId = this.dataset.productId;
            const quantityInput = document.querySelector(`.product-quantity-input[data-product-id="${productId}"]`);
            const quantity = quantityInput ? parseInt(quantityInput.value) : 1; // Read quantity from input
            const submitBtn = this;
            const originalIcon = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            submitBtn.disabled = true;
            
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', quantity);

            fetch('cart/add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    if (cartBadge) {
                        cartBadge.textContent = data.cart_count;
                    }
                    const productQuantityBadge = document.getElementById(`product-quantity-${productId}`);
                    if (productQuantityBadge) {
                        productQuantityBadge.textContent = data.product_quantity;
                        productQuantityBadge.style.display = data.product_quantity > 0 ? 'inline-block' : 'none';
                    }
                    const productQuantityBadge = document.getElementById(`product-quantity-${productId}`);
                    if (productQuantityBadge) {
                        productQuantityBadge.textContent = data.product_quantity;
                        productQuantityBadge.style.display = data.product_quantity > 0 ? 'inline-block' : 'none';
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
                submitBtn.innerHTML = originalIcon;
                submitBtn.disabled = false;
            });
        });
    });

    // Buy Now button functionality for product list
    const buyNowButtons = document.querySelectorAll('.buy-now-btn');
    buyNowButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const productId = this.dataset.productId;
            const quantityInput = document.querySelector(`.product-quantity-input[data-product-id="${productId}"]`);
            const quantity = quantityInput ? parseInt(quantityInput.value) : 1; // Read quantity from input
            
            const submitBtn = this;
            const originalIcon = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
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
                    submitBtn.innerHTML = originalIcon;
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
                submitBtn.innerHTML = originalIcon;
                submitBtn.disabled = false;
            });
        });
    });

    function showAlert(type, message) {
        const alertContainer = document.getElementById('flash-messages') || document.querySelector('main .container');
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show mt-3`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        if (alertContainer) {
            alertContainer.prepend(alertDiv);
        } else {
            document.body.prepend(alertDiv);
        }
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    // Fetch initial cart quantities and update badges on page load
    fetch('cart/get_cart_quantities.php')
        .then(response => response.json())
        .then(cartQuantities => {
            for (const productId in cartQuantities) {
                if (cartQuantities.hasOwnProperty(productId)) {
                    const quantity = cartQuantities[productId];
                    const productQuantityBadge = document.getElementById(`product-quantity-${productId}`);
                    if (productQuantityBadge) {
                        productQuantityBadge.textContent = quantity;
                        productQuantityBadge.style.display = quantity > 0 ? 'inline-block' : 'none';
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error fetching initial cart quantities:', error);
        });
});
</script>

<?php include 'includes/footer.php'; ?>

    font-size: 1rem;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    min-height: 2.6rem;
}

.product-title:hover {
    color: #0d6efd !important;
}

.product-description {
    font-size: 0.85rem;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    min-height: 2.8rem;
}

.stock-status small {
    font-weight: 500;
}

.price-section {
    border-top: 1px solid #f1f3f4;
    padding-top: 0.75rem;
}

.quantity-selector {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 0.5rem;
}

.quantity-selector .input-group {
    border-radius: 6px;
    overflow: hidden;
}

.quantity-selector .btn {
    border: 1px solid #dee2e6;
    background: #fff;
    color: #6c757d;
    padding: 0.375rem 0.5rem;
    transition: all 0.2s ease;
}

.quantity-selector .btn:hover {
    background: #e9ecef;
    color: #495057;
}

.quantity-selector .form-control {
    border: 1px solid #dee2e6;
    background: #fff;
    font-weight: 600;
    color: #495057;
}

.quantity-selector .form-control:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.action-buttons .btn {
    border-radius: 8px;
    font-weight: 500;
    padding: 0.5rem 0.75rem;
    transition: all 0.2s ease;
    text-transform: none;
    letter-spacing: 0.025em;
}

.action-buttons .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.action-buttons .btn:active {
    transform: translateY(0);
}

.add-to-cart-btn {
    background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
    border: none;
}

.add-to-cart-btn:hover {
    background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
}

.buy-now-btn {
    background: linear-gradient(135deg, #198754 0%, #146c43 100%);
    border: none;
}

.buy-now-btn:hover {
    background: linear-gradient(135deg, #146c43 0%, #0f5132 100%);
}

.product-cart-quantity-badge {
    animation: bounceIn 0.5s ease;
    font-weight: 600;
}

@keyframes bounceIn {
    0% { transform: scale(0.3); opacity: 0; }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); opacity: 1; }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .product-image {
        height: 200px;
    }
    
    .product-title {
        font-size: 0.9rem;
        min-height: 2.3rem;
    }
    
    .product-description {
        font-size: 0.8rem;
        min-height: 2.4rem;
    }
    
    .action-buttons .btn {
        font-size: 0.8rem;
        padding: 0.4rem 0.6rem;
    }
}

@media (max-width: 576px) {
    .product-card {
        margin-bottom: 1.5rem;
    }
    
    .product-image {
        height: 180px;
    }
}

/* Loading animation for buttons */
.btn.loading {
    position: relative;
    color: transparent !important;
}

.btn.loading::after {
    content: "";
    position: absolute;
    width: 16px;
    height: 16px;
    top: 50%;
    left: 50%;
    margin-left: -8px;
    margin-top: -8px;
    border: 2px solid transparent;
    border-top-color: #ffffff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Success animation for cart updates */
.btn.success {
    background: #198754 !important;
    color: white !important;
}

.btn.success::before {
    content: "✓";
    margin-right: 0.5rem;
}

/* Disabled state for out of stock */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
    box-shadow: none !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quantity adjustment for product cards
    document.querySelectorAll('.quantity-minus').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const quantityInput = document.querySelector(`.product-quantity-input[data-product-id="${productId}"]`);
            if (quantityInput && parseInt(quantityInput.value) > 1) {
                quantityInput.value = parseInt(quantityInput.value) - 1;
            }
        });
    });

    document.querySelectorAll('.quantity-plus').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const quantityInput = document.querySelector(`.product-quantity-input[data-product-id="${productId}"]`);
            if (quantityInput) {
                quantityInput.value = parseInt(quantityInput.value) + 1;
            }
        });
    });

    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    const cartBadge = document.querySelector('#main-nav .badge');

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const productId = this.dataset.productId;
            const quantityInput = document.querySelector(`.product-quantity-input[data-product-id="${productId}"]`);
            const quantity = quantityInput ? parseInt(quantityInput.value) : 1; // Read quantity from input
            const submitBtn = this;
            const originalIcon = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            submitBtn.disabled = true;
            
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', quantity);

            fetch('cart/add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    if (cartBadge) {
                        cartBadge.textContent = data.cart_count;
                    }
                    const productQuantityBadge = document.getElementById(`product-quantity-${productId}`);
                    if (productQuantityBadge) {
                        productQuantityBadge.textContent = data.product_quantity;
                        productQuantityBadge.style.display = data.product_quantity > 0 ? 'inline-block' : 'none';
                    }
                    const productQuantityBadge = document.getElementById(`product-quantity-${productId}`);
                    if (productQuantityBadge) {
                        productQuantityBadge.textContent = data.product_quantity;
                        productQuantityBadge.style.display = data.product_quantity > 0 ? 'inline-block' : 'none';
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
                submitBtn.innerHTML = originalIcon;
                submitBtn.disabled = false;
            });
        });
    });

    // Buy Now button functionality for product list
    const buyNowButtons = document.querySelectorAll('.buy-now-btn');
    buyNowButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const productId = this.dataset.productId;
            const quantityInput = document.querySelector(`.product-quantity-input[data-product-id="${productId}"]`);
            const quantity = quantityInput ? parseInt(quantityInput.value) : 1; // Read quantity from input
            
            const submitBtn = this;
            const originalIcon = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
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
                    submitBtn.innerHTML = originalIcon;
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
                submitBtn.innerHTML = originalIcon;
                submitBtn.disabled = false;
            });
        });
    });

    function showAlert(type, message) {
        const alertContainer = document.getElementById('flash-messages') || document.querySelector('main .container');
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show mt-3`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        if (alertContainer) {
            alertContainer.prepend(alertDiv);
        } else {
            document.body.prepend(alertDiv);
        }
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    // Fetch initial cart quantities and update badges on page load
    fetch('cart/get_cart_quantities.php')
        .then(response => response.json())
        .then(cartQuantities => {
            for (const productId in cartQuantities) {
                if (cartQuantities.hasOwnProperty(productId)) {
                    const quantity = cartQuantities[productId];
                    const productQuantityBadge = document.getElementById(`product-quantity-${productId}`);
                    if (productQuantityBadge) {
                        productQuantityBadge.textContent = quantity;
                        productQuantityBadge.style.display = quantity > 0 ? 'inline-block' : 'none';
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error fetching initial cart quantities:', error);
        });
});
</script>

<?php include 'includes/footer.php'; ?>
