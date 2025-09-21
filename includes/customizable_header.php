<?php
// Customizable Header Component
// This header adapts based on theme settings and menu configuration

// Get theme settings
$theme_settings = [];
$settings_result = $conn->query("SELECT setting_key, setting_value FROM settings WHERE category = 'theme'");
while ($row = $settings_result->fetch_assoc()) {
    $theme_settings[$row['setting_key']] = $row['setting_value'];
}

// Get header menu
$header_menu = null;
$menu_result = $conn->query("SELECT menu_items FROM menus WHERE location = 'header' AND is_active = 1 LIMIT 1");
if ($menu_result && $menu_result->num_rows > 0) {
    $header_menu = json_decode($menu_result->fetch_assoc()['menu_items'], true);
}

// Get site settings
$site_name = get_setting('site_name', 'SmartProZen', $conn);
$site_logo = get_setting('site_logo', '/uploads/logos/logo.png', $conn);

// Header layout
$header_layout = $theme_settings['header_layout'] ?? 'default';
$primary_color = $theme_settings['primary_color'] ?? '#007bff';

// Custom CSS
$custom_css = $theme_settings['custom_css'] ?? '';
$font_family = $theme_settings['font_family'] ?? 'system';

// Font mapping
$fonts = [
    'system' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
    'roboto' => '"Roboto", sans-serif',
    'opensans' => '"Open Sans", sans-serif',
    'lato' => '"Lato", sans-serif',
    'montserrat' => '"Montserrat", sans-serif',
    'poppins' => '"Poppins", sans-serif'
];

$selected_font = $fonts[$font_family] ?? $fonts['system'];
?>

<style>
:root {
    --primary-color: <?php echo $primary_color; ?>;
    --font-family: <?php echo $selected_font; ?>;
}

body {
    font-family: var(--font-family);
}

<?php echo $custom_css; ?>

/* Header Layout Styles */
.header-default {
    background: #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.header-centered {
    background: #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
}

.header-minimal {
    background: transparent;
    border-bottom: 1px solid #eee;
}

.header-<?php echo $header_layout; ?> .navbar-brand {
    color: var(--primary-color) !important;
    font-weight: bold;
}

.header-<?php echo $header_layout; ?> .nav-link {
    color: #333 !important;
    transition: color 0.3s ease;
}

.header-<?php echo $header_layout; ?> .nav-link:hover {
    color: var(--primary-color) !important;
}

.header-<?php echo $header_layout; ?> .btn-primary {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.header-<?php echo $header_layout; ?> .btn-primary:hover {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    opacity: 0.9;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .header-<?php echo $header_layout; ?> .navbar-collapse {
        background: #fff;
        margin-top: 10px;
        padding: 15px;
        border-radius: 5px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
}
</style>

<header class="header-<?php echo $header_layout; ?> sticky-top">
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <?php if ($header_layout === 'centered'): ?>
                <!-- Centered Layout -->
                <div class="navbar-brand mx-auto d-flex align-items-center">
                    <img src="<?php echo SITE_URL . $site_logo; ?>" alt="<?php echo htmlspecialchars($site_name); ?>" height="40" class="me-2">
                    <span class="fs-4 fw-bold"><?php echo htmlspecialchars($site_name); ?></span>
                </div>
            <?php else: ?>
                <!-- Default and Minimal Layout -->
                <a class="navbar-brand d-flex align-items-center" href="<?php echo SITE_URL; ?>">
                    <img src="<?php echo SITE_URL . $site_logo; ?>" alt="<?php echo htmlspecialchars($site_name); ?>" height="40" class="me-2">
                    <?php if ($header_layout !== 'minimal'): ?>
                        <span class="fs-4 fw-bold"><?php echo htmlspecialchars($site_name); ?></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <?php if ($header_layout === 'centered'): ?>
                    <!-- Centered menu -->
                    <ul class="navbar-nav mx-auto">
                        <?php if ($header_menu && is_array($header_menu)): ?>
                            <?php foreach ($header_menu as $item): ?>
                                <li class="nav-item dropdown">
                                    <?php 
                                    $url = $item['url'];
                                    // Fix relative URLs to absolute URLs
                                    if (strpos($url, 'http') !== 0) {
                                        if (strpos($url, '/') === 0) {
                                            $url = SITE_URL . $url;
                                        } else {
                                            $url = SITE_URL . '/' . $url;
                                        }
                                    }
                                    ?>
                                    <a class="nav-link" href="<?php echo htmlspecialchars($url); ?>" 
                                       <?php echo isset($item['children']) ? 'data-bs-toggle="dropdown"' : ''; ?>>
                                        <?php echo htmlspecialchars($item['title']); ?>
                                    </a>
                                    <?php if (isset($item['children'])): ?>
                                        <ul class="dropdown-menu">
                                            <?php foreach ($item['children'] as $child): ?>
                                                <?php 
                                                $child_url = $child['url'];
                                                // Fix relative URLs to absolute URLs
                                                if (strpos($child_url, 'http') !== 0) {
                                                    if (strpos($child_url, '/') === 0) {
                                                        $child_url = SITE_URL . $child_url;
                                                    } else {
                                                        $child_url = SITE_URL . '/' . $child_url;
                                                    }
                                                }
                                                ?>
                                                <li><a class="dropdown-item" href="<?php echo htmlspecialchars($child_url); ?>">
                                                    <?php echo htmlspecialchars($child['title']); ?>
                                                </a></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                <?php else: ?>
                    <!-- Default and Minimal menu -->
                    <ul class="navbar-nav me-auto">
                        <?php if ($header_menu && is_array($header_menu)): ?>
                            <?php foreach ($header_menu as $item): ?>
                                <li class="nav-item dropdown">
                                    <?php 
                                    $url = $item['url'];
                                    // Fix relative URLs to absolute URLs
                                    if (strpos($url, 'http') !== 0) {
                                        if (strpos($url, '/') === 0) {
                                            $url = SITE_URL . $url;
                                        } else {
                                            $url = SITE_URL . '/' . $url;
                                        }
                                    }
                                    ?>
                                    <a class="nav-link" href="<?php echo htmlspecialchars($url); ?>" 
                                       <?php echo isset($item['children']) ? 'data-bs-toggle="dropdown"' : ''; ?>>
                                        <?php echo htmlspecialchars($item['title']); ?>
                                    </a>
                                    <?php if (isset($item['children'])): ?>
                                        <ul class="dropdown-menu">
                                            <?php foreach ($item['children'] as $child): ?>
                                                <?php 
                                                $child_url = $child['url'];
                                                // Fix relative URLs to absolute URLs
                                                if (strpos($child_url, 'http') !== 0) {
                                                    if (strpos($child_url, '/') === 0) {
                                                        $child_url = SITE_URL . $child_url;
                                                    } else {
                                                        $child_url = SITE_URL . '/' . $child_url;
                                                    }
                                                }
                                                ?>
                                                <li><a class="dropdown-item" href="<?php echo htmlspecialchars($child_url); ?>">
                                                    <?php echo htmlspecialchars($child['title']); ?>
                                                </a></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                <?php endif; ?>

                <!-- Right side actions -->
                <div class="d-flex align-items-center">
                    <!-- Search -->
                    <div class="me-3">
                        <form class="d-flex" action="<?php echo SITE_URL; ?>/search.php" method="GET">
                            <input class="form-control form-control-sm" type="search" name="q" placeholder="Search..." style="width: 200px;">
                            <button class="btn btn-outline-secondary btn-sm ms-1" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </form>
                    </div>

                    <!-- User Account -->
                    <?php if (is_user_logged_in()): ?>
                        <div class="dropdown me-2">
                            <a class="btn btn-outline-secondary dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-person"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/user/dashboard.php">
                                    <i class="bi bi-speedometer2"></i> Dashboard
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/user/orders.php">
                                    <i class="bi bi-box-seam"></i> Orders
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/user/profile.php">
                                    <i class="bi bi-person-gear"></i> Profile
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/auth/logout.php">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <div class="me-2">
                            <a href="<?php echo SITE_URL; ?>/auth/login.php" class="btn btn-outline-secondary">
                                <i class="bi bi-person"></i> Login
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- Cart -->
                    <div class="dropdown">
                        <a class="btn btn-primary position-relative" href="<?php echo SITE_URL; ?>/cart/" data-bs-toggle="dropdown">
                            <i class="bi bi-cart"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count" id="cart-count">
                                <?php 
                                $cart_count = 0;
                                if (function_exists('get_cart_count')) {
                                    try {
                                        $cart_count = get_cart_count();
                                    } catch (Exception $e) {
                                        $cart_count = 0;
                                    }
                                }
                                echo $cart_count;
                                ?>
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" style="width: 300px;">
                            <div id="cart-dropdown-content">
                                <!-- Cart items will be loaded here via AJAX -->
                                <div class="p-3 text-center">
                                    <div class="spinner-border spinner-border-sm" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>

<script>
// Load cart dropdown content
document.addEventListener('DOMContentLoaded', function() {
    loadCartDropdown();
});

function loadCartDropdown() {
    fetch('<?php echo SITE_URL; ?>/cart/get_cart_quantities.php')
        .then(response => response.json())
        .then(data => {
            const cartContent = document.getElementById('cart-dropdown-content');
            if (data.items && data.items.length > 0) {
                let html = '<div class="p-2">';
                let total = 0;
                
                data.items.forEach(item => {
                    html += `
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <div class="fw-bold">${item.name}</div>
                                <small class="text-muted">Qty: ${item.quantity}</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">$${parseFloat(item.price * item.quantity).toFixed(2)}</div>
                            </div>
                        </div>
                    `;
                    total += item.price * item.quantity;
                });
                
                html += `
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Total:</strong>
                        <strong>$${total.toFixed(2)}</strong>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="<?php echo SITE_URL; ?>/cart/" class="btn btn-primary btn-sm">View Cart</a>
                        <a href="<?php echo SITE_URL; ?>/cart/checkout.php" class="btn btn-success btn-sm">Checkout</a>
                    </div>
                </div>
                `;
                
                cartContent.innerHTML = html;
            } else {
                cartContent.innerHTML = `
                    <div class="p-3 text-center">
                        <i class="bi bi-cart-x fs-1 text-muted"></i>
                        <p class="text-muted mt-2">Your cart is empty</p>
                        <a href="<?php echo SITE_URL; ?>/products_list.php" class="btn btn-primary btn-sm">Shop Now</a>
                    </div>
                `;
            }
            
            // Update cart count
            document.getElementById('cart-count').textContent = data.total_items || 0;
        })
        .catch(error => {
            console.error('Error loading cart:', error);
        });
}
</script>
