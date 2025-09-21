<?php
// core/functions.php

// ... existing functions ...

/**
 * Resizes and saves an uploaded image, creating a full-size version and a thumbnail.
 *
 * @param array $file The uploaded file array from $_FILES.
 * @param string $target_dir The directory to save the images in.
 * @param int $full_width The width for the full-size image.
 * @param int $thumb_width The width for the thumbnail image.
 * @return string|false The new filename on success, false on failure.
 */
function resize_and_save_image($file, $target_dir, $full_width = 800, $thumb_width = 150) {
    $filename = uniqid() . '-' . basename($file["name"]);
    $target_file = $target_dir . $filename;
    $thumb_file = $target_dir . 'thumb-' . $filename;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    if (getimagesize($file["tmp_name"]) === false) {
        return false;
    }

    // Create image resource
    $source_image = null;
    if ($imageFileType == "jpg" || $imageFileType == "jpeg") {
        $source_image = imagecreatefromjpeg($file["tmp_name"]);
    } elseif ($imageFileType == "png") {
        $source_image = imagecreatefrompng($file["tmp_name"]);
    } elseif ($imageFileType == "gif") {
        $source_image = imagecreatefromgif($file["tmp_name"]);
    }

    if (!$source_image) {
        return false;
    }

    $width = imagesx($source_image);
    $height = imagesy($source_image);

    // --- Create and save full-size image ---
    $full_height = floor($height * ($full_width / $width));
    $virtual_image = imagecreatetruecolor($full_width, $full_height);
    imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $full_width, $full_height, $width, $height);
    imagejpeg($virtual_image, $target_file, 85); // Save with 85% quality

    // --- Create and save thumbnail ---
    $thumb_height = floor($height * ($thumb_width / $width));
    $virtual_thumb = imagecreatetruecolor($thumb_width, $thumb_height);
    imagecopyresampled($virtual_thumb, $source_image, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height);
    imagejpeg($virtual_thumb, $thumb_file, 80); // Save with 80% quality

    imagedestroy($source_image);
    imagedestroy($virtual_image);
    imagedestroy($virtual_thumb);

    return $filename;
}

/**
 * Clears the PWA cache by updating the service worker cache version.
 */
function clear_pwa_cache() {
    $sw_file = __DIR__ . '/../sw.js';
    if (file_exists($sw_file)) {
        $content = file_get_contents($sw_file);
        $content = preg_replace_callback(
            "/const CACHE_NAME = 'smartprozen-cache-v(\d+)';/",
            function($matches) {
                $version = (int)$matches[1] + 1;
                return "const CACHE_NAME = 'smartprozen-cache-v{$version}';";
            },
            $content
        );
        file_put_contents($sw_file, $content);
        return true;
    }
    return false;
}

// ... rest of the file ...

// --- Language & Translation (Feature Disabled) ---
function __($key) {
    return ucwords(str_replace('_', ' ', $key));
}

// This function is kept for theme compatibility after disabling the language feature.
function get_translated_text($data, $key = '') {
    if (is_array($data)) {
        return $data[$key] ?? null;
    }
    if (is_string($data)) {
        // Check if the string is JSON
        $decoded = json_decode($data, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            // If JSON, return the 'en' key if it exists, or the first element, or the original string
            return $decoded['en'] ?? reset($decoded) ?? $data;
        }
        return $data; // It's a plain string
    }
    return null;
}

