<?php
// Assumes $content has 'product_ids'
$product_ids = $content['product_ids'] ?? [];
if (!empty($product_ids)):
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
    $stmt->execute();
    $products = $stmt->get_result();
?>
<div class="featured-products-section" data-aos="fade-up">
    <h2>Featured Products</h2>
    <div class="product-grid">
        <?php while($product = $products->fetch_assoc()): ?>
            <?php endwhile; ?>
    </div>
</div>
<?php endif; ?>