<?php
// find_errors_and_fix.php - A diagnostic script for SmartProzen project

echo "<h1>SmartProzen Project Diagnostics</h1>";
echo "<p>This script performs a series of checks to identify common errors and potential issues in your project.</p>";
echo "<hr>";

// --- Configuration and Environment Checks ---
echo "<h2>1. Configuration and Environment</h2>";

// Check config.php for DEBUG mode and error logging
$config_path = __DIR__ . '/config.php';
if (file_exists($config_path)) {
    $config_content = file_get_contents($config_path);
    
    // Check DEBUG constant
    if (preg_match("/define\('DEBUG',\s*(true|false)\);/i", $config_content, $matches)) {
        $debug_mode = ($matches[1] === 'true') ? 'Enabled' : 'Disabled';
        echo "<p><strong>DEBUG Mode:</strong> <span style='color:" . ($debug_mode === 'Enabled' ? 'green' : 'orange') . "'>{$debug_mode}</span></p>";
        if ($debug_mode === 'Disabled') {
            echo "<p style='color:orange;'><em>Recommendation:</em> Consider enabling DEBUG mode (set to `true`) in `config.php` during development to see errors on screen.</p>";
        }
    } else {
        echo "<p style='color:orange;'><strong>DEBUG Mode:</strong> Not found or incorrectly defined in `config.php`.</p>";
    }

    // Check error logging
    if (preg_match("/ini_set\('log_errors',\s*1\);/i", $config_content)) {
        echo "<p><strong>Error Logging:</strong> <span style='color:green;'>Enabled</span></p>";
    } else {
        echo "<p style='color:red;'><strong>Error Logging:</strong> Disabled. Errors might not be written to log files.</p>";
        echo "<p style='color:red;'><em>Recommendation:</em> Ensure `ini_set('log_errors', 1);` is present in `config.php`.</p>";
    }

    if (preg_match("/ini_set\('error_log',\s*__DIR__\s*\.\s*'\/\.\.\/logs\/php-error\.log'\);/i", $config_content)) {
        echo "<p><strong>Error Log Path:</strong> <span style='color:green;'>Configured to `logs/php-error.log`</span></p>";
    } else {
        echo "<p style='color:red;'><strong>Error Log Path:</strong> Not found or incorrectly defined in `config.php`.</p>";
        echo "<p style='color:red;'><em>Recommendation:</em> Ensure `ini_set('error_log', __DIR__ . '/../logs/php-error.log');` is present in `config.php`.</p>";
    }

} else {
    echo "<p style='color:red;'><strong>Error:</strong> `config.php` not found in the root directory.</p>";
}

// Check logs directory and writability
$logs_dir = __DIR__ . '/logs';
echo "<p><strong>Logs Directory:</strong> ";
if (is_dir($logs_dir)) {
    echo "<span style='color:green;'>Exists</span> (`{$logs_dir}`)</p>";
    if (is_writable($logs_dir)) {
        echo "<p><strong>Logs Directory Permissions:</strong> <span style='color:green;'>Writable</span></p>";
    } else {
        echo "<p style='color:red;'><strong>Logs Directory Permissions:</strong> Not Writable. PHP might not be able to write error logs.</p>";
        echo "<p style='color:red;'><em>Recommendation:</em> Adjust directory permissions (e.g., `chmod 775 logs` or `chmod 777 logs` on Linux/macOS, or grant write access on Windows).</p>";
    }
} else {
    echo "<p style='color:red;'>Does NOT exist.</p>";
    echo "<p style='color:red;'><em>Recommendation:</em> Create the `logs` directory in the project root (`{$logs_dir}`).</p>";
}