// --- Settings ---
function get_all_settings($conn) {
    static $settings = null;
    if ($settings === null) {
        $settings = [];
        $result = $conn->query("SELECT * FROM settings");
        while ($row = $result->fetch_assoc()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    return $settings;
}
function get_setting($key, $conn) {
    $settings = get_all_settings($conn);
    return $settings[$key] ?? null;
}

// --- Authentication & Permissions (RBAC) ---
$GLOBALS['admin_permissions'] = [];

function is_admin_logged_in() {
    return isset($_SESSION['admin_id']);
}

function is_user_logged_in() {
    return isset($_SESSION['user_id']);
}

function get_logged_in_user() {
    global $conn;
    if (!is_user_logged_in()) return null;
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    return $user;
}

function get_current_admin() {
    global $conn;
    if (!is_admin_logged_in()) return null;
    
    $stmt = $conn->prepare("SELECT * FROM admin_users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['admin_id']);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    return $admin;
}

function require_login() {
    if (!is_user_logged_in()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: /smartprozen/auth/login.php');
        exit;
    }
}

function require_admin_login() {
    if (!is_admin_logged_in()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: /smartprozen/admin/login.php');
        exit;
    }
}

function logout_user() {
    // Unset user session variables
    unset($_SESSION['user_id']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_email']);
    
    // Clear cart if exists
    unset($_SESSION['cart']);
    
    // Regenerate session ID for security
    session_regenerate_id(true);
}

function logout_admin() {
    // Unset admin session variables
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_username']);
    unset($_SESSION['admin_role_id']);
    
    // Clear admin permissions cache
    $GLOBALS['admin_permissions'] = [];
    
    // Regenerate session ID for security
    session_regenerate_id(true);
}

function logout_all() {
    // Unset all session variables
    $_SESSION = [];
    
    // Destroy session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy the session
    session_destroy();
}
function has_permission($permission_key) {
    global $conn;
    if (!is_admin_logged_in()) return false;
    
    if (empty($GLOBALS['admin_permissions'])) {
        $role_id = $_SESSION['admin_role_id'];
        if ($role_id == 1) { // Super Admin bypass
            return true;
        }
        $stmt = $conn->prepare("SELECT permissions FROM roles WHERE id = ?");
        $stmt->bind_param("i", $role_id);
        $stmt->execute();
        $role = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $GLOBALS['admin_permissions'] = json_decode($role['permissions'], true) ?? [];
    }
    
    return isset($GLOBALS['admin_permissions'][$permission_key]) && $GLOBALS['admin_permissions'][$permission_key] == 'true';
}

// --- Modules ---
function is_module_enabled($slug, $conn) {
    static $modules = null;
    if ($modules === null) {
        $modules = [];
        $result = $conn->query("SELECT slug, is_active FROM modules");
        while ($row = $result->fetch_assoc()) {
            $modules[$row['slug']] = (bool)$row['is_active'];
        }
    }
    return $modules[$slug] ?? false;
}

// --- Cart Functions ---
function get_cart_count() {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $total_items = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_items += $item['quantity'];
    }
    return $total_items;
}

function get_cart_total() {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $price = $item['sale_price'] ?? $item['price'];
        $total += $price * $item['quantity'];
    }
    return $total;
}

function add_to_cart($product_id, $quantity = 1) {
    global $conn;
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Get product details
    $stmt = $conn->prepare("SELECT id, name, price, sale_price, stock_quantity, stock_status FROM products WHERE id = ? AND is_published = 1");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if (!$product) {
        return false;
    }
    
    // Check stock
    if ($product['stock_status'] === 'outofstock') {
        return false;
    }
    
    if ($product['stock_quantity'] < $quantity) {
        return false;
    }
    
    $cart_key = $product_id;
    
    if (isset($_SESSION['cart'][$cart_key])) {
        $_SESSION['cart'][$cart_key]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$cart_key] = [
            'product_id' => $product_id,
            'name' => $product['name'],
            'price' => $product['price'],
            'sale_price' => $product['sale_price'],
            'quantity' => $quantity
        ];
    }
    
    return true;
}

function remove_from_cart($product_id) {
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        return true;
    }
    return false;
}

function update_cart_quantity($product_id, $quantity) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if ($quantity <= 0) {
        return remove_from_cart($product_id);
    }
    
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        return true;
    }
    
    return false;
}

function clear_cart() {
    $_SESSION['cart'] = [];
    return true;
}

// --- User Functions ---
// Note: get_logged_in_user() function already exists above

// --- Helper Functions ---
function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    
    return $text;
}

function format_price($price, $currency = '$') {
    return $currency . number_format($price, 2);
}

