<?php
require_once 'config.php';
require_once 'core/db.php';

echo "<h2>🎨 Template Bundle Test Results</h2>";

try {
    echo "<h3>📊 Content Inventory</h3>";
    
    // Check pages
    $result = $conn->query("SELECT COUNT(*) as count FROM pages WHERE is_published = 1");
    $page_count = $result->fetch_assoc()['count'];
    echo "<p>📄 <strong>Published Pages:</strong> $page_count</p>";
    
    // Check products
    $result = $conn->query("SELECT COUNT(*) as count FROM products WHERE is_published = 1");
    $product_count = $result->fetch_assoc()['count'];
    echo "<p>📦 <strong>Published Products:</strong> $product_count</p>";
    
    // Check categories
    $result = $conn->query("SELECT COUNT(*) as count FROM product_categories WHERE is_active = 1");
    $category_count = $result->fetch_assoc()['count'];
    echo "<p>📂 <strong>Active Categories:</strong> $category_count</p>";
    
    // Check testimonials
    $result = $conn->query("SELECT COUNT(*) as count FROM testimonials WHERE is_published = 1");
    $testimonial_count = $result->fetch_assoc()['count'];
    echo "<p>💬 <strong>Published Testimonials:</strong> $testimonial_count</p>";
    
    // Check coupons
    $result = $conn->query("SELECT COUNT(*) as count FROM coupons WHERE is_active = 1");
    $coupon_count = $result->fetch_assoc()['count'];
    echo "<p>🎫 <strong>Active Coupons:</strong> $coupon_count</p>";
    
    // Check menus
    $result = $conn->query("SELECT COUNT(*) as count FROM menus WHERE is_active = 1");
    $menu_count = $result->fetch_assoc()['count'];
    echo "<p>🔗 <strong>Active Menus:</strong> $menu_count</p>";
    
    echo "<h3>📋 Sample Pages Created</h3>";
    $result = $conn->query("SELECT title, slug, meta_title FROM pages WHERE is_published = 1 ORDER BY title");
    if ($result->num_rows > 0) {
        echo "<ul>";
        while ($page = $result->fetch_assoc()) {
            echo "<li><strong>{$page['title']}</strong> ({$page['slug']}) - <a href='" . SITE_URL . "/{$page['slug']}' target='_blank'>View</a></li>";
        }
        echo "</ul>";
    }
    
    echo "<h3>🛍️ Product Categories</h3>";
    $result = $conn->query("SELECT name, slug, description FROM product_categories WHERE is_active = 1 ORDER BY display_order");
    if ($result->num_rows > 0) {
        echo "<ul>";
        while ($category = $result->fetch_assoc()) {
            echo "<li><strong>{$category['name']}</strong> ({$category['slug']}) - " . substr($category['description'], 0, 50) . "...</li>";
        }
        echo "</ul>";
    }
    
    echo "<h3>⭐ Featured Products</h3>";
    $result = $conn->query("SELECT name, price, sale_price, category_id FROM products WHERE is_featured = 1 AND is_published = 1 ORDER BY name LIMIT 5");
    if ($result->num_rows > 0) {
        echo "<ul>";
        while ($product = $result->fetch_assoc()) {
            $display_price = !empty($product['sale_price']) ? $product['sale_price'] : $product['price'];
            echo "<li><strong>{$product['name']}</strong> - $" . number_format($display_price, 2);
            if (!empty($product['sale_price'])) {
                echo " <span style='color: red;'>(Was: $" . number_format($product['price'], 2) . ")</span>";
            }
            echo "</li>";
        }
        echo "</ul>";
    }
    
    echo "<h3>💬 Customer Testimonials</h3>";
    $result = $conn->query("SELECT name, company, rating, testimonial FROM testimonials WHERE is_published = 1 ORDER BY rating DESC LIMIT 3");
    if ($result->num_rows > 0) {
        echo "<ul>";
        while ($testimonial = $result->fetch_assoc()) {
            $stars = str_repeat('⭐', $testimonial['rating']);
            echo "<li><strong>{$testimonial['name']}</strong> from {$testimonial['company']} {$stars}<br>";
            echo "<em>" . substr($testimonial['testimonial'], 0, 100) . "...</em></li>";
        }
        echo "</ul>";
    }
    
    echo "<h3>🎫 Available Coupons</h3>";
    $result = $conn->query("SELECT code, name, discount_type, discount_value, discount_amount FROM coupons WHERE is_active = 1 ORDER BY code");
    if ($result->num_rows > 0) {
        echo "<ul>";
        while ($coupon = $result->fetch_assoc()) {
            $discount = $coupon['discount_type'] === 'percentage' ? 
                $coupon['discount_value'] . '%' : 
                '$' . number_format($coupon['discount_amount'], 2);
            echo "<li><strong>{$coupon['code']}</strong> - {$coupon['name']} ({$discount} off)</li>";
        }
        echo "</ul>";
    }
    
    echo "<h3>🔗 Navigation Menus</h3>";
    $result = $conn->query("SELECT name, location, menu_items FROM menus WHERE is_active = 1");
    if ($result->num_rows > 0) {
        while ($menu = $result->fetch_assoc()) {
            $menu_items = json_decode($menu['menu_items'], true);
            echo "<h4>{$menu['name']} ({$menu['location']})</h4>";
            echo "<ul>";
            foreach ($menu_items as $item) {
                echo "<li><strong>{$item['title']}</strong> - {$item['url']}";
                if (isset($item['children']) && count($item['children']) > 0) {
                    echo " <small>(" . count($item['children']) . " sub-items)</small>";
                }
                echo "</li>";
            }
            echo "</ul>";
        }
    }
    
    echo "<h3>✅ Status Check</h3>";
    
    $all_good = true;
    
    if ($page_count >= 5) {
        echo "<p>✅ <strong>Pages:</strong> GOOD ($page_count pages created)</p>";
    } else {
        echo "<p>❌ <strong>Pages:</strong> NEEDS MORE ($page_count pages, need 5+)</p>";
        $all_good = false;
    }
    
    if ($product_count >= 10) {
        echo "<p>✅ <strong>Products:</strong> GOOD ($product_count products created)</p>";
    } else {
        echo "<p>❌ <strong>Products:</strong> NEEDS MORE ($product_count products, need 10+)</p>";
        $all_good = false;
    }
    
    if ($category_count >= 6) {
        echo "<p>✅ <strong>Categories:</strong> GOOD ($category_count categories created)</p>";
    } else {
        echo "<p>❌ <strong>Categories:</strong> NEEDS MORE ($category_count categories, need 6+)</p>";
        $all_good = false;
    }
    
    if ($testimonial_count >= 5) {
        echo "<p>✅ <strong>Testimonials:</strong> GOOD ($testimonial_count testimonials created)</p>";
    } else {
        echo "<p>❌ <strong>Testimonials:</strong> NEEDS MORE ($testimonial_count testimonials, need 5+)</p>";
        $all_good = false;
    }
    
    if ($coupon_count >= 3) {
        echo "<p>✅ <strong>Coupons:</strong> GOOD ($coupon_count coupons created)</p>";
    } else {
        echo "<p>❌ <strong>Coupons:</strong> NEEDS MORE ($coupon_count coupons, need 3+)</p>";
        $all_good = false;
    }
    
    if ($menu_count >= 2) {
        echo "<p>✅ <strong>Menus:</strong> GOOD ($menu_count menus created)</p>";
    } else {
        echo "<p>❌ <strong>Menus:</strong> NEEDS MORE ($menu_count menus, need 2+)</p>";
        $all_good = false;
    }
    
    if ($all_good) {
        echo "<div style='background: #d4edda; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h4>🎉 Template Bundle Successfully Installed!</h4>";
        echo "<p>Your website now has a complete set of content and should no longer show empty messages.</p>";
        echo "</div>";
        
        echo "<div style='background: #cce5ff; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h4>🔗 Test Your Complete Website:</h4>";
        echo "<p><a href='" . SITE_URL . "' target='_blank'>🏠 Homepage</a> - Should show real content</p>";
        echo "<p><a href='" . SITE_URL . "/about' target='_blank'>📖 About Page</a> - Complete with sections</p>";
        echo "<p><a href='" . SITE_URL . "/products_list.php' target='_blank'>🛍️ Products</a> - Browse categories</p>";
        echo "<p><a href='" . SITE_URL . "/contact.php' target='_blank'>📞 Contact</a> - Contact information</p>";
        echo "<p><a href='" . SITE_URL . "/admin/dashboard.php' target='_blank'>⚙️ Admin Panel</a> - Manage content</p>";
        echo "</div>";
        
    } else {
        echo "<div style='background: #f8d7da; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h4>⚠️ Template Bundle Installation Incomplete</h4>";
        echo "<p>Some content is missing. Please run the template bundle installer:</p>";
        echo "<p><a href='template_bundle_installer.php' target='_blank'>🎨 Run Template Bundle Installer</a></p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