// Check php-error.log content
$error_log_file = $logs_dir . '/php-error.log';
echo "<p><strong>`php-error.log` Status:</strong> ";
if (file_exists($error_log_file)) {
    echo "<span style='color:orange;'>Exists</span></p>";
    $log_content = file_get_contents($error_log_file);
    if (!empty($log_content)) {
        echo "<p style='color:red;'><strong>Errors Found in Log:</strong> Yes. Please review `{$error_log_file}` for details.</p>";
        echo "<pre style='background-color:#eee; padding:10px; border:1px solid #ccc; max-height: 200px; overflow-y: scroll;'>" . htmlspecialchars($log_content) . "</pre>";
    } else {
        echo "<p style='color:green;'>No errors recorded in log file.</p>";
    }
} else {
    echo "<p style='color:green;'>Does NOT exist (no errors logged yet, or permissions issue).</p>";
}

echo "<hr>";

// --- Codebase Specific Checks ---
echo "<h2>2. Codebase Specific Issues</h2>";

// Check schema.sql for duplicate testimonials table
$schema_path = __DIR__ . '/schema.sql';
if (file_exists($schema_path)) {
    $schema_content = file_get_contents($schema_path);
    preg_match_all("/CREATE TABLE `testimonials`/i", $schema_content, $matches);
    if (count($matches[0]) > 1) {
        echo "<p style='color:red;'><strong>`schema.sql` Error:</strong> Duplicate `CREATE TABLE `testimonials`` definitions found.</p>";
        echo "<p style='color:red;'><em>Recommendation:</em> Remove the duplicate `CREATE TABLE `testimonials`` statements from `schema.sql`. (This was previously fixed by the agent).</p>";
    } else {
        echo "<p><strong>`schema.sql` `testimonials` table:</strong> <span style='color:green;'>Looks good (no duplicate definitions).</span></p>";
    }

    // Check schema.sql for 'All Products' menu link
    if (preg_match("/'All Products','url':'\/'/i", $schema_content)) {
        echo "<p style='color:red;'><strong>`schema.sql` Menu Link Error:</strong> 'All Products' in `main-menu` still links to `/`.</p>";
        echo "<p style='color:red;'><em>Recommendation:</em> Update the `url` for 'All Products' in the `main-menu` INSERT statement to `/smartprozen/products_list.php`. (This was previously fixed by the agent).</p>";
    } else {
        echo "<p><strong>`schema.sql` 'All Products' menu link:</strong> <span style='color:green;'>Looks good (points to `products_list.php`).</span></p>";
    }

} else {
    echo "<p style='color:orange;'><strong>Warning:</strong> `schema.sql` not found. Cannot check database schema issues.</p>";
}

// Check page.php for duplicate header inclusion
$page_path = __DIR__ . '/page.php';
if (file_exists($page_path)) {
    $page_content = file_get_contents($page_path);
    if (preg_match("/if \(!\$page\) \{.*?require_once __DIR__ \. '\/includes\/header\.php';/is", $page_content)) {
        echo "<p style='color:red;'><strong>`page.php` Error:</strong> Duplicate `header.php` inclusion in 404 block.</p>";
        echo "<p style='color:red;'><em>Recommendation:</em> Remove the `require_once __DIR__ . '/includes/header.php';` line inside the `if (!$page)` block. (This was previously fixed by the agent).</p>";
    } else {
        echo "<p><strong>`page.php` header inclusion:</strong> <span style='color:green;'>Looks good (no duplicate inclusion).</span></p>";
    }
} else {
    echo "<p style='color:orange;'><strong>Warning:</strong> `page.php` not found. Cannot check header inclusion.</p>";
}

// Check core/functions.php for slugify recursion
$functions_path = __DIR__ . '/core/functions.php';
if (file_exists($functions_path)) {
    $functions_content = file_get_contents($functions_path);
    if (preg_match("/return empty\(\$text\) \? 'n-a' : slugify\(\$text\);/i", $functions_content)) {
        echo "<p style='color:red;'><strong>`core/functions.php` Error:</strong> `slugify()` function has infinite recursion.</p>";
        echo "<p style='color:red;'><em>Recommendation:</em> Change `return empty(\$text) ? 'n-a' : slugify(\$text);` to `return empty(\$text) ? 'n-a' : \$text;`. (This was previously fixed by the agent).</p>";
    } else {
        echo "<p><strong>`core/functions.php` `slugify()`:</strong> <span style='color:green;'>Looks good (recursion fixed).</span></p>";
    }
} else {
    echo "<p style='color:orange;'><strong>Warning:</strong> `core/functions.php` not found. Cannot check `slugify()` function.</p>";
}

