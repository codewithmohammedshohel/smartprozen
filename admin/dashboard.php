<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/functions.php';

// Authenticate and authorize admin
if (!is_admin_logged_in()) {
    header('Location: /smartprozen/admin/login.php');
    exit;
}
if (!has_permission('view_dashboard')) { // Example permission
    $_SESSION['error_message'] = "You don't have permission to view the dashboard.";
    header('Location: /smartprozen/admin/login.php'); // Redirect to admin login with error
    exit;
}

// Fetch dashboard metrics
$metrics_query = $conn->query("SELECT
    SUM(CASE WHEN status = 'Completed' THEN total_amount ELSE 0 END) as total_sales,
    COUNT(id) as total_orders,
    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending_orders
FROM orders");
$metrics = $metrics_query->fetch_assoc();

$total_sales = $metrics['total_sales'] ?? 0;
$total_orders = $metrics['total_orders'] ?? 0;
$pending_orders = $metrics['pending_orders'] ?? 0;

// Calculate Total Registered Customers
$total_registered_customers = $conn->query("SELECT COUNT(id) as total FROM users")->fetch_assoc()['total'] ?? 0;

    // Calculate Total Paid Customers (users with at least one completed order)
$total_paid_customers_query = $conn->query("SELECT COUNT(DISTINCT user_id) as total FROM orders WHERE status = 'Completed'");
$total_paid_customers = $total_paid_customers_query->fetch_assoc()['total'] ?? 0;

// Calculate Total Products
$total_products_query = $conn->query("SELECT COUNT(id) as total FROM products");
$total_products = $total_products_query->fetch_assoc()['total'] ?? 0;

// Calculate Total Reviews
$total_reviews_query = $conn->query("SELECT COUNT(id) as total FROM reviews");
$total_reviews = $total_reviews_query->fetch_assoc()['total'] ?? 0;

// Fetch recent admin activity
$recent_admin_activity = $conn->query("SELECT * FROM activity_logs WHERE user_type = 'admin' ORDER BY timestamp DESC LIMIT 5");

// Fetch recent user activity
$recent_user_activity = $conn->query("SELECT * FROM activity_logs WHERE user_type = 'user' ORDER BY timestamp DESC LIMIT 5");

// Fetch Top-Selling Products (by quantity sold)
$top_selling_products_query = $conn->query("SELECT p.id, p.name, SUM(oi.quantity) as total_quantity_sold
                                            FROM order_items oi
                                            JOIN products p ON oi.product_id = p.id
                                            GROUP BY p.id, p.name
                                            ORDER BY total_quantity_sold DESC
                                            LIMIT 5");
$top_selling_products = $top_selling_products_query->fetch_all(MYSQLI_ASSOC);

// Fetch Recent Orders List
$recent_orders_query = $conn->query("SELECT o.id, o.total_amount, o.status, u.name as customer_name, o.created_at
                                     FROM orders o
                                     JOIN users u ON o.user_id = u.id
                                     ORDER BY o.created_at DESC
                                     LIMIT 5");
$recent_orders = $recent_orders_query->fetch_all(MYSQLI_ASSOC);

// Fetch Low Stock Alerts (assuming 'stock_quantity' column in 'products' table)
// Define a low stock threshold
$low_stock_threshold = 5;
$low_stock_products_query = $conn->prepare("SELECT id, name, stock_quantity FROM products WHERE stock_quantity <= ? ORDER BY stock_quantity ASC LIMIT 5");
$low_stock_products_query->bind_param("i", $low_stock_threshold);
$low_stock_products_query->execute();
$low_stock_products = $low_stock_products_query->get_result()->fetch_all(MYSQLI_ASSOC);
$low_stock_products_query->close();

// Fetch New Customers (This Month)
$current_month_start = date('Y-m-01 00:00:00');
$new_customers_query = $conn->prepare("SELECT COUNT(id) as total FROM users WHERE created_at >= ?");
$new_customers_query->bind_param("s", $current_month_start);
$new_customers_query->execute();
$new_customers_this_month = $new_customers_query->get_result()->fetch_assoc()['total'] ?? 0;
$new_customers_query->close();

require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/admin_sidebar.php';

?>
<div class="container-fluid px-4">
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h1 class="h4 mb-0">Dashboard</h1>
        </div>
        <div class="card-body">
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card metric-card card-revenue">
                        <div class="card-body">
                            <div class="metric-value">$<?php echo number_format($total_sales, 2); ?></div>
                            <div class="metric-label">Total Revenue</div>
                            <div class="card-icon"><i class="bi bi-cash-coin"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card metric-card card-customers">
                        <div class="card-body">
                            <div class="metric-value"><?php echo $total_paid_customers; ?></div>
                            <div class="metric-label">Total Paid Customers</div>
                            <div class="card-icon"><i class="bi bi-person-check-fill"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card metric-card card-users">
                        <div class="card-body">
                            <div class="metric-value"><?php echo $total_registered_customers; ?></div>
                            <div class="metric-label">Total Registered Customers</div>
                            <div class="card-icon"><i class="bi bi-people-fill"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card metric-card card-orders">
                        <div class="card-body">
                            <div class="metric-value"><?php echo $total_orders; ?></div>
                            <div class="metric-label">Total Orders</div>
                            <div class="card-icon"><i class="bi bi-box-seam-fill"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card metric-card card-pending">
                        <div class="card-body">
                            <div class="metric-value"><?php echo $pending_orders; ?></div>
                            <div class="metric-label">Pending Orders</div>
                            <div class="card-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card metric-card card-products">
                        <div class="card-body">
                            <div class="metric-value"><?php echo $total_products; ?></div>
                            <div class="metric-label">Total Products</div>
                            <div class="card-icon"><i class="bi bi-box-seam"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card metric-card card-reviews">
                        <div class="card-body">
                            <div class="metric-value"><?php echo $total_reviews; ?></div>
                            <div class="metric-label">Total Reviews</div>
                            <div class="card-icon"><i class="bi bi-star-fill"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card metric-card card-new-customers">
                        <div class="card-body">
                            <div class="metric-value"><?php echo $new_customers_this_month; ?></div>
                            <div class="metric-label">New Customers (This Month)</div>
                            <div class="card-icon"><i class="bi bi-person-plus-fill"></i></div>
                        </div>
                    </div>
                </div>
            </div>
    <div class="row">
        <div class="col-xl-6">
            <div class="card shadow mb-4 card-hover-effect h-100">
                <div class="card-header">
                    <i class="bi bi-bar-chart-fill me-1"></i>
                    Sales This Week
                </div>
                <div class="card-body"><canvas id="salesChart" width="100%" height="40"></canvas></div>
            </div>
        </div>
                <div class="col-xl-6">
                    <div class="card shadow mb-4 card-hover-effect h-100">
                        <div class="card-header">
                            <i class="bi bi-person-gear me-1"></i>
                            Recent Admins Activity
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <?php if ($recent_admin_activity->num_rows > 0): ?>
                                    <?php while($activity = $recent_admin_activity->fetch_assoc()): ?>
                                        <li class="list-group-item py-2">
                                            <strong><?php echo htmlspecialchars($activity['action']); ?>:</strong>
                                            <span><?php echo htmlspecialchars($activity['details']); ?></span>
                                            <small class="text-muted d-block"><?php echo date("M j, g:i a", strtotime($activity['timestamp'])); ?></small>
                                        </li>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <li class="list-group-item">No recent admin activity.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>

            <div class="row"> <!-- New row for Top-Selling Products and Recent Orders -->
                <div class="col-xl-6">
                    <div class="card shadow mb-4 card-hover-effect h-100">
                        <div class="card-header">
                            <i class="bi bi-graph-up me-1"></i>
                            Top-Selling Products
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <?php if (!empty($top_selling_products)) { ?>
                                    <?php foreach ($top_selling_products as $product) { ?>
                                        <li class="list-group-item py-2">
                                            <strong><?php echo htmlspecialchars(get_translated_text($product['name'], 'name')); ?>:</strong>
                                            <span><?php echo htmlspecialchars($product['total_quantity_sold']); ?> units sold</span>
                                        </li>
                                    <?php } ?>
                                <?php } else { ?>
                                    <li class="list-group-item">No top-selling products yet.</li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="card shadow mb-4 card-hover-effect h-100">
                        <div class="card-header">
                            <i class="bi bi-receipt me-1"></i>
                            Recent Orders
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <?php if (!empty($recent_orders)) { ?>
                                    <?php foreach ($recent_orders as $order) { ?>
                                        <li class="list-group-item py-2">
                                            <strong>#<?php echo htmlspecialchars($order['id']); ?></strong> from <?php echo htmlspecialchars($order['customer_name']); ?>
                                            <span class="float-end">$<?php echo number_format($order['total_amount'], 2); ?></span><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($order['status']); ?> - <?php echo date("M j, g:i a", strtotime($order['created_at'])); ?></small>
                                        </li>
                                    <?php } ?>
                                <?php } else { ?>
                                    <li class="list-group-item">No recent orders.</li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row"> <!-- New row for Recent Users Activity and Low Stock Alerts -->
                <div class="col-xl-6">
                    <div class="card shadow mb-4 card-hover-effect h-100">
                        <div class="card-header">
                            <i class="bi bi-person me-1"></i>
                            Recent Users Activity
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <?php if ($recent_user_activity->num_rows > 0): ?>
                                    <?php while($activity = $recent_user_activity->fetch_assoc()): ?>
                                        <li class="list-group-item py-2">
                                            <strong><?php echo htmlspecialchars($activity['action']); ?>:</strong>
                                            <span><?php echo htmlspecialchars($activity['details']); ?></span>
                                            <small class="text-muted d-block"><?php echo date("M j, g:i a", strtotime($activity['timestamp'])); ?></small>
                                        </li>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <li class="list-group-item">No recent user activity.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="card shadow mb-4 card-hover-effect h-100">
                        <div class="card-header">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            Low Stock Alerts
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <?php if (!empty($low_stock_products)) { ?>
                                    <?php foreach ($low_stock_products as $product) { ?>
                                        <li class="list-group-item py-2">
                                            <strong><?php echo htmlspecialchars(get_translated_text($product['name'], 'name')); ?>:</strong>
                                            <span class="float-end text-danger"><?php echo htmlspecialchars($product['stock_quantity']); ?> in stock</span>
                                        </li>
                                    <?php } ?>
                                <?php } else { ?>
                                    <li class="list-group-item">No low stock products.</li>
                                <?php } ?>
                            </ul>
                            <?php // End of low stock products list ?>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- Close card-body -->
    </div> <!-- Close card -->
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        fetch('/smartprozen/api/sales_data.php')
            .then(response => response.json())
            .then(data => {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Daily Sales ($)',
                            data: data.values,
                            backgroundColor: 'rgba(0, 123, 255, 0.5)',
                            borderColor: 'rgba(0, 123, 255, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: { scales: { y: { beginAtZero: true } } }
                });
            });
    });
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>