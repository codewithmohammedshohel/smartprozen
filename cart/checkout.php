<?php
require_once '../config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

if (empty($_SESSION['cart'])) {
    header('Location: index.php');
    exit;
}

$page_title = 'Checkout';
include '../includes/header.php';
?>

<div class="container my-5">
    <h1 class="mb-4">Checkout</h1>
    
    <div class="row">
        <div class="col-lg-8">
            <form id="checkoutForm">
                <!-- Billing Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Billing Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="firstName" class="form-label">First Name *</label>
                                <input type="text" class="form-control" id="firstName" name="first_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lastName" class="form-label">Last Name *</label>
                                <input type="text" class="form-control" id="lastName" name="last_name" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone">
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address *</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="city" class="form-label">City *</label>
                                <input type="text" class="form-control" id="city" name="city" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="state" class="form-label">State *</label>
                                <input type="text" class="form-control" id="state" name="state" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="zip" class="form-label">ZIP Code *</label>
                                <input type="text" class="form-control" id="zip" name="zip_code" required>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Payment Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="creditCard" value="credit_card" checked>
                                <label class="form-check-label" for="creditCard">
                                    Credit Card
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal">
                                <label class="form-check-label" for="paypal">
                                    PayPal
                                </label>
                            </div>
                        </div>
                        <div id="creditCardFields">
                            <div class="mb-3">
                                <label for="cardNumber" class="form-label">Card Number *</label>
                                <input type="text" class="form-control" id="cardNumber" name="card_number" placeholder="1234 5678 9012 3456">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="expiryDate" class="form-label">Expiry Date *</label>
                                    <input type="text" class="form-control" id="expiryDate" name="expiry_date" placeholder="MM/YY">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cvv" class="form-label">CVV *</label>
                                    <input type="text" class="form-control" id="cvv" name="cvv" placeholder="123">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <?php 
                    $cart_total = 0;
                    if (!empty($_SESSION['cart'])) {
                        $product_ids = array_keys($_SESSION['cart']);
                        $placeholders = str_repeat('?,', count($product_ids) - 1) . '?';
                        $stmt = $conn->prepare("SELECT id, name, price, sale_price FROM products WHERE id IN ($placeholders)");
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
                                $line_total = $price * $quantity;
                                $cart_total += $line_total;
                    ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><?php echo htmlspecialchars($product['name']); ?> x<?php echo $quantity; ?></span>
                            <span><?php echo format_price($line_total); ?></span>
                        </div>
                    <?php 
                            endif;
                        endforeach;
                    }
                    ?>
                    
                    <hr>
                    
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
                    
                    <button type="button" class="btn btn-primary w-100" onclick="placeOrder()">Place Order</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function placeOrder() {
    const form = document.getElementById('checkoutForm');
    const formData = new FormData(form);
    
    // Add cart data
    formData.append('cart_data', JSON.stringify(<?php echo json_encode($_SESSION['cart']); ?>));
    formData.append('total_amount', <?php echo get_cart_total() + 9.99; ?>);
    
    fetch('place_order.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Order placed successfully!');
            window.location.href = '/smartprozen/order_confirmation.php?order_id=' + data.order_id;
        } else {
            alert('Error placing order: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

// Show/hide credit card fields based on payment method
document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const creditCardFields = document.getElementById('creditCardFields');
        if (this.value === 'credit_card') {
            creditCardFields.style.display = 'block';
        } else {
            creditCardFields.style.display = 'none';
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>