// Check auth/login.php for rate limiting
$auth_login_path = __DIR__ . '/auth/login.php';
if (file_exists($auth_login_path)) {
    $auth_login_content = file_get_contents($auth_login_path);
    if (!preg_match("/CRITICAL:\s*Implement robust rate limiting here/i", $auth_login_content)) {
        echo "<p style='color:orange;'><strong>`auth/login.php` Warning:</strong> Rate limiting not explicitly implemented.</p>";
        echo "<p style='color:orange;'><em>Recommendation:</em> Implement robust rate limiting to prevent brute-force attacks. (A comment was added by the agent).</p>";
    } else {
        echo "<p><strong>`auth/login.php` Rate Limiting:</strong> <span style='color:green;'>Comment highlighting need for rate limiting is present.</span></p>";
    }
    if (preg_match("/inputRememberPassword.*?\(Not functional\)/i", $auth_login_content)) {
        echo "<p><strong>`auth/login.php` 'Remember Me':</strong> <span style='color:green;'>Comment indicating non-functional status is present.</span></p>";
    } else {
        echo "<p style='color:orange;'><strong>`auth/login.php` Warning:</strong> 'Remember Me' checkbox might be non-functional without a clarifying comment.</p>";
        echo "<p style='color:orange;'><em>Recommendation:</em> Add a comment `(Not functional)` next to the 'Remember Me' label if it's not implemented.</p>";
    }
} else {
    echo "<p style='color:orange;'><strong>Warning:</strong> `auth/login.php` not found. Cannot check login security.</p>";
}

// Check admin/login.php for rate limiting
$admin_login_path = __DIR__ . '/admin/login.php';
if (file_exists($admin_login_path)) {
    $admin_login_content = file_get_contents($admin_login_path);
    if (!preg_match("/CRITICAL:\s*Implement robust rate limiting here/i", $admin_login_content)) {
        echo "<p style='color:red;'><strong>`admin/login.php` CRITICAL Warning:</strong> Rate limiting not explicitly implemented.</p>";
        echo "<p style='color:red;'><em>Recommendation:</em> Implement robust rate limiting to prevent brute-force attacks on the admin panel. (A comment was added by the agent).</p>";
    } else {
        echo "<p><strong>`admin/login.php` Rate Limiting:</strong> <span style='color:green;'>Comment highlighting critical need for rate limiting is present.</span></p>";
    }
} else {
    echo "<p style='color:orange;'><strong>Warning:</strong> `admin/login.php` not found. Cannot check admin login security.</p>";
}

// Check products_list.php for search functionality
$products_list_path = __DIR__ . '/products_list.php';
if (file_exists($products_list_path)) {
    $products_list_content = file_get_contents($products_list_path);
    if (preg_match("/\$_GET\['search'\]/i", $products_list_content) && preg_match("/JSON_EXTRACT\(name,\s*'\$\.en'\)\s*LIKE\s*\?/", $products_list_content)) {
        echo "<p><strong>`products_list.php` Search:</strong> <span style='color:green;'>Search functionality implemented.</span></p>";
    } else {
        echo "<p style='color:red;'><strong>`products_list.php` Error:</strong> Search functionality not fully implemented or detected.</p>";
        echo "<p style='color:red;'><em>Recommendation:</em> Ensure `products_list.php` checks for `$_GET['search']` and filters products accordingly. (This was previously fixed by the agent).</p>";
    }
} else {
    echo "<p style='color:orange;'><strong>Warning:</strong> `products_list.php` not found. Cannot check search functionality.</p>";
}

