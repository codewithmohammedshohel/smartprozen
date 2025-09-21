<?php
// This should be included after config.php and db.php is available as $conn

// --- Language & Translation ---
$GLOBALS['lang'] = [];
function load_language($lang_code) { // Removed default value
    $lang_file = __DIR__ . "/../lang/{$lang_code}.json";
    if (file_exists($lang_file)) {
        $GLOBALS['lang'] = json_decode(file_get_contents($lang_file), true);
    }
}
function __($key) {
    // Ensure language is loaded only once per request
    if (empty($GLOBALS['lang']) && isset($_SESSION['lang'])) {
        load_language($_SESSION['lang']);
    } elseif (empty($GLOBALS['lang'])) {
        load_language(DEFAULT_LANG); // Fallback to default from config
    }
    return $GLOBALS['lang'][$key] ?? ucwords(str_replace('_', ' ', $key));
}
function get_translated_text($json_string, $key_prefix = '') {
    if (empty($json_string)) return '';
    
    // Handle if it's already an array
    if (is_array($json_string)) {
        $data = $json_string;
    } else {
        // Try to decode JSON
        $data = json_decode($json_string, true);
        if (!is_array($data)) {
            // If it's not JSON and not an array, return as string
            return is_string($json_string) ? $json_string : '';
        }
    }
    
    $lang = $_SESSION['lang'] ?? DEFAULT_LANG; // Use DEFAULT_LANG from config
    
    // For simple {en: 'text', bn: 'text'} structure
    if (isset($data[$lang]) && is_string($data[$lang])) return $data[$lang];
    if (isset($data['en']) && is_string($data['en'])) return $data['en']; // English fallback

    // For complex {title_en: 'text', title_bn: 'text'} structure
    $key_lang = $key_prefix . '_' . $lang;
    $key_en = $key_prefix . '_en'; 
    if (isset($data[$key_lang]) && !empty($data[$key_lang]) && is_string($data[$key_lang])) return $data[$key_lang];
    if (isset($data[$key_en]) && !empty($data[$key_en]) && is_string($data[$key_en])) return $data[$key_en];
    
    // If we have any string value in the array, return the first one
    foreach ($data as $value) {
        if (is_string($value) && !empty($value)) {
            return $value;
        }
    }
    
    return '...';
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
function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'n-a' : $text;
}
function show_flash_messages() {
    if (isset($_SESSION['success_message'])) {
        echo '<div class="flash-message success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
        unset($_SESSION['success_message']);
    }
    if (isset($_SESSION['error_message'])) {
        echo '<div class="flash-message error">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
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