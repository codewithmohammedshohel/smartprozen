<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/functions.php';

// Language setting
$lang_code = $_SESSION['lang'] ?? DEFAULT_LANG; // Default to English

// Ensure $lang_code is a string, not an array
if (is_array($lang_code)) {
    $lang_code = DEFAULT_LANG;
}

// Additional safety check - ensure it's a valid language code
if (!in_array($lang_code, ['en', 'bn'])) {
    $lang_code = DEFAULT_LANG;
}

// Check if language is set in URL and update session
if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'bn'])) {
    $lang_code = $_GET['lang'];
    $_SESSION['lang'] = $lang_code;
}

load_language($lang_code);

// Theme/Skin settings
$theme_class = get_setting('theme_skin', $conn) ?? 'default'; // e.g., 'default', 'dark', 'corporate'
$theme_stylesheet = "/smartprozen/css/{$theme_class}.css";

// Google Fonts
$google_font = get_setting('google_font', $conn);

// Custom CSS
$custom_css = get_setting('custom_css', $conn);

// SEO Variables
// Ensure $page_title is always a string or a JSON string for translation
$page_title_for_translation = '';
if (isset($page_title)) {
    if (is_array($page_title)) {
        $page_title_for_translation = json_encode($page_title);
    } elseif (is_string($page_title)) {
        $page_title_for_translation = $page_title;
    }
}

// If $page_title_for_translation is still empty, try to get business_name from settings
if (empty($page_title_for_translation)) {
    $page_title_for_translation = get_setting('business_name', $conn);
}

$final_page_title_display = get_translated_text($page_title_for_translation, 'business_name') ?? 'SmartProZen';
$site_description_setting = get_setting('site_description', $conn);
$page_description_display = get_translated_text($site_description_setting, 'site_description') ?? 'Your ultimate online shopping destination.';

// Ensure we have strings, not arrays
if (is_array($final_page_title_display)) {
    $final_page_title_display = 'SmartProZen';
}
if (is_array($page_description_display)) {
    $page_description_display = 'Your ultimate online shopping destination.';
}

?>
<!DOCTYPE html>
<html lang="<?php echo $lang_code; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($final_page_title_display); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($page_description_display); ?>">
    
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
<header class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="/smartprozen/">
            <?php
            $business_logo_filename = get_setting('business_logo_filename', $conn);
            $business_name_setting = get_setting('business_name', $conn);
            $business_name = get_translated_text($business_name_setting, 'business_name') ?? 'SmartProZen';
            
            // Ensure business_name is a string
            if (is_array($business_name)) {
                $business_name = 'SmartProZen';
            }
            if ($business_logo_filename) {
                echo '<img src="' . SITE_URL . '/uploads/media/' . htmlspecialchars($business_logo_filename) . '" alt="' . htmlspecialchars($business_name) . '" style="max-height: 40px;">';
            } else {
                echo '<span>' . htmlspecialchars($business_name) . '</span>'; // Use span for non-h1 branding
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
            <form class="d-flex ms-auto me-2" action="/smartprozen/products_list.php" method="GET">
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
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-1"></i>
                                <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="/smartprozen/user/dashboard.php">
                                    <i class="bi bi-speedometer2 me-2"></i><?php echo __('dashboard'); ?>
                                </a></li>
                                <li><a class="dropdown-item" href="/smartprozen/user/profile.php">
                                    <i class="bi bi-person me-2"></i><?php echo __('profile'); ?>
                                </a></li>
                                <li><a class="dropdown-item" href="/smartprozen/user/orders.php">
                                    <i class="bi bi-bag me-2"></i><?php echo __('my_orders'); ?>
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="/smartprozen/auth/logout.php">
                                    <i class="bi bi-box-arrow-right me-2"></i><?php echo __('logout'); ?>
                                </a></li>
                            </ul>
                        </div>
                    <?php elseif (isset($_SESSION['admin_id'])): ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-light dropdown-toggle" type="button" id="adminDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-shield-check me-1"></i>
                                <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">
                                <li><a class="dropdown-item" href="/smartprozen/admin/dashboard.php">
                                    <i class="bi bi-speedometer2 me-2"></i>Admin Dashboard
                                </a></li>
                                <li><a class="dropdown-item" href="/smartprozen/admin/settings.php">
                                    <i class="bi bi-gear me-2"></i>Settings
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="/smartprozen/admin/logout.php">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="/smartprozen/auth/login.php" class="btn btn-primary me-2"><?php echo __('login'); ?></a>
                        <a href="/smartprozen/auth/register.php" class="btn btn-outline-light"><?php echo __('register'); ?></a>
                    <?php endif; ?>
                <div class="lang-switcher ms-3">
                    <a href="?lang=en" class="text-decoration-none <?php echo $lang_code === 'en' ? 'fw-bold' : ''; ?>">EN</a> | 
                    <a href="?lang=bn" class="text-decoration-none <?php echo $lang_code === 'bn' ? 'fw-bold' : ''; ?>">BN</a>
                </div>
            </div>
        </div>
    </div>
</header>
<main class="flex-grow-1">
    <div class="container">