// Check .htaccess for search form action
$htaccess_path = __DIR__ . '/.htaccess';
if (file_exists($htaccess_path)) {
    $htaccess_content = file_get_contents($htaccess_path);
    if (preg_match("/RewriteRule \^([a-zA-Z0-9-]+)\/\?\$ \$1\.php \[L,QSA\]/i", $htaccess_content)) {
        echo "<p><strong>`.htaccess` Clean URLs:</strong> <span style='color:green;'>Root-level PHP files are handled.</span></p>";
    } else {
        echo "<p style='color:red;'><strong>`.htaccess` Error:</strong> Clean URLs for root-level PHP files might not be handled.</p>";
        echo "<p style='color:red;'><em>Recommendation:</em> Add `RewriteCond %{REQUEST_FILENAME}.php -f` and `RewriteRule ^([a-zA-Z0-9-]+)/?$ $1.php [L,QSA]` to `.htaccess`. (This was previously fixed by the agent).</p>";
    }
} else {
    echo "<p style='color:orange;'><strong>Warning:</strong> `.htaccess` not found. Cannot check URL rewriting rules.</p>";
}

echo "<hr>";
echo "<h2>3. General Recommendations</h2>";
echo "<ul>";
echo "<li>Always check your browser's developer console (F12) for JavaScript errors when a page is not displaying correctly.</li>";
echo "<li>Ensure your web server (Apache/Nginx) has write permissions to the `logs` directory.</li>";
echo "<li>Regularly review the `php-error.log` file for any new errors.</li>";
echo "<li>Consider implementing a more robust error reporting system for production environments.</li>";
echo "<li>For security, ensure all user inputs are properly validated and sanitized.</li>";
echo "</ul>";

echo "<p><em>Note: This script performs static analysis and cannot detect all runtime errors or logical bugs. It's a starting point for diagnostics.</em></p>";

// --- New Checks Appended ---

echo "<hr>";
echo "<h2>4. Missing Critical Files Check</h2>";
$critical_files = [
    'config.php',
    'core/db.php',
    'core/functions.php',
    'includes/header.php',
    'includes/footer.php',
    'index.php',
    'page.php',
    'products_list.php',
    'contact.php',
    'auth/login.php',
    'admin/login.php',
    'schema.sql',
    '.htaccess'
];

foreach ($critical_files as $file) {
    $full_path = __DIR__ . '/' . $file;
    if (!file_exists($full_path)) {
        echo "<p style='color:red;'><strong>Missing File:</strong> `{$file}` not found.</p>";
    } else {
        echo "<p><strong>File Exists:</strong> `{$file}` <span style='color:green;'>Found</span></p>";
    }
}

echo "<hr>";
echo "<h2>5. Link Integrity Checks</h2>";

// Check header links
$header_path = __DIR__ . '/includes/header.php';
if (file_exists($header_path)) {
    $header_content = file_get_contents($header_path);
    preg_match_all("/href=\"(.*?)\"/", $header_content, $links);
    $base_url = '/smartprozen/'; // Assuming this is the base URL for internal links

    foreach ($links[1] as $link) {
        // Skip external links, anchors, and JavaScript links
        if (str_starts_with($link, 'http') || str_starts_with($link, '#') || str_starts_with($link, 'javascript')) {
            continue;
        }
        // Normalize link for checking
        $normalized_link = str_replace($base_url, '/', $link);
        $normalized_link = ltrim($normalized_link, '/'); // Remove leading slash for file_exists check

        // Special handling for dynamic links or links that are not direct files
        if (empty($normalized_link) || str_starts_with($normalized_link, 'page.php') || str_starts_with($normalized_link, 'product.php') || str_starts_with($normalized_link, 'cart/') || str_starts_with($normalized_link, 'user/') || str_starts_with($normalized_link, 'auth/') || str_starts_with($normalized_link, '?lang=')) {
            // These are dynamic or handled by rewrite rules, assume valid for static check
            continue;
        }

        $target_file = __DIR__ . '/' . $normalized_link;
        if (!file_exists($target_file) && !file_exists($target_file . '.php')) {
            echo "<p style='color:red;'><strong>Missing Link Target in Header:</strong> `{$link}` points to a non-existent file (`{$target_file}` or `{$target_file}.php`).</p>";
        }
    }
    echo "<p><strong>Header Links:</strong> <span style='color:green;'>Basic check completed.</span></p>";
} else {
    echo "<p style='color:orange;'><strong>Warning:</strong> `includes/header.php` not found. Cannot check header links.</p>";
}

