<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/functions.php';

// Language setting - Hardcoded to English
$lang_code = 'en';
load_language($lang_code);

// Theme/Skin settings
$theme_class = get_setting('theme_skin', $conn) ?? 'default'; // e.g., 'default', 'dark', 'corporate'
$theme_stylesheet = "/smartprozen/css/{$theme_class}.css";

// Google Fonts
$google_font = get_setting('google_font', $conn);

// Custom CSS
$custom_css = get_setting('custom_css', $conn);

// SEO Variables
$page_title = $page_title ?? get_translated_text(get_setting('business_name', $conn), 'business_name') ?? 'SmartProZen';
$page_description = $page_description ?? get_translated_text(get_setting('site_description', $conn), 'site_description') ?? 'Your ultimate online shopping destination.';

?>
<!DOCTYPE html>
<html lang="<?php echo $lang_code; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
        <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Enhanced CSS -->
    <link rel="stylesheet" href="/smartprozen/css/enhanced.css">

    <!-- Google Font -->
    <?php if ($google_font): ?>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=<?php echo urlencode($google_font); ?>:wght@400;700&display=swap">
    <style>
        body, h1, h2, h3, h4, h5, h6, p, a, .btn {
            font-family: '<?php echo $google_font; ?>', sans-serif;
        }
    </style>
    <?php endif; ?>

    <!-- Custom CSS -->
    <?php if ($custom_css): ?>
    <style>
        <?php echo $custom_css; ?>
    </style>
    <?php endif; ?>

    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
<header class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="/smartprozen/">
            <?php
            $logo_path = get_setting('logo_path', $conn);
            $business_name_setting = get_setting('business_name', $conn);
            $business_name = get_translated_text($business_name_setting, 'business_name') ?? 'SmartProZen';

            // Ensure business_name is a string
            if (is_array($business_name)) {
                $business_name = 'SmartProZen';
            }

            if ($logo_path) {
                echo '<img src="' . SITE_URL . '/' . htmlspecialchars($logo_path) . '" alt="' . htmlspecialchars($business_name) . '" style="max-height: 40px;">';
            } else {
                echo '<h1>' . htmlspecialchars($business_name) . '</h1>';
            }
            ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-nav" aria-controls="main-nav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="main-nav">
            <?php
            if (function_exists('generate_menu')) {
                echo generate_menu('main-menu', $conn);
            }
            ?>
            <form class="d-flex ms-auto me-2" action="/smartprozen/index.php" method="GET">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="search">
                <button class="btn btn-outline-success" type="submit"><i class="bi bi-search"></i></button>
            </form>
            <div class="d-flex align-items-center">
                <a href="/smartprozen/cart/" class="btn btn-outline-primary position-relative me-2">
                    <?php echo __('cart'); ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>
                    </span>
                </a>
                <?php if (is_logged_in()): ?>
                    <div class="dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><?php echo __('my_account'); ?></a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/smartprozen/user/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                            <li><a class="dropdown-item" href="/smartprozen/user/orders.php"><?php echo __('my_orders'); ?></a></li>
                            <li><a class="dropdown-item" href="/smartprozen/user/profile.php"><?php echo __('profile'); ?></a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/smartprozen/auth/logout.php"><?php echo __('logout'); ?></a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="/smartprozen/auth/login.php" class="btn btn-primary"><?php echo __('login'); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
<main class="flex-grow-1">
    <div class="container">