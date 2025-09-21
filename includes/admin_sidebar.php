<?php
require_once __DIR__ . '/../core/functions.php';

$current_page = basename($_SERVER['PHP_SELF']);
$admin_user = get_current_admin();

function is_active($page) {
    global $current_page;
    return $current_page === $page ? 'active' : '';
}
?>
<div class="d-flex flex-column flex-shrink-0" id="sidebar-wrapper">
    <div class="sidebar-header text-center">
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle fs-2 me-2"></i>
                <div>
                    <h5 class="mb-0"><?php echo htmlspecialchars($admin_user['username'] ?? 'Admin'); ?></h5>
                    <small>Administrator</small>
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                <li><a class="dropdown-item" href="/smartprozen/" target="_blank"><i class="bi bi-box-arrow-up-right me-2"></i>View Site</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="/smartprozen/admin/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Sign out</a></li>
            </ul>
        </div>
    </div>
    <ul class="nav nav-pills flex-column mb-auto pt-3">
        <li class="nav-item">
            <a href="/smartprozen/admin/dashboard.php" class="nav-link <?php echo is_active('dashboard.php'); ?>">
                <i class="bi bi-grid-fill me-2"></i> Dashboard
            </a>
        </li>
        
        <li class="nav-heading px-3 mt-3 mb-1">Store</li>
        <li>
            <a href="/smartprozen/admin/view_orders.php" class="nav-link <?php echo is_active('view_orders.php'); ?>">
                <i class="bi bi-box-seam-fill me-2"></i> Orders
            </a>
        </li>
        <?php if (is_module_enabled('products', $conn)): ?>
        <li>
            <a href="/smartprozen/admin/manage_products.php" class="nav-link <?php echo is_active('manage_products.php'); ?>">
                <i class="bi bi-box-fill me-2"></i> Products
            </a>
        </li>
        <li>
            <a href="/smartprozen/admin/manage_categories.php" class="nav-link <?php echo is_active('manage_categories.php'); ?>">
                <i class="bi bi-tags-fill me-2"></i> Categories
            </a>
        </li>
        <?php endif; ?>
        <li>
            <a href="/smartprozen/admin/manage_customers.php" class="nav-link <?php echo is_active('manage_customers.php'); ?>">
                <i class="bi bi-people-fill me-2"></i> Customers
            </a>
        </li>
        <?php if (is_module_enabled('coupons', $conn)): ?>
        <li>
            <a href="/smartprozen/admin/manage_coupons.php" class="nav-link <?php echo is_active('manage_coupons.php'); ?>">
                <i class="bi bi-ticket-percent-fill me-2"></i> Coupons
            </a>
        </li>
        <?php endif; ?>
        <li>
            <a href="/smartprozen/admin/reports.php" class="nav-link <?php echo is_active('reports.php'); ?>">
                <i class="bi bi-file-bar-graph-fill me-2"></i> Reports
            </a>
        </li>

        <li class="nav-heading px-3 mt-3 mb-1">Content</li>
        <li>
            <a href="/smartprozen/admin/manage_pages.php" class="nav-link <?php echo is_active('manage_pages.php'); ?>">
                <i class="bi bi-file-earmark-fill me-2"></i> Pages & Menu
            </a>
        </li>
        <?php if (is_module_enabled('blog', $conn)): ?>
        <li>
             <a href="/smartprozen/admin/manage_posts.php" class="nav-link <?php echo is_active('manage_posts.php'); ?>">
                <i class="bi bi-file-post-fill me-2"></i> Blog
            </a>
        </li>
        <?php endif; ?>
        <li>
            <a href="/smartprozen/admin/media_library.php" class="nav-link <?php echo is_active('media_library.php'); ?>">
                <i class="bi bi-image-fill me-2"></i> Media Library
            </a>
        </li>
        <li>
            <a href="/smartprozen/admin/manage_reviews.php" class="nav-link <?php echo is_active('manage_reviews.php'); ?>">
                <i class="bi bi-star-fill me-2"></i> Reviews
            </a>
        </li>
        <li>
            <a href="/smartprozen/admin/manage_testimonials.php" class="nav-link <?php echo is_active('manage_testimonials.php'); ?>">
                <i class="bi bi-chat-quote me-2"></i> Testimonials
            </a>
        </li>

        <li class="nav-heading px-3 mt-3 mb-1">System</li>
        <li>
            <a href="/smartprozen/admin/settings.php" class="nav-link <?php echo is_active('settings.php'); ?>">
                <i class="bi bi-gear-fill me-2"></i> Site Settings
            </a>
        </li>
        <li>
            <a href="/smartprozen/admin/theme_settings.php" class="nav-link <?php echo is_active('theme_settings.php'); ?>">
                <i class="bi bi-palette-fill me-2"></i> Theme
            </a>
        </li>
        <li>
            <a href="/smartprozen/admin/manage_gateways.php" class="nav-link <?php echo is_active('manage_gateways._php'); ?>">
                <i class="bi bi-credit-card-fill me-2"></i> Payment Gateways
            </a>
        </li>
        <li>
            <a href="/smartprozen/admin/manage_modules.php" class="nav-link <?php echo is_active('manage_modules.php'); ?>">
                <i class="bi bi-box-fill me-2"></i> Modules
            </a>
        </li>
        <li>
            <a href="/smartprozen/admin/manage_admins.php" class="nav-link <?php echo is_active('manage_admins.php'); ?>">
                <i class="bi bi-person-plus-fill me-2"></i> Admins & Roles
            </a>
        </li>
    </ul>
</div>