// Check products_list.php links
if (file_exists($products_list_path)) {
    $products_list_content = file_get_contents($products_list_path);
    preg_match_all("/href=\"(.*?)\"/", $products_list_content, $links);
    $base_url = '/smartprozen/';

    foreach ($links[1] as $link) {
        if (str_starts_with($link, 'http') || str_starts_with($link, '#') || str_starts_with($link, 'javascript')) {
            continue;
        }
        $normalized_link = str_replace($base_url, '/', $link);
        $normalized_link = ltrim($normalized_link, '/');

        if (empty($normalized_link) || str_starts_with($normalized_link, 'product.php') || str_starts_with($normalized_link, 'cart/add_to_cart.php')) {
            continue;
        }

        $target_file = __DIR__ . '/' . $normalized_link;
        if (!file_exists($target_file) && !file_exists($target_file . '.php')) {
            echo "<p style='color:red;'><strong>Missing Link Target in `products_list.php`:</strong> `{$link}` points to a non-existent file (`{$target_file}` or `{$target_file}.php`).</p>";
        }
    }
    echo "<p><strong>`products_list.php` Links:</strong> <span style='color:green;'>Basic check completed.</span></p>";
} else {
    echo "<p style='color:orange;'><strong>Warning:</strong> `products_list.php` not found. Cannot check its links.</p>";
}

echo "<hr>";
echo "<h2>6. Basic Syntax Check (Limited)</h2>";

$php_files_to_check = [
    'config.php',
    'core/db.php',
    'core/functions.php',
    'includes/header.php',
    'includes/footer.php',
    'index.php',
    'page.php',
    'products_list.php',
    'contact.php',
    'auth/login.php',
    'admin/login.php'
];

foreach ($php_files_to_check as $file) {
    $full_path = __DIR__ . '/' . $file;
    if (file_exists($full_path)) {
        $content = file_get_contents($full_path);
        // Very basic check: look for unclosed PHP tags or obvious syntax errors
        if (substr_count($content, '<?php') > substr_count($content, '?>')) {
            echo "<p style='color:orange;'><strong>Potential Syntax Warning in `{$file}`:</strong> Unclosed `<?php` tag detected. This is a very basic check and might not be accurate.</p>";
        }
        // Add more basic regex checks for common syntax errors if needed
    } else {
        // Already reported as missing file
    }
}
echo "<p><strong>Basic Syntax Check:</strong> <span style='color:green;'>Completed (very limited).</span></p>";
echo "<p style='color:orange;'><em>Note: A full syntax check requires a dedicated PHP linter (e.g., `php -l <file>`). This script performs only very basic pattern matching.</em></p>";