function time_ago($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'just now';
    if ($time < 3600) return floor($time/60) . ' minutes ago';
    if ($time < 86400) return floor($time/3600) . ' hours ago';
    if ($time < 2592000) return floor($time/86400) . ' days ago';
    if ($time < 31536000) return floor($time/2592000) . ' months ago';
    
    return floor($time/31536000) . ' years ago';
}

// --- Logging ---
function log_activity($user_type, $user_id, $action, $details) {
    global $conn;
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_type, user_id, action, details, ip_address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sisss", $user_type, $user_id, $action, $details, $ip_address);
    $stmt->execute();
    $stmt->close();
}

// --- Utilities ---
// Note: slugify() function already exists above

/**
 * Checks if a string is a valid JSON.
 *
 * @param string $string The string to check.
 * @return bool True if the string is valid JSON, false otherwise.
 */
function is_json($string) {
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}
function show_flash_messages() {
    if (isset($_SESSION['success_message'])) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . htmlspecialchars($_SESSION['success_message']) . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        unset($_SESSION['success_message']);
    }
    if (isset($_SESSION['error_message'])) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . htmlspecialchars($_SESSION['error_message']) . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        unset($_SESSION['error_message']);
    }
}

// --- User-related functions ---
function is_logged_in() {
    return isset($_SESSION['user_id']);
}
function get_user_by_id($id, $conn) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// --- Menu ---
function generate_menu($menu_name, $conn) {
    try {
        $stmt = $conn->prepare("SELECT items FROM menus WHERE name = ?");
        $stmt->bind_param("s", $menu_name);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $menu_items = json_decode($row['items'], true);
            if (is_array($menu_items)) {
                $html = '<ul class="navbar-nav me-auto mb-2 mb-lg-0">';
                foreach ($menu_items as $item) {
                    $label = htmlspecialchars($item['label']);
                    $url = $item['url'];
                    
                    // Fix relative URLs to absolute URLs
                    if (strpos($url, 'http') !== 0) {
                        if (strpos($url, '/') === 0) {
                            // URL starts with /, make it relative to SITE_URL
                            $url = SITE_URL . $url;
                        } else {
                            // Relative URL, make it relative to SITE_URL
                            $url = SITE_URL . '/' . $url;
                        }
                    }
                    
                    $url = htmlspecialchars($url);
                    $current_page = basename($_SERVER['PHP_SELF']);
                    $current_uri = $_SERVER['REQUEST_URI'];
                    
                    // Determine active class
                    $active_class = '';
                    
                    // Check for exact match with current URI
                    if ($current_uri === parse_url($url, PHP_URL_PATH)) {
                        $active_class = 'active';
                    }
                    // Check for home page
                    elseif (($current_page === 'index.php' || $current_page === '') && 
                            (strpos($url, SITE_URL . '/') === 0 && parse_url($url, PHP_URL_PATH) === '/')) {
                        $active_class = 'active';
                    }
                    // Check for specific pages
                    elseif ($current_page === 'products_list.php' && strpos($url, 'products_list.php') !== false) {
                        $active_class = 'active';
                    }
                    elseif ($current_page === 'contact.php' && strpos($url, 'contact') !== false) {
                        $active_class = 'active';
                    }

                    $html .= '<li class="nav-item">';
                    $html .= '<a class="nav-link ' . $active_class . '" href="' . $url . '">' . $label . '</a>';
                    $html .= '</li>';
                }
                $html .= '</ul>';
                return $html;
            }
        }
    } catch (mysqli_sql_exception $e) {
        // If the table doesn't exist, or another SQL error occurs, return a default menu
        return generate_default_menu();
    }
    return generate_default_menu(); // Return default menu if no menu found
}

// Default menu fallback
function generate_default_menu() {
    return '<ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
            <a class="nav-link" href="' . SITE_URL . '/">' . __('home') . '</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="' . SITE_URL . '/products_list.php">' . __('all_products') . '</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="' . SITE_URL . '/contact.php">' . __('contact') . '</a>
        </li>
    </ul>';
}
?>