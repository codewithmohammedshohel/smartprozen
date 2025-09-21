<?php
$current_page = basename($_SERVER['PHP_SELF']);

function is_active($page) {
    global $current_page;
    return $current_page === $page ? 'active' : '';
}
?>
<div class="bg-dark border-right" id="sidebar-wrapper">
    <div class="sidebar-heading text-white">SmartProzen Apex</div>
    <div class="list-group list-group-flush">
        <a href="/smartprozen/admin/dashboard.php" class="list-group-item list-group-item-action bg-dark text-white <?php echo is_active('dashboard.php'); ?>"><i class="bi bi-grid-fill me-2"></i>Dashboard</a>
        
        <?php if (is_module_enabled('orders', $conn)): ?>
            <a href="/smartprozen/admin/view_orders.php" class="list-group-item list-group-item-action bg-dark text-white <?php echo is_active('view_orders.php'); ?>"><i class="bi bi-box-seam-fill me-2"></i>Orders</a>
        <?php endif; ?>
        
        <?php if (is_module_enabled('products', $conn)): ?>
            <a href="/smartprozen/admin/manage_products.php" class="list-group-item list-group-item-action bg-dark text-white <?php echo is_active('manage_products.php'); ?>"><i class="bi bi-box-fill me-2"></i>Products</a>
        <?php endif; ?>
        
        <a href="/smartprozen/admin/manage_customers.php" class="list-group-item list-group-item-action bg-dark text-white <?php echo is_active('manage_customers.php'); ?>"><i class="bi bi-people-fill me-2"></i>Customers</a>

        <a href="/smartprozen/admin/manage_reviews.php" class="list-group-item list-group-item-action bg-dark text-white <?php echo is_active('manage_reviews.php'); ?>"><i class="bi bi-star-fill me-2"></i>Reviews</a>

        <a href="/smartprozen/admin/manage_testimonials.php" class="list-group-item list-group-item-action bg-dark text-white <?php echo is_active('manage_testimonials.php'); ?>"><i class="bi bi-chat-quote me-2"></i>Testimonials</a>

        <?php if (is_module_enabled('blog', $conn)): ?>
             <a href="/smartprozen/admin/manage_posts.php" class="list-group-item list-group-item-action bg-dark text-white <?php echo is_active('manage_posts.php'); ?>"><i class="bi bi-file-post-fill me-2"></i>Blog</a>
        <?php endif; ?>

        <a href="/smartprozen/admin/manage_pages.php" class="list-group-item list-group-item-action bg-dark text-white <?php echo is_active('manage_pages.php'); ?>"><i class="bi bi-file-earmark-fill me-2"></i>Pages & Menu</a>
        <a href="/smartprozen/admin/media_library.php" class="list-group-item list-group-item-action bg-dark text-white <?php echo is_active('media_library.php'); ?>"><i class="bi bi-image-fill me-2"></i>Media Library</a>

        <?php if (is_module_enabled('coupons', $conn)): ?>
            <a href="/smartprozen/admin/manage_coupons.php" class="list-group-item list-group-item-action bg-dark text-white <?php echo is_active('manage_coupons.php'); ?>"><i class="bi bi-ticket-percent-fill me-2"></i>Coupons</a>
        <?php endif; ?>

        <div class="list-group-item bg-dark text-white">System</div>
        <a href="/smartprozen/admin/settings.php" class="list-group-item list-group-item-action bg-dark text-white <?php echo is_active('settings.php'); ?>"><i class="bi bi-gear-fill me-2"></i>Site Settings</a>
        <a href="/smartprozen/admin/theme_settings.php" class="list-group-item list-group-item-action bg-dark text-white <?php echo is_active('theme_settings.php'); ?>"><i class="bi bi-palette-fill me-2"></i>Theme & Appearance</a>
        <a href="/smartprozen/admin/manage_gateways.php" class="list-group-item list-group-item-action bg-dark text-white <?php echo is_active('manage_gateways.php'); ?>"><i class="bi bi-credit-card-fill me-2"></i>Payment Gateways</a>
        <a href="/smartprozen/admin/manage_admins.php" class="list-group-item list-group-item-action bg-dark text-white <?php echo is_active('manage_admins.php'); ?>"><i class="bi bi-person-plus-fill me-2"></i>Admins & Roles</a>
        <a href="/smartprozen/admin/manage_modules.php" class="list-group-item list-group-item-action bg-dark text-white <?php echo is_active('manage_modules.php'); ?>"><i class="bi bi-box-fill me-2"></i>Modules</a>
        <a href="/smartprozen/admin/reports.php" class="list-group-item list-group-item-action bg-dark text-white <?php echo is_active('reports.php'); ?>"><i class="bi bi-file-bar-graph-fill me-2"></i>Reports</a>

        <div class="list-group-item bg-dark text-white"></div>
        <a href="/smartprozen/" target="_blank" class="list-group-item list-group-item-action bg-dark text-white"><i class="bi bi-box-arrow-up-right me-2"></i>View Site</a>
        <a href="/smartprozen/admin/logout.php" class="list-group-item list-group-item-action bg-dark text-white"><i class="bi bi-box-arrow-left me-2"></i>Logout</a>
    </div>
</div>
<div id="page-content-wrapper">
    <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
        <div class="container-fluid">
            <button class="btn btn-primary" id="menu-toggle"><i class="bi bi-list"></i></button>
        </div>
    </nav>
    <div class="container-fluid">
