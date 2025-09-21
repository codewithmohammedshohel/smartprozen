<?php
require_once '../config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

// Authenticate and authorize admin
if (!is_admin_logged_in() || !has_permission('manage_users')) {
    header('Location: /smartprozen/admin/login.php');
    exit;
}

// Search functionality
$search_term = $_GET['search'] ?? '';
$where_clause = '';
$params = [];
if (!empty($search_term)) {
    $where_clause = " WHERE name LIKE ? OR email LIKE ?";
    $like_term = "%{$search_term}%";
    $params[] = $like_term;
    $params[] = $like_term;
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

$count_stmt = $conn->prepare("SELECT COUNT(id) FROM users" . $where_clause);
if (!empty($params)) {
    $count_stmt->bind_param(str_repeat('s', count($params)), ...$params);
}
$count_stmt->execute();
$total_customers = $count_stmt->get_result()->fetch_row()[0];
$total_pages = ceil($total_customers / $limit);

$stmt = $conn->prepare("SELECT * FROM users" . $where_clause . " ORDER BY created_at DESC LIMIT ? OFFSET ?");
$param_types = str_repeat('s', count($params)) . 'ii';
$final_params = array_merge($params, [$limit, $offset]);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$final_params);
} else {
    $stmt->bind_param("ii", $limit, $offset);
}
$stmt->execute();
$customers = $stmt->get_result();

require_once '../includes/admin_header.php';
?>

<div class="dashboard-layout">
    <?php require_once '../includes/admin_sidebar.php'; ?>
    <div class="dashboard-content">
        <div class="dashboard-header">
            <h1>Manage Customers</h1>
        </div>

        <div class="search-container">
            <form action="manage_customers.php" method="GET">
                <input type="text" name="search" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($search_term); ?>">
                <button type="submit" class="btn">Search</button>
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Registered On</th>
                        <th>Provider</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($customer = $customers->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $customer['id']; ?></td>
                        <td><?php echo htmlspecialchars($customer['name']); ?></td>
                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                        <td><?php echo htmlspecialchars($customer['phone_number'] ?? 'N/A'); ?></td>
                        <td><?php echo date("M j, Y", strtotime($customer['created_at'])); ?></td>
                        <td><?php echo ucfirst($customer['provider']); ?></td>
                        <td>
                            <a href="view_orders.php?customer_id=<?php echo $customer['id']; ?>" class="btn btn-secondary">View Orders</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo htmlspecialchars($search_term); ?>" class="<?php if($i == $page) echo 'active'; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/admin_footer.php'; ?>