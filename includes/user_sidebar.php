<?php
$current_page = basename($_SERVER['PHP_SELF']);
$nav_items = [
    'dashboard.php' => ['icon' => 'bi-grid-fill', 'label' => __('dashboard')],
    'orders.php' => ['icon' => 'bi-box-seam-fill', 'label' => __('orders')],
    'downloads.php' => ['icon' => 'bi-download', 'label' => __('downloads')],
    'profile.php' => ['icon' => 'bi-person-fill', 'label' => __('profile')],
    'wishlist.php' => ['icon' => 'bi-heart-fill', 'label' => __('wishlist')],
];
?>
<div class="col-md-3">
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-light rounded shadow-sm">
        <ul class="nav nav-pills flex-column mb-auto">
            <?php foreach ($nav_items as $file => $item): ?>
                <li class="nav-item">
                    <a href="<?php echo SITE_URL; ?>/user/<?php echo $file; ?>" class="nav-link <?php echo ($current_page === $file) ? 'active' : 'text-dark'; ?>">
                        <i class="bi <?php echo $item['icon']; ?> me-2"></i>
                        <?php echo $item['label']; ?>
                    </a>
                </li>
            <?php endforeach; ?>
            <hr>
            <li class="nav-item">
                <a href="<?php echo SITE_URL; ?>/auth/logout.php" class="nav-link text-dark">
                    <i class="bi bi-box-arrow-left me-2"></i>
                    <?php echo __('logout'); ?>
                </a>
            </li>
        </ul>
    </div>
</div>
