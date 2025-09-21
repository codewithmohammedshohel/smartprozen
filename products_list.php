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
                $sql .= " AND (JSON_EXTRACT(name, '$.en') LIKE ? OR JSON_EXTRACT(description, '$.en') LIKE ?)";
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
                                    <div class="btn-group" role="group">
                                        <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="cart/add_to_cart.php?product_id=<?php echo $product['id']; ?>&quantity=1" 
                                           class="btn btn-primary btn-sm">
                                            <i class="bi bi-cart-plus"></i>
                                        </a>
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

<?php include 'includes/footer.php'; ?>
