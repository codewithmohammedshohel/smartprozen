<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/functions.php';

// Get theme settings
$settings = get_all_settings($conn);

// Theme customization
$theme_primary_color = $settings['theme_primary_color'] ?? '#007bff';
$theme_body_bg = $settings['theme_body_bg'] ?? '#ffffff';
$theme_text_color = $settings['theme_text_color'] ?? '#212529';
$theme_font_family = $settings['theme_font_family'] ?? 'Poppins';
$theme_button_radius = $settings['theme_button_radius'] ?? '4px';
$theme_card_radius = $settings['theme_card_radius'] ?? '8px';
$theme_shadow = $settings['theme_shadow'] ?? '0 0.125rem 0.25rem rgba(0, 0, 0, 0.075)';

// Google Fonts
$google_font = $settings['google_font'] ?? 'Poppins';

// SEO Variables
$page_title_for_translation = $page_title ?? $settings['site_name'] ?? 'SmartProZen';
$page_description_display = $page_description ?? $settings['site_description'] ?? 'Your ultimate online shopping destination.';

// Site information
$site_name = $settings['site_name'] ?? 'SmartProZen';
$business_name = $settings['business_name'] ?? 'SmartProZen';
$logo_filename = $settings['logo_filename'] ?? '';
$favicon_filename = $settings['favicon_filename'] ?? '';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <!-- Responsive Meta Tag -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo htmlspecialchars($page_title_for_translation); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($page_description_display); ?>">
    
    <!-- Favicon -->
    <?php if ($favicon_filename && file_exists(__DIR__ . '/../uploads/logos/' . $favicon_filename)): ?>
        <link rel="icon" type="image/x-icon" href="<?php echo SITE_URL; ?>/uploads/logos/<?php echo htmlspecialchars($favicon_filename); ?>">
    <?php endif; ?>

    <!-- Preconnect to external domains for performance -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://unpkg.com">

    <!-- CSS with preload for critical resources -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=<?php echo urlencode($google_font); ?>:wght@300;400;500;600;700&display=swap">

    <!-- Preload critical JS -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" as="script">

    <!-- Theme and Component CSS with media attribute for non-critical styles -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/enhanced.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/modern-components.css" media="print" onload="this.media='all'">

    <!-- Animation Libraries with defer loading -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" media="print" onload="this.media='all'">

    <!-- Dynamic Theme Styles -->
    <style>
        :root {
            --bs-primary: <?php echo $theme_primary_color; ?>;
            --bs-body-bg: <?php echo $theme_body_bg; ?>;
            --bs-body-color: <?php echo $theme_text_color; ?>;
            --bs-border-radius: <?php echo $theme_button_radius; ?>;
            --bs-border-radius-lg: <?php echo $theme_card_radius; ?>;
            --bs-box-shadow: <?php echo $theme_shadow; ?>;
        }
        
        body {
            font-family: '<?php echo $theme_font_family; ?>', sans-serif;
            background-color: <?php echo $theme_body_bg; ?>;
            color: <?php echo $theme_text_color; ?>;
        }
        
        .btn {
            border-radius: <?php echo $theme_button_radius; ?> !important;
        }
        
        .card {
            border-radius: <?php echo $theme_card_radius; ?> !important;
            box-shadow: <?php echo $theme_shadow; ?> !important;
        }
        
        .navbar-brand {
            font-family: '<?php echo $theme_font_family; ?>', sans-serif;
        }
        
        .btn-primary {
            background-color: <?php echo $theme_primary_color; ?>;
            border-color: <?php echo $theme_primary_color; ?>;
        }
        
        .btn-primary:hover {
            background-color: <?php echo $theme_primary_color; ?>;
            border-color: <?php echo $theme_primary_color; ?>;
            opacity: 0.9;
        }
        
        .text-primary {
            color: <?php echo $theme_primary_color; ?> !important;
        }
        
        .bg-primary {
            background-color: <?php echo $theme_primary_color; ?> !important;
        }
    </style>



</head>

<body class="d-flex flex-column min-vh-100">

    <header class="navbar navbar-expand-lg shadow-sm sticky-top bg-gradient">
        <div class="container py-3">
            <a class="navbar-brand d-flex align-items-center" href="<?php echo SITE_URL; ?>/">
                <?php if ($logo_filename && file_exists(__DIR__ . '/../uploads/logos/' . $logo_filename)): ?>
                    <img src="<?php echo SITE_URL; ?>/uploads/logos/<?php echo htmlspecialchars($logo_filename); ?>" 
                         alt="<?php echo htmlspecialchars($business_name); ?>" height="40" class="me-2">
                <?php else: ?>
                    <span class="fw-bold fs-4 me-2"><?php echo htmlspecialchars($business_name); ?></span>
                <?php endif; ?>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain"
                aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <?php echo generate_menu('main-menu', $conn); ?>

                <div class="d-flex align-items-center ms-lg-3 gap-2">
                    <?php if (is_user_logged_in()): ?>
                        <div class="dropdown">
                            <a class="btn btn-outline-primary dropdown-toggle" href="#" role="button" id="userDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-1"></i> Account
                            </a>
                                <ul class="dropdown-menu shadow-medium border-0" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/user/dashboard.php"><i
                                            class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/user/orders.php"><i
                                            class="bi bi-bag-check me-2"></i>Orders</a></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/user/profile.php"><i
                                            class="bi bi-person me-2"></i>Profile</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/auth/logout.php"><i
                                            class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo SITE_URL; ?>/auth/login.php" class="btn btn-outline-primary me-2">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Login
                        </a>
                        <a href="<?php echo SITE_URL; ?>/auth/register.php" class="btn btn-primary d-none d-lg-inline-flex">
                            Register
                        </a>
                    <?php endif; ?>

                    <a href="<?php echo SITE_URL; ?>/cart/" class="btn btn-outline-primary btn-cart position-relative me-2">
                        <i class="bi bi-cart3"></i>
                        <span
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count">
                            <span class="cart-item-count">0</span>
                        </span>
                    </a>


                </div>
            </div>
        </div>
    </header>
    <script>
        function changeLanguage(lang) {
            var iframe = document.getElementsByClassName('goog-te-menu-frame')[0];
            if (!iframe) return;
            var innerDoc = iframe.contentDocument || iframe.contentWindow.document;
            var langElements = innerDoc.getElementsByClassName('goog-te-menu2-item');
            for (var i = 0; i < langElements.length; i++) {
                if (langElements[i].getAttribute('value') == lang) {
                    langElements[i].click();
                }
            }
        }
    </script>
    <main class="flex-grow-1">
        <div class="container">
        </div>