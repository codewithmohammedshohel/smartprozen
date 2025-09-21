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
    $where_clause = " WHERE name LIKE ? OR email LIKE ? OR address LIKE ? OR contact_number LIKE ?";
    $like_term = "%{$search_term}%";
    $params[] = $like_term;
    $params[] = $like_term;
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
    <div class="container-fluid px-4">
    <h1 class="mt-4">Manage Customers</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Manage Customers</li>
    </ol>

        <div class="card shadow-sm mb-4">
    <div class="card-header">
        <i class="bi bi-search me-1"></i>
        Search Customers
    </div>
    <div class="card-body">
        <form action="manage_customers.php" method="GET" class="d-flex">
            <input type="text" name="search" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($search_term); ?>" class="form-control me-2 mb-3">
            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
        </form>
    </div>
</div>

        <div class="card shadow-sm mb-4">
    <div class="card-header">
        <i class="bi bi-people-fill me-1"></i>
        Customer List
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact Number</th>
                        <th>Address</th>
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
                        <td><?php echo htmlspecialchars($customer['contact_number'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($customer['address'] ?? 'N/A'); ?></td>
                        <td><?php echo date("M j, Y", strtotime($customer['created_at'])); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($customer['provider'] ?? 'N/A')); ?></td>
                        <td>
                            <a href="view_orders.php?customer_id=<?php echo $customer['id']; ?>" class="btn btn-sm btn-outline-info me-2"><i class="bi bi-eye"></i> View Orders</a>
                            <a href="manage_customers.php?action=edit&id=<?php echo $customer['id']; ?>" class="btn btn-sm btn-outline-primary me-2">Edit</a>
                            <a href="handle_customer.php?action=delete&id=<?php echo $customer['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this customer?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

        <nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?php if($i == $page) echo 'active'; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo htmlspecialchars($search_term); ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
    </div>
</div>

<?php require_once '../includes/admin_footer.php'; ?>