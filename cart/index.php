<?php
require_once __DIR__ . '/../includes/header.php';

$cart_items = [];
$subtotal = 0;
if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($product = $result->fetch_assoc()) {
        $product['quantity'] = $_SESSION['cart'][$product['id']];
        $cart_items[] = $product;
        $subtotal += $product['price'] * $product['quantity'];
    }
}

$discount = $_SESSION['discount_amount'] ?? 0;
$total = $subtotal - $discount;

?>
<div class="container py-5">
    <h1 class="mb-4">Your Shopping Cart</h1>
    <div id="flash-messages"></div>
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">Product</th>
                                    <th scope="col">Price</th>
                                    <th scope="col" style="width: 120px;">Quantity</th>
                                    <th scope="col" class="text-end">Total</th>
                                    <th scope="col" class="text-center">Remove</th>
                                </tr>
                            </thead>
                            <tbody id="cart-items-container">
                                <?php if (empty($cart_items)): ?>
                                    <tr><td colspan="5" class="text-center">Your cart is empty.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($cart_items as $item): ?>
                                        <tr id="cart-item-<?php echo $item['id']; ?>">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo SITE_URL . '/uploads/media/thumb-' . htmlspecialchars($item['image_filename']); ?>" alt="<?php echo htmlspecialchars(get_translated_text($item['name'], 'name')); ?>" class="img-fluid rounded me-3" style="width: 60px;">
                                                    <div><?php echo htmlspecialchars(get_translated_text($item['name'], 'name')); ?></div>
                                                </div>
                                            </td>
                                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm quantity-input" data-id="<?php echo $item['id']; ?>" value="<?php echo $item['quantity']; ?>" min="1">
                                            </td>
                                            <td class="text-end">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-outline-danger remove-item" data-id="<?php echo $item['id']; ?>">&times;</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="card-title h4">Cart Summary</h2>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span>Subtotal</span>
                        <strong id="cart-subtotal">$<?php echo number_format($subtotal, 2); ?></strong>
                    </div>
                    <form action="apply_coupon.php" method="POST" class="mt-3">
                        <div class="input-group">
                            <input type="text" class="form-control" name="coupon_code" placeholder="Coupon Code">
                            <button type="submit" class="btn btn-secondary">Apply</button>
                        </div>
                    </form>
                    <?php if ($discount > 0): ?>
                        <div class="d-flex justify-content-between align-items-center text-danger mt-2" id="cart-summary-discount-row">
                            <span>Discount (<?php echo $_SESSION['coupon_code']; ?>)</span>
                            <span id="cart-discount">-$<?php echo number_format($discount, 2); ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between align-items-center mt-3 h5 border-top pt-2">
                        <strong>Total</strong>
                        <strong id="cart-total">$<?php echo number_format($total, 2); ?></strong>
                    </div>
                    <div class="d-grid mt-4">
                        <a href="/smartprozen/cart/checkout.php" class="btn btn-primary btn-lg">Proceed to Checkout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cartItemsContainer = document.getElementById('cart-items-container');
    const cartSubtotalElem = document.getElementById('cart-subtotal');
    const cartDiscountElem = document.getElementById('cart-discount');
    const cartTotalElem = document.getElementById('cart-total');
    const cartCountBadge = document.querySelector('#main-nav .badge'); // Assuming this is the cart count in header

    function updateCartDisplay(data) {
        // Update individual item totals
        data.cart_items.forEach(item => {
            const itemRow = document.getElementById(`cart-item-${item.id}`);
            if (itemRow) {
                itemRow.querySelector('.quantity-input').value = item.quantity;
                itemRow.querySelector('.text-end').textContent = `${item.item_total.toFixed(2)}`;
            }
        });

        // Remove items that are no longer in cart
        const currentItemIds = data.cart_items.map(item => item.id);
        Array.from(cartItemsContainer.children).forEach(row => {
            const rowProductId = parseInt(row.id.replace('cart-item-', ''));
            if (!currentItemIds.includes(rowProductId)) {
                row.remove();
            }
        });

        // If cart is empty, display message
        if (data.cart_items.length === 0) {
            cartItemsContainer.innerHTML = '<tr><td colspan="5" class="text-center">Your cart is empty.</td></tr>';
        }

        // Update summary
        cartSubtotalElem.textContent = `${data.cart_total.toFixed(2)}`;
        // Discount handling needs more logic if it's dynamic and not just a fixed value
        // For now, assume discount is part of total calculation or handled separately
        // cartDiscountElem.textContent = `-${data.discount.toFixed(2)}`; // If discount is returned
        cartTotalElem.textContent = `${data.cart_total.toFixed(2)}`; // Assuming total is subtotal - discount

        // Update cart count in header
        if (cartCountBadge) {
            cartCountBadge.textContent = data.cart_count;
        }
    }

    const sendCartUpdate = (productId, quantity, action) => {
        const formData = new FormData();
        formData.append('action', action);
        formData.append('product_id', productId);
        formData.append('quantity', quantity);

        fetch('/smartprozen/cart/update_cart.php', { // Changed endpoint
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // alert(data.message); // Optional: show message
                updateCartDisplay(data);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    };

    // Event listener for quantity changes
    cartItemsContainer.addEventListener('change', e => {
        if (e.target.classList.contains('quantity-input')) {
            const productId = e.target.dataset.id;
            const quantity = e.target.value;
            sendCartUpdate(productId, quantity, 'update');
        }
    });

    // Event listener for remove buttons
    cartItemsContainer.addEventListener('click', e => {
        if (e.target.classList.contains('remove-item')) {
            const productId = e.target.dataset.id;
            if (confirm('Are you sure you want to remove this item from your cart?')) {
                sendCartUpdate(productId, 0, 'update'); // Update with quantity 0 to remove
            }
        }
    });

    // Coupon form AJAX submission
    const couponForm = document.querySelector('form[action="apply_coupon.php"]');
    if (couponForm) {
        couponForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('/smartprozen/cart/apply_coupon.php', { // Endpoint for coupon
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    // Dynamically update discount and total
                    const currentSubtotal = parseFloat(cartSubtotalElem.textContent.replace('$', ''));
                    let newTotal = currentSubtotal - data.discount_amount;

                    // Remove existing discount display if any
                    const existingDiscountRow = document.querySelector('#cart-summary-discount-row');
                    if (existingDiscountRow) {
                        existingDiscountRow.remove();
                    }

                    if (data.discount_amount > 0) {
                        const discountHtml = `
                            <div class="d-flex justify-content-between align-items-center text-danger mt-2" id="cart-summary-discount-row">
                                <span>Discount (${formData.get('coupon_code')})</span>
                                <span id="cart-discount">-$${data.discount_amount.toFixed(2)}</span>
                            </div>
                        `;
                        cartSubtotalElem.closest('.card-body').insertBefore(
                            document.createRange().createContextualFragment(discountHtml),
                            cartTotalElem.closest('.d-flex').previousElementSibling // Insert before total row
                        );
                    }
                    cartTotalElem.textContent = `$${newTotal.toFixed(2)}`;

                } else {
                    alert('Error: ' + data.message);
                    // Also remove discount if coupon is invalid
                    const existingDiscountRow = document.querySelector('#cart-summary-discount-row');
                    if (existingDiscountRow) {
                        existingDiscountRow.remove();
                    }
                    // Recalculate total to subtotal if discount removed
                    cartTotalElem.textContent = cartSubtotalElem.textContent;
                }
            })
            .catch(error => {
                console.error('Error applying coupon:', error);
                alert('An error occurred while applying coupon. Please try again.');
            });
        });
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>