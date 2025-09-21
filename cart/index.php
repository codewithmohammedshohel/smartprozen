<?php
require_once '../config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

$page_title = 'Shopping Cart';
include '../includes/header.php';
?>

<div class="container my-5">
    <h1 class="mb-4">Shopping Cart</h1>
    
    <?php if (empty($_SESSION['cart'])): ?>
        <div class="text-center py-5">
            <i class="bi bi-cart-x fs-1 text-muted"></i>
            <h3 class="text-muted mt-3">Your cart is empty</h3>
            <p class="text-muted">Add some products to get started!</p>
            <a href="<?php echo SITE_URL; ?>/products_list.php" class="btn btn-primary">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if (!empty($_SESSION['cart'])) {
                                        $product_ids = array_keys($_SESSION['cart']);
                                        $placeholders = str_repeat('?,', count($product_ids) - 1) . '?';
                                        $stmt = $conn->prepare("SELECT id, name, price, sale_price, featured_image FROM products WHERE id IN ($placeholders)");
                                        $stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
                                        $stmt->execute();
                                        $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                                        $stmt->close();
                                        
                                        $products_by_id = [];
                                        foreach ($products as $product) {
                                            $products_by_id[$product['id']] = $product;
                                        }
                                        
                                        foreach ($_SESSION['cart'] as $product_id => $quantity):
                                            $product = $products_by_id[$product_id] ?? null;
                                            if ($product):
                                                $price = $product['sale_price'] ?? $product['price'];
                                                $product_image = !empty($product['featured_image']) ? 
                                                    SITE_URL . '/uploads/media/thumb-' . $product['featured_image'] : 
                                                    'https://placehold.co/80x80/efefef/333?text=' . urlencode($product['name']);
                                    ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <img src="<?php echo htmlspecialchars($product_image); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-thumbnail">
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0"><?php echo htmlspecialchars($product['name']); ?></h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php echo format_price($price); ?>
                                            </td>
                                            <td>
                                                <div class="input-group" style="width: 120px;">
                                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="updateQuantity(<?php echo $product_id; ?>, -1)">-</button>
                                                    <input type="number" class="form-control form-control-sm text-center" value="<?php echo $quantity; ?>" min="1" onchange="updateQuantity(<?php echo $product_id; ?>, this.value)">
                                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="updateQuantity(<?php echo $product_id; ?>, 1)">+</button>
                                                </div>
                                            </td>
                                            <td>
                                                <strong><?php echo format_price($price * $quantity); ?></strong>
                                            </td>
                                            <td>
                                                <button class="btn btn-outline-danger btn-sm" onclick="removeFromCart(<?php echo $product_id; ?>)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php 
                                            endif;
                                        endforeach;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span><?php echo format_price(get_cart_total()); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span>$9.99</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Tax:</span>
                            <span>$0.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong><?php echo format_price(get_cart_total() + 9.99); ?></strong>
                        </div>
                        <div class="d-grid gap-2">
                            <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
                            <a href="<?php echo SITE_URL; ?>/products_list.php" class="btn btn-outline-secondary">Continue Shopping</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function updateQuantity(productId, quantity) {
    if (quantity <= 0) {
        removeFromCart(productId);
        return;
    }
    
    fetch('update_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=update&product_id=${productId}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating cart: ' + data.message);
        }
    });
}

function removeFromCart(productId) {
    if (confirm('Are you sure you want to remove this item from your cart?')) {
        fetch('update_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=remove&product_id=${productId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error removing item: ' + data.message);
            }
        });
    }
}
</script>

<?php include '../includes/footer.php'; ?>