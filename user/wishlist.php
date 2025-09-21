<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/functions.php';

if (!is_logged_in()) {
    header('Location: /smartprozen/auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch wishlist items
$wishlist_items = [];
$stmt = $conn->prepare("SELECT p.id, p.name, p.price, p.image_filename FROM wishlist w JOIN products p ON w.product_id = p.id WHERE w.user_id = ? ORDER BY w.created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $wishlist_items[] = $row;
}
$stmt->close();

require_once __DIR__ . '/../includes/user_header.php';
?>

<div class="row">
    <?php require_once __DIR__ . '/../includes/user_sidebar.php'; ?>
    <div class="col-md-9">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="card-title">My Wishlist</h1>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Product</th>
                                <th scope="col">Price</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($wishlist_items)): ?>
                                <?php foreach ($wishlist_items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo SITE_URL . '/uploads/media/thumb-' . htmlspecialchars($item['image_filename']); ?>" alt="<?php echo htmlspecialchars(get_translated_text($item['name'], 'name')); ?>" class="img-fluid rounded me-3" style="width: 60px;">
                                            <a href="<?php echo SITE_URL . '/product.php?id=' . $item['id']; ?>" class="text-decoration-none text-dark">
                                                <?php echo htmlspecialchars(get_translated_text($item['name'], 'name')); ?>
                                            </a>
                                        </div>
                                    </td>
                                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger remove-from-wishlist-btn" data-product-id="<?php echo $item['id']; ?>">Remove</button>
                                        <form action="<?php echo SITE_URL; ?>/cart/add_to_cart.php" method="POST" class="d-inline-block ms-2">
                                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn btn-sm btn-primary">Add to Cart</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">Your wishlist is empty.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.remove-from-wishlist-btn').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Are you sure you want to remove this item from your wishlist?')) {
                const productId = this.dataset.productId;
                
                fetch('<?php echo SITE_URL; ?>/api/wishlist_handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `product_id=${productId}&action=remove`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload(); // Reload page to update wishlist
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>