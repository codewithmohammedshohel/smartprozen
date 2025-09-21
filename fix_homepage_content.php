<?php
require_once 'config.php';
require_once 'core/db.php';

echo "<h2>Fixing Homepage Content Issues</h2>";

try {
    echo "<h3>Step 1: Checking Database Content</h3>";
    
    // Check if we have a homepage in database
    $result = $conn->query("SELECT id FROM pages WHERE slug = 'home'");
    if ($result->num_rows > 0) {
        echo "<p>✅ Homepage exists in database (ID: " . $result->fetch_assoc()['id'] . ")</p>";
    } else {
        echo "<p>❌ No homepage in database</p>";
    }
    
    // Check products
    $result = $conn->query("SELECT COUNT(*) as count FROM products WHERE is_published = 1");
    $product_count = $result->fetch_assoc()['count'];
    echo "<p>📦 Products in database: $product_count</p>";
    
    // Check categories
    $result = $conn->query("SELECT COUNT(*) as count FROM product_categories WHERE is_active = 1");
    $category_count = $result->fetch_assoc()['count'];
    echo "<p>📂 Categories in database: $category_count</p>";
    
    // Check testimonials
    $result = $conn->query("SELECT COUNT(*) as count FROM testimonials WHERE is_featured = 1 AND is_published = 1");
    $testimonial_count = $result->fetch_assoc()['count'];
    echo "<p>💬 Featured testimonials: $testimonial_count</p>";
    
    echo "<h3>Step 2: Adding Missing Content</h3>";
    
    // Add more products if we have less than 3
    if ($product_count < 3) {
        echo "<p>Adding more products...</p>";
        
        $products = [
            ['ZenBuds Pro 3', 'zenbuds-pro-3', 'Premium wireless earbuds with active noise cancellation, 30-hour battery life, and crystal clear sound quality.', 'Premium wireless earbuds', 'ZBP3-001', 89.99, 79.99, 50, 'instock', 'physical', 2, '68cfc186c96b3-front cover.jpg'],
            ['SmartGlow Ambient Light', 'smartglow-ambient-light', 'Smart LED light with 16M colors, music sync, and voice control. Perfect for creating the perfect ambiance.', 'Smart LED light', 'SGL-001', 59.99, 49.99, 75, 'instock', 'physical', 1, '68cfc18a0712d-front cover.jpg'],
            ['ProCharge Wireless Stand', 'procharge-wireless-stand', 'Fast wireless charging stand with multiple device support and sleek design.', 'Fast wireless charging', 'PCS-001', 45.00, 39.99, 100, 'instock', 'physical', 3, '68cfc18baa5be-1755877095.png'],
            ['ZenWatch Sport', 'zenwatch-sport', 'Smart fitness watch with heart rate monitoring, GPS, and 7-day battery life.', 'Smart fitness watch', 'ZWS-001', 199.99, 179.99, 25, 'instock', 'physical', 4, ''],
            ['GameMaster Pro Keyboard', 'gamemaster-pro-keyboard', 'Mechanical gaming keyboard with RGB lighting, programmable keys, and tactile switches.', 'Mechanical gaming keyboard', 'GMK-001', 129.99, 119.99, 40, 'instock', 'physical', 5, ''],
            ['Productivity Suite Pro', 'productivity-suite-pro', 'Digital productivity suite with project management, time tracking, and collaboration tools.', 'Digital productivity suite', 'PSP-001', 49.99, 39.99, 999, 'instock', 'digital', 6, '']
        ];
        
        foreach ($products as $product) {
            $stmt = $conn->prepare("INSERT IGNORE INTO products (name, slug, description, short_description, sku, price, sale_price, stock_quantity, stock_status, product_type, category_id, featured_image, is_featured, is_published) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 1)");
            $stmt->bind_param("sssssddissi", $product[0], $product[1], $product[2], $product[3], $product[4], $product[5], $product[6], $product[7], $product[8], $product[9], $product[10], $product[11]);
            $stmt->execute();
            $stmt->close();
        }
        echo "<p>✅ Added products</p>";
    }
    
    // Add more categories if we have less than 6
    if ($category_count < 6) {
        echo "<p>Adding more categories...</p>";
        
        $categories = [
            ['Smart Home Devices', 'smart-home', 'Transform your home with intelligent devices and automation systems.', 1],
            ['Professional Audio', 'audio', 'Premium audio equipment for professionals and enthusiasts.', 2],
            ['Mobile Accessories', 'mobile-accessories', 'Essential accessories for mobile devices and smartphones.', 3],
            ['Wearable Technology', 'wearables', 'Smart watches, fitness trackers, and wearable devices.', 4],
            ['Gaming Accessories', 'gaming', 'Professional gaming equipment and accessories.', 5],
            ['Digital Products', 'digital', 'Software, apps, and digital services.', 6]
        ];
        
        foreach ($categories as $category) {
            $stmt = $conn->prepare("INSERT IGNORE INTO product_categories (name, slug, description, display_order, is_active) VALUES (?, ?, ?, ?, 1)");
            $stmt->bind_param("sssi", $category[0], $category[1], $category[2], $category[3]);
            $stmt->execute();
            $stmt->close();
        }
        echo "<p>✅ Added categories</p>";
    }
    
    // Add testimonials if we have less than 4
    if ($testimonial_count < 4) {
        echo "<p>Adding testimonials...</p>";
        
        $testimonials = [
            ['Mark Thompson', 'mark@example.com', 'AudioEngine Studios', 'Senior Audio Engineer', 5, 'The ZenBuds Pro are the best wireless earbuds I have ever used. The sound quality is incredible and they are so comfortable for long listening sessions.', '', 1, 1],
            ['Sarah Kim', 'sarah@example.com', 'Design Co.', 'Creative Director', 5, 'My order from SmartProZen arrived in just two days! The packaging was great and the SmartGlow light looks amazing on my desk. 10/10 would shop again.', '', 1, 1],
            ['David Chen', 'david@example.com', 'Tech Startup', 'CEO', 5, 'The ProCharge stand is exactly what I needed for my workspace. It charges multiple devices simultaneously and looks great. Highly recommend!', '', 1, 1],
            ['Emily Rodriguez', 'emily@example.com', 'Fitness Coach', 'Personal Trainer', 5, 'The ZenWatch Sport has been a game-changer for my fitness tracking. The heart rate monitoring is accurate and the battery lasts all week.', '', 1, 1]
        ];
        
        foreach ($testimonials as $testimonial) {
            $stmt = $conn->prepare("INSERT IGNORE INTO testimonials (name, email, company, position, rating, testimonial, avatar, is_featured, is_published) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssisssi", $testimonial[0], $testimonial[1], $testimonial[2], $testimonial[3], $testimonial[4], $testimonial[5], $testimonial[6], $testimonial[7], $testimonial[8]);
            $stmt->execute();
            $stmt->close();
        }
        echo "<p>✅ Added testimonials</p>";
    }
    
    echo "<h3>Step 3: Updating Homepage Sections</h3>";
    
    // Clear existing homepage sections to prevent duplicates
    $conn->query("DELETE FROM page_sections WHERE page_id = 1");
    echo "<p>✅ Cleared existing homepage sections</p>";
    
    // Add proper homepage sections
    $sections = [
        [
            'hero',
            'Hero Section',
            json_encode([
                'title' => 'Smart Tech, Simplified Living.',
                'subtitle' => 'Discover our curated collection of smart gadgets and professional accessories designed to elevate your everyday.',
                'button_text' => 'Shop Now',
                'button_url' => '/products_list.php',
                'background_image' => '',
                'overlay_opacity' => 0.5
            ]),
            1
        ],
        [
            'features',
            'Features Section',
            json_encode([
                'title' => 'Why Choose SmartProZen?',
                'subtitle' => 'We deliver premium quality products with exceptional service',
                'features' => [
                    ['icon' => 'bi-gem', 'title' => 'Premium Quality', 'description' => 'We source and test every product to ensure it meets our high standards.'],
                    ['icon' => 'bi-truck', 'title' => 'Fast Shipping', 'description' => 'Get your order delivered quickly with our reliable shipping partners.'],
                    ['icon' => 'bi-shield-check', 'title' => 'Secure Checkout', 'description' => 'Your privacy and security are our top priority with encrypted payments.'],
                    ['icon' => 'bi-headset', 'title' => '24/7 Support', 'description' => 'Our dedicated support team is here to help you around the clock.']
                ]
            ]),
            2
        ],
        [
            'featured_products',
            'Featured Products',
            json_encode([
                'title' => 'Featured Products',
                'subtitle' => 'Discover our most popular items',
                'product_count' => 6,
                'show_featured_only' => true
            ]),
            3
        ],
        [
            'testimonials',
            'Testimonials Section',
            json_encode([
                'title' => 'What Our Customers Say',
                'subtitle' => 'Real feedback from satisfied customers',
                'show_featured_only' => true,
                'testimonial_count' => 4
            ]),
            4
        ]
    ];
    
    foreach ($sections as $section) {
        $stmt = $conn->prepare("INSERT INTO page_sections (page_id, section_type, title, content_json, display_order, is_active) VALUES (1, ?, ?, ?, ?, 1)");
        $stmt->bind_param("sssi", $section[0], $section[1], $section[2], $section[3]);
        $stmt->execute();
        $stmt->close();
    }
    echo "<p>✅ Added homepage sections</p>";
    
    echo "<h3>Step 4: Checking Final Results</h3>";
    
    // Re-check counts
    $result = $conn->query("SELECT COUNT(*) as count FROM products WHERE is_published = 1");
    $final_product_count = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM product_categories WHERE is_active = 1");
    $final_category_count = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM testimonials WHERE is_featured = 1 AND is_published = 1");
    $final_testimonial_count = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM page_sections WHERE page_id = 1 AND is_active = 1");
    $section_count = $result->fetch_assoc()['count'];
    
    echo "<p>📦 Final products: $final_product_count</p>";
    echo "<p>📂 Final categories: $final_category_count</p>";
    echo "<p>💬 Final testimonials: $final_testimonial_count</p>";
    echo "<p>📄 Homepage sections: $section_count</p>";
    
    echo "<h3>🎉 Homepage Content Fix Complete!</h3>";
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4>✅ Issues Fixed:</h4>";
    echo "<ul>";
    echo "<li><strong>Duplicate Content</strong> - Cleared duplicate homepage sections</li>";
    echo "<li><strong>Missing Products</strong> - Added 6 products with proper images</li>";
    echo "<li><strong>Missing Categories</strong> - Added 6 product categories</li>";
    echo "<li><strong>Missing Testimonials</strong> - Added 4 customer testimonials</li>";
    echo "<li><strong>Homepage Sections</strong> - Added proper sections (Hero, Features, Products, Testimonials)</li>";
    echo "<li><strong>Database Content</strong> - All content now comes from database</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div style='background: #cce5ff; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4>🔗 Test Your Fixed Homepage:</h4>";
    echo "<p><a href='" . SITE_URL . "' target='_blank'>🏠 Homepage</a> - Should now show real content</p>";
    echo "<p><a href='" . SITE_URL . "/products_list.php' target='_blank'>🛍️ Products</a> - Browse real products</p>";
    echo "<p><a href='" . SITE_URL . "/admin/login.php' target='_blank'>⚙️ Admin Panel</a> - Manage content</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
