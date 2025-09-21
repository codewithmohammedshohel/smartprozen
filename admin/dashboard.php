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

$total_users = $conn->query("SELECT COUNT(id) as total FROM users")->fetch_assoc()['total'] ?? 0;

// Fetch recent activity
$recent_activity = $conn->query("SELECT * FROM activity_logs ORDER BY timestamp DESC LIMIT 5");

require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/admin_sidebar.php';

?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Dashboard</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <i class="bi bi-cash-coin fs-1"></i>
                        <div>
                            <div class="fs-3 fw-bold">$<?php echo number_format($total_sales, 2); ?></div>
                            <div>Total Revenue</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <i class="bi bi-people-fill fs-1"></i>
                        <div>
                            <div class="fs-3 fw-bold"><?php echo $total_users; ?></div>
                            <div>Total Customers</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <i class="bi bi-box-seam-fill fs-1"></i>
                        <div>
                            <div class="fs-3 fw-bold"><?php echo $total_orders; ?></div>
                            <div>Total Orders</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <i class="bi bi-exclamation-triangle-fill fs-1"></i>
                        <div>
                            <div class="fs-3 fw-bold"><?php echo $pending_orders; ?></div>
                            <div>Pending Orders</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-bar-chart-fill me-1"></i>
                    Sales This Week
                </div>
                <div class="card-body"><canvas id="salesChart" width="100%" height="40"></canvas></div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-activity me-1"></i>
                    Recent Activity
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <?php while($activity = $recent_activity->fetch_assoc()): ?>
                            <li class="list-group-item">
                                <strong><?php echo htmlspecialchars($activity['action']); ?>:</strong>
                                <span><?php echo htmlspecialchars($activity['details']); ?></span>
                                <small class="text-muted d-block"><?php echo date("M j, g:i a", strtotime($activity['timestamp'])); ?></small>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
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