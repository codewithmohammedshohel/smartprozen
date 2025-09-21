<?php
require_once __DIR__ . '/../includes/header.php';

if (!is_logged_in()) {
    header('Location: /smartprozen/auth/login.php?redirect_to=/cart/checkout.php');
    exit;
}

if (empty($_SESSION['cart'])) {
    header('Location: /smartprozen/cart/');
    exit;
}

$cart_items = [];
$subtotal = 0;
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

$discount = $_SESSION['discount_amount'] ?? 0;
$total = $subtotal - $discount;

$user = get_user_by_id($_SESSION['user_id'], $conn);

?>
<div class="container py-5">
    <h1 class="mb-4">Checkout</h1>
    <div class="row">
        <div class="col-md-7">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="card-title h4">Shipping Information</h2>
                    <form id="checkout-form">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="zip" class="form-label">ZIP Code</label>
                                <input type="text" class="form-control" id="zip" name="zip" required>
                            </div>
                        </div>
                        
                        <h2 class="card-title h4 mt-4">Payment Method</h2>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                            <label class="form-check-label" for="cod">
                                Cash on Delivery
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="stripe" value="stripe" disabled>
                            <label class="form-check-label" for="stripe">
                                Credit Card (Stripe) - Coming Soon
                            </label>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="card-title h4">Order Summary</h2>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($cart_items as $item): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo get_translated_text($item['name'], 'name'); ?> (x<?php echo $item['quantity']; ?>)
                                <span>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                        <strong>Subtotal</strong>
                        <strong>$<?php echo number_format($subtotal, 2); ?></strong>
                    </div>
                    <?php if ($discount > 0): ?>
                        <div class="d-flex justify-content-between align-items-center text-danger">
                            <span>Discount (<?php echo $_SESSION['coupon_code']; ?>)</span>
                            <span>-$<?php echo number_format($discount, 2); ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between align-items-center mt-2 h5">
                        <strong>Total</strong>
                        <strong>$<?php echo number_format($total, 2); ?></strong>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" form="checkout-form" class="btn btn-primary btn-lg">Place Order</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkoutForm = document.getElementById('checkout-form');
    const placeOrderBtn = document.querySelector('button[form="checkout-form"]');

    if (checkoutForm && placeOrderBtn) {
        placeOrderBtn.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default button submission

            // Manually trigger form submission to ensure validation runs
            if (!checkoutForm.checkValidity()) {
                checkoutForm.reportValidity();
                return;
            }

            const formData = new FormData(checkoutForm);

            fetch('/smartprozen/cart/place_order.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.href = `/smartprozen/order_confirmation.php?id=${data.order_id}`;
                } else {
                    alert('Error placing order: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while placing your order. Please try again.');
            });
        });
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>