<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/functions.php';

if (!is_logged_in()) {
    header('Location: /smartprozen/auth/login.php');
    exit;
}

$user = get_user_by_id($_SESSION['user_id'], $conn);
$page_title = __('my_dashboard');

// Get user's recent orders
$orders_stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$orders_stmt->bind_param("i", $_SESSION['user_id']);
$orders_stmt->execute();
$recent_orders = $orders_stmt->get_result();
$orders_stmt->close();

// Get user's wishlist count
$wishlist_stmt = $conn->prepare("SELECT COUNT(*) as count FROM wishlist WHERE user_id = ?");
$wishlist_stmt->bind_param("i", $_SESSION['user_id']);
$wishlist_stmt->execute();
$wishlist_count = $wishlist_stmt->get_result()->fetch_assoc()['count'];
$wishlist_stmt->close();

// Get user's download count
$downloads_stmt = $conn->prepare("SELECT COUNT(*) as count FROM downloads WHERE user_id = ?");
$downloads_stmt->bind_param("i", $_SESSION['user_id']);
$downloads_stmt->execute();
$downloads_count = $downloads_stmt->get_result()->fetch_assoc()['count'];
$downloads_stmt->close();

include __DIR__ . '/../includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-3">
            <div class="card shadow-medium">
                <div class="card-body">
                    <h5 class="card-title"><?php echo __('my_account'); ?></h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <a href="/smartprozen/user/dashboard.php" class="text-decoration-none active">
                                <i class="bi bi-house-door me-2"></i><?php echo __('dashboard'); ?>
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="/smartprozen/user/orders.php" class="text-decoration-none">
                                <i class="bi bi-bag me-2"></i><?php echo __('my_orders'); ?>
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="/smartprozen/user/downloads.php" class="text-decoration-none">
                                <i class="bi bi-download me-2"></i><?php echo __('downloads'); ?>
                                <?php if ($downloads_count > 0): ?>
                                    <span class="badge bg-primary"><?php echo $downloads_count; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="/smartprozen/user/wishlist.php" class="text-decoration-none">
                                <i class="bi bi-heart me-2"></i><?php echo __('wishlist'); ?>
                                <?php if ($wishlist_count > 0): ?>
                                    <span class="badge bg-danger"><?php echo $wishlist_count; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="/smartprozen/user/profile.php" class="text-decoration-none">
                                <i class="bi bi-person me-2"></i><?php echo __('profile'); ?>
                            </a>
                        </li>
                        <li>
                            <a href="/smartprozen/auth/logout.php" class="text-decoration-none text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i><?php echo __('logout'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-lg-9">
            <div class="row mb-4">
                <div class="col-12">
                    <h2 class="mb-0"><?php echo __('welcome_back'); ?>, <?php echo htmlspecialchars($user['name']); ?>!</h2>
                    <p class="text-muted"><?php echo __('dashboard_description'); ?></p>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title"><?php echo __('total_orders'); ?></h4>
                                    <h2 class="mb-0"><?php echo $recent_orders->num_rows; ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-bag fs-1 opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title"><?php echo __('downloads'); ?></h4>
                                    <h2 class="mb-0"><?php echo $downloads_count; ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-download fs-1 opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title"><?php echo __('wishlist'); ?></h4>
                                    <h2 class="mb-0"><?php echo $wishlist_count; ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-heart fs-1 opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-medium">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><?php echo __('recent_orders'); ?></h5>
                        </div>
                        <div class="card-body">
                            <?php if ($recent_orders->num_rows > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th><?php echo __('order_number'); ?></th>
                                                <th><?php echo __('order_date'); ?></th>
                                                <th><?php echo __('total'); ?></th>
                                                <th><?php echo __('status'); ?></th>
                                                <th><?php echo __('actions'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($order = $recent_orders->fetch_assoc()): ?>
                                                <tr>
                                                    <td>#<?php echo $order['id']; ?></td>
                                                    <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                                                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php 
                                                            echo $order['status'] === 'Completed' ? 'success' : 
                                                                ($order['status'] === 'Pending' ? 'warning' : 'secondary'); 
                                                        ?>">
                                                            <?php echo $order['status']; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="/smartprozen/user/view_order_details.php?id=<?php echo $order['id']; ?>" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <?php echo __('view_details'); ?>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="/smartprozen/user/orders.php" class="btn btn-primary">
                                        <?php echo __('view_all_orders'); ?>
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="bi bi-bag fs-1 text-muted mb-3"></i>
                                    <h5 class="text-muted"><?php echo __('no_orders_yet'); ?></h5>
                                    <p class="text-muted"><?php echo __('start_shopping_message'); ?></p>
                                    <a href="/smartprozen/products_list.php" class="btn btn-primary">
                                        <?php echo __('start_shopping'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>