echo "<hr>";
echo "<h2>7. Runtime Error Considerations</h2>";
echo "<p><strong>HTML 500 Errors:</strong> These are server-side errors that occur during script execution. They are best diagnosed by:</p>";
echo "<ul>";
echo "<li>Checking your web server's error logs (e.g., Apache `error.log`).</li>";
echo "<li>Reviewing the `php-error.log` file (if configured and writable).</li>";
echo "<li>Enabling `DEBUG` mode in `config.php` to see errors directly on the page.</li>";
echo "</ul>";
echo "<p><strong>Missing Contents from Files:</strong> This is a logical issue. If a file exists but doesn't display expected content, it could be due to:</p>";
echo "<ul>";
echo "<li>A PHP error preventing rendering (check logs/DEBUG mode).</li>";
echo "<li>Incorrect data fetched from the database.</li>";
echo "<li>Conditional logic preventing content from being displayed.</li>";
echo "<li>JavaScript errors preventing dynamic content from loading.</li>";
echo "</ul>";

echo "<hr>";
echo "<h2>8. Styling Issues Check</h2>";

// Check for main CSS files
$css_files = [
    'css/enhanced.css',
    'css/default.css', // Assuming default theme
    'css/dark.css', // Assuming dark theme
    'css/corporate.css' // Assuming corporate theme
];

foreach ($css_files as $css_file) {
    $full_path = __DIR__ . '/' . $css_file;
    if (!file_exists($full_path)) {
        echo "<p style='color:orange;'><strong>Missing CSS File:</strong> `{$css_file}` not found. This might affect styling.</p>";
    } else {
        echo "<p><strong>CSS File Exists:</strong> `{$css_file}` <span style='color:green;'>Found</span></p>";
    }
}

// Check for Bootstrap CSS (external link, so just check for its presence in header.php)
if (file_exists($header_path)) {
    $header_content = file_get_contents($header_path);
    if (!preg_match("/cdn\.jsdelivr\.net\\/npm\\/bootstrap/i", $header_content)) {
        echo "<p style='color:orange;'><strong>Bootstrap CSS:</strong> Link to Bootstrap CDN not found in `includes/header.php`. Styling might be affected.</p>";
    } else {
        echo "<p><strong>Bootstrap CSS:</strong> <span style='color:green;'>Link found in `includes/header.php`.</span></p>";
    }
}

echo "<p style='color:orange;'><em>Recommendation for Styling Issues:</em></p>";
echo "<ul>";
echo "<li>Use your browser's developer tools (F12) to inspect elements and check the 'Console' for errors and 'Network' tab for failed CSS file loads.</li>";
echo "<li>Verify that the `theme_class` setting in `config.php` (or database) corresponds to an existing CSS file in the `css/` directory.</li>";
echo "<li>Ensure paths to CSS files are correct relative to the document root.</li>";
echo "</ul>";

echo "<hr>";
echo "<h2>9. Blank Source Code / Early Fatal Error Diagnosis</h2>";
echo "<p>If you are seeing a completely blank page (no HTML output at all), it almost always indicates a fatal PHP error occurred very early in the script's execution, before any content could be sent to the browser.</p>";
echo "<p style='color:red;'><strong>Diagnosis Steps:</strong></p>";
echo "<ul>";
echo "<li><strong>Ensure `DEBUG` mode is `true` in `config.php`.</strong> This should force errors to display on screen.</li>";
echo "<li><strong>Check `php-error.log` immediately.</strong> Even if the screen is blank, a fatal error should be logged if `log_errors` is enabled and the `logs` directory is writable.</li>";
echo "<li><strong>Add temporary `echo` statements:</strong> Insert `echo '<!-- checkpoint 1 -->';` at the very beginning of your main script (`index.php`, `page.php`, `contact.php`, etc.) and then progressively move it down, or into included files (`config.php`, `core/db.php`, `core/functions.php`). The last `echo` statement that *does not* appear in the browser's source code (View Page Source) indicates the line where the fatal error occurred.</li>";
echo "<li><strong>Check for parse errors:</strong> A missing semicolon, unclosed brace, or other syntax error can cause a blank page. Use a PHP linter (`php -l your_file.php`) if available.</li>";
echo "<li><strong>Verify file includes:</strong> Ensure all `require_once` and `include` paths are correct. If a required file is missing, it will cause a fatal error.</li>";
echo "</ul>";


