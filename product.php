<?php
require_once 'config.php';
require_once 'core/db.php';
require_once 'core/functions.php';

$product_id = (int)($_GET['id'] ?? 0);

if ($product_id <= 0) {
    header('Location: /smartprozen/');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    header('Location: /smartprozen/');
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
                <img src="<?php echo SITE_URL . '/uploads/media/' . htmlspecialchars($product['image_filename']); ?>" 
                     alt="<?php echo htmlspecialchars($page_title); ?>" 
                     class="img-fluid rounded shadow-medium">
            </div>
        </div>
        <div class="col-lg-6">
            <div class="product-details">
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
                
                <div class="product-actions mb-4">
                    <form action="cart/add_to_cart.php" method="POST" class="d-flex gap-3 align-items-center">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <div class="quantity-selector">
                            <label for="quantity" class="form-label"><?php echo __('quantity'); ?>:</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" max="10" style="width: 80px;">
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-cart-plus"></i> <?php echo __('add_to_cart'); ?>
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
                        <?php if ($product['file_size']): ?>
                            <div class="col-sm-6">
                                <strong><?php echo __('file_size'); ?>:</strong> <?php echo htmlspecialchars($product['file_size']); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($product['is_digital']): ?>
                            <div class="col-sm-6">
                                <strong><?php echo __('product_type'); ?>:</strong> <?php echo __('digital_product'); ?>
                            </div>
                        <?php endif; ?>
                        <div class="col-sm-6">
                            <strong><?php echo __('availability'); ?>:</strong> 
                            <span class="text-success"><?php echo __('in_stock'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if ($product['tags']): ?>
        <div class="row mt-5">
            <div class="col-12">
                <h5><?php echo __('tags'); ?>:</h5>
                <div class="tags">
                    <?php
                    $tags = json_decode($product['tags'], true);
                    if (is_array($tags)) {
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
    
    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Insert at the top of the product details
        const productDetails = document.querySelector('.product-details');
        productDetails.insertBefore(alertDiv, productDetails.firstChild);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
});
</script>

<?php include 'includes/footer.php'; ?>