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
                    <div class="col" data-aos="fade-up" data-aos-delay="<?php echo ($all_products->current_field % 4) * 100; ?>">
                        <div class="card h-100 product-card shadow-sm border-0">
                            <div class="position-relative">
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="text-decoration-none">
                                    <img loading="lazy" 
                                         src="<?php echo SITE_URL . '/uploads/media/thumb-' . htmlspecialchars($product['image_filename']); ?>" 
                                         class="card-img-top" 
                                         alt="<?php echo htmlspecialchars(get_translated_text($product['name'], 'name')); ?>"
                                         style="height: 250px; object-fit: cover;">
                                </a>
                                <div class="position-absolute top-0 end-0 p-2">
                                    <span class="badge bg-success">New</span>
                                    <span class="badge bg-info product-cart-quantity-badge" id="product-quantity-<?php echo $product['id']; ?>" style="display: none;">0</span>
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column p-4">
                                <h5 class="card-title mb-3">
                                    <a href="product.php?id=<?php echo $product['id']; ?>" class="text-decoration-none text-dark fw-semibold">
                                        <?php echo htmlspecialchars(get_translated_text($product['name'], 'name')); ?>
                                    </a>
                                </h5>
                                <p class="card-text text-muted small mb-3">
                                    <?php echo htmlspecialchars(substr(strip_tags(get_translated_text($product['description'], 'description')), 0, 100)) . '...'; ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <div class="price-section">
                                        <span class="h4 text-primary fw-bold mb-0">$<?php echo number_format($product['price'], 2); ?></span>
                                    </div>
                                    <div class="input-group input-group-sm" style="width: 100px;">
                                        <button class="btn btn-outline-secondary quantity-minus" type="button" data-product-id="<?php echo $product['id']; ?>">-</button>
                                        <input type="number" class="form-control text-center product-quantity-input" value="1" min="1" data-product-id="<?php echo $product['id']; ?>">
                                        <button class="btn btn-outline-secondary quantity-plus" type="button" data-product-id="<?php echo $product['id']; ?>">+</button>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-primary btn-sm add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">
                                            <i class="bi bi-cart-plus"></i>
                                        </button>
                                        <button type="button" class="btn btn-success btn-sm buy-now-btn" data-product-id="<?php echo $product['id']; ?>">
                                            <i class="bi bi-lightning-charge"></i>
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
