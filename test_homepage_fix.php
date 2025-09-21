<?php
require_once 'config.php';
require_once 'core/db.php';

echo "<h2>Testing Homepage Content Fix</h2>";

try {
    echo "<h3>📊 Current Database Status:</h3>";
    
    // Check products
    $result = $conn->query("SELECT COUNT(*) as count FROM products WHERE is_published = 1");
    $product_count = $result->fetch_assoc()['count'];
    echo "<p>📦 Published products: <strong>$product_count</strong></p>";
    
    // Check featured products
    $result = $conn->query("SELECT COUNT(*) as count FROM products WHERE is_featured = 1 AND is_published = 1");
    $featured_count = $result->fetch_assoc()['count'];
    echo "<p>⭐ Featured products: <strong>$featured_count</strong></p>";
    
    // Check categories
    $result = $conn->query("SELECT COUNT(*) as count FROM product_categories WHERE is_active = 1");
    $category_count = $result->fetch_assoc()['count'];
    echo "<p>📂 Active categories: <strong>$category_count</strong></p>";
    
    // Check testimonials
    $result = $conn->query("SELECT COUNT(*) as count FROM testimonials WHERE is_featured = 1 AND is_published = 1");
    $testimonial_count = $result->fetch_assoc()['count'];
    echo "<p>💬 Featured testimonials: <strong>$testimonial_count</strong></p>";
    
    // Check homepage sections
    $result = $conn->query("SELECT COUNT(*) as count FROM page_sections WHERE page_id = 1 AND is_active = 1");
    $section_count = $result->fetch_assoc()['count'];
    echo "<p>📄 Homepage sections: <strong>$section_count</strong></p>";
    
    echo "<h3>🔍 Sample Content Preview:</h3>";
    
    // Show sample products
    echo "<h4>Featured Products:</h4>";
    $result = $conn->query("SELECT name, price, sale_price, featured_image FROM products WHERE is_featured = 1 AND is_published = 1 LIMIT 3");
    if ($result->num_rows > 0) {
        echo "<ul>";
        while ($product = $result->fetch_assoc()) {
            $display_price = !empty($product['sale_price']) ? $product['sale_price'] : $product['price'];
            echo "<li><strong>{$product['name']}</strong> - $" . number_format($display_price, 2);
            if (!empty($product['featured_image'])) {
                echo " (Image: {$product['featured_image']})";
            }
            echo "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>❌ No featured products found</p>";
    }
    
    // Show sample categories
    echo "<h4>Categories:</h4>";
    $result = $conn->query("SELECT name, slug, description FROM product_categories WHERE is_active = 1 ORDER BY display_order LIMIT 3");
    if ($result->num_rows > 0) {
        echo "<ul>";
        while ($category = $result->fetch_assoc()) {
            echo "<li><strong>{$category['name']}</strong> ({$category['slug']}) - " . substr($category['description'], 0, 50) . "...</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>❌ No categories found</p>";
    }
    
    // Show sample testimonials
    echo "<h4>Featured Testimonials:</h4>";
    $result = $conn->query("SELECT name, company, rating, testimonial FROM testimonials WHERE is_featured = 1 AND is_published = 1 LIMIT 2");
    if ($result->num_rows > 0) {
        echo "<ul>";
        while ($testimonial = $result->fetch_assoc()) {
            echo "<li><strong>{$testimonial['name']}</strong> ({$testimonial['company']}) - {$testimonial['rating']} stars<br>";
            echo "<em>" . substr($testimonial['testimonial'], 0, 100) . "...</em></li>";
        }
        echo "</ul>";
    } else {
        echo "<p>❌ No testimonials found</p>";
    }
    
    // Show homepage sections
    echo "<h4>Homepage Sections:</h4>";
    $result = $conn->query("SELECT section_type, title, display_order FROM page_sections WHERE page_id = 1 AND is_active = 1 ORDER BY display_order");
    if ($result->num_rows > 0) {
        echo "<ul>";
        while ($section = $result->fetch_assoc()) {
            echo "<li><strong>{$section['section_type']}</strong>: {$section['title']} (Order: {$section['display_order']})</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>❌ No homepage sections found</p>";
    }
    
    echo "<h3>🎯 Status Summary:</h3>";
    
    $all_good = true;
    
    if ($product_count >= 6) {
        echo "<p>✅ Products: <strong>GOOD</strong> ($product_count products)</p>";
    } else {
        echo "<p>❌ Products: <strong>NEEDS MORE</strong> ($product_count products, need 6+)</p>";
        $all_good = false;
    }
    
    if ($category_count >= 6) {
        echo "<p>✅ Categories: <strong>GOOD</strong> ($category_count categories)</p>";
    } else {
        echo "<p>❌ Categories: <strong>NEEDS MORE</strong> ($category_count categories, need 6+)</p>";
        $all_good = false;
    }
    
    if ($testimonial_count >= 4) {
        echo "<p>✅ Testimonials: <strong>GOOD</strong> ($testimonial_count testimonials)</p>";
    } else {
        echo "<p>❌ Testimonials: <strong>NEEDS MORE</strong> ($testimonial_count testimonials, need 4+)</p>";
        $all_good = false;
    }
    
    if ($section_count >= 4) {
        echo "<p>✅ Homepage Sections: <strong>GOOD</strong> ($section_count sections)</p>";
    } else {
        echo "<p>❌ Homepage Sections: <strong>NEEDS MORE</strong> ($section_count sections, need 4+)</p>";
        $all_good = false;
    }
    
    if ($all_good) {
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h4>🎉 All Good! Your homepage should be working perfectly now!</h4>";
        echo "<p><a href='" . SITE_URL . "' target='_blank'>🏠 Test Your Homepage</a></p>";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h4>⚠️ Some content is missing. Run the fix script:</h4>";
        echo "<p><a href='fix_homepage_content.php' target='_blank'>🔧 Run Homepage Content Fix</a></p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
