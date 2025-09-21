<?php
require_once 'config.php';
require_once 'core/db.php';

echo "<h2>🎨 SmartProZen Template Bundle Installer</h2>";
echo "<p>This installer will create a complete set of templates, pages, sections, and sample content.</p>";

try {
    echo "<h3>Step 1: Creating Sample Pages</h3>";
    
    // Sample pages
    $pages = [
        [
            'title' => 'About Us',
            'slug' => 'about',
            'content' => '{"en": "Learn more about SmartProZen and our mission to deliver smart technology solutions."}',
            'template_slug' => 'default_page',
            'meta_title' => 'About Us - SmartProZen',
            'meta_description' => 'Learn about SmartProZen\'s mission to deliver smart technology solutions and exceptional customer service.',
            'is_published' => 1,
            'is_homepage' => 0
        ],
        [
            'title' => 'Services',
            'slug' => 'services',
            'content' => '{"en": "Our comprehensive range of smart technology services and solutions."}',
            'template_slug' => 'default_page',
            'meta_title' => 'Services - SmartProZen',
            'meta_description' => 'Explore our smart technology services including consultation, installation, and support.',
            'is_published' => 1,
            'is_homepage' => 0
        ],
        [
            'title' => 'Support',
            'slug' => 'support',
            'content' => '{"en": "Get help and support for all your SmartProZen products and services."}',
            'template_slug' => 'default_page',
            'meta_title' => 'Support - SmartProZen',
            'meta_description' => 'Access technical support, documentation, and help resources for SmartProZen products.',
            'is_published' => 1,
            'is_homepage' => 0
        ],
        [
            'title' => 'Privacy Policy',
            'slug' => 'privacy-policy',
            'content' => '{"en": "Our privacy policy and how we protect your personal information."}',
            'template_slug' => 'default_page',
            'meta_title' => 'Privacy Policy - SmartProZen',
            'meta_description' => 'Read our privacy policy to understand how we collect, use, and protect your information.',
            'is_published' => 1,
            'is_homepage' => 0
        ],
        [
            'title' => 'Terms of Service',
            'slug' => 'terms-of-service',
            'content' => '{"en": "Terms and conditions for using SmartProZen services and products."}',
            'template_slug' => 'default_page',
            'meta_title' => 'Terms of Service - SmartProZen',
            'meta_description' => 'Review the terms and conditions for using SmartProZen products and services.',
            'is_published' => 1,
            'is_homepage' => 0
        ]
    ];
    
    foreach ($pages as $page) {
        $stmt = $conn->prepare("INSERT IGNORE INTO pages (title, slug, content, template_slug, meta_title, meta_description, is_published, is_homepage) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssii", $page['title'], $page['slug'], $page['content'], $page['template_slug'], $page['meta_title'], $page['meta_description'], $page['is_published'], $page['is_homepage']);
        $stmt->execute();
        $page_id = $conn->insert_id;
        $stmt->close();
        
        echo "<p>✅ Created page: {$page['title']}</p>";
        
        // Add sections to each page
        if ($page['slug'] === 'about') {
            // About page sections
            $about_sections = [
                [
                    'section_type' => 'hero',
                    'title' => 'About Us Hero',
                    'content_json' => json_encode([
                        'title' => 'About SmartProZen',
                        'subtitle' => 'We are passionate about bringing smart technology to your everyday life',
                        'button_text' => 'Learn More',
                        'button_url' => '#our-story',
                        'background_image' => '',
                        'overlay_opacity' => 0.5
                    ]),
                    'display_order' => 1
                ],
                [
                    'section_type' => 'features',
                    'title' => 'Why Choose Us',
                    'content_json' => json_encode([
                        'title' => 'Why Choose SmartProZen?',
                        'subtitle' => 'We deliver excellence in every product and service',
                        'features' => [
                            ['icon' => 'bi-award', 'title' => 'Quality Assurance', 'description' => 'Every product undergoes rigorous testing to ensure premium quality.'],
                            ['icon' => 'bi-people', 'title' => 'Expert Team', 'description' => 'Our team of professionals brings years of experience in smart technology.'],
                            ['icon' => 'bi-heart', 'title' => 'Customer First', 'description' => 'Your satisfaction is our priority, backed by our customer-first approach.'],
                            ['icon' => 'bi-lightning', 'title' => 'Innovation', 'description' => 'We stay ahead of technology trends to bring you the latest innovations.']
                        ]
                    ]),
                    'display_order' => 2
                ],
                [
                    'section_type' => 'rich_text',
                    'title' => 'Our Story',
                    'content_json' => json_encode([
                        'title' => 'Our Story',
                        'content' => '<p>Founded in 2020, SmartProZen emerged from a simple vision: to make smart technology accessible, reliable, and beneficial for everyone. We started as a small team of technology enthusiasts who believed that smart devices should enhance your life, not complicate it.</p><p>Today, we have grown into a trusted name in smart technology, serving thousands of customers worldwide. Our commitment to quality, innovation, and customer satisfaction remains unwavering.</p>'
                    ]),
                    'display_order' => 3
                ]
            ];
            
            foreach ($about_sections as $section) {
                $stmt = $conn->prepare("INSERT IGNORE INTO page_sections (page_id, section_type, title, content_json, display_order, is_active) VALUES (?, ?, ?, ?, ?, 1)");
                $stmt->bind_param("isssi", $page_id, $section['section_type'], $section['title'], $section['content_json'], $section['display_order']);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
    
    echo "<h3>Step 2: Creating Sample Categories</h3>";
    
    // Sample categories
    $categories = [
        ['Smart Home Devices', 'smart-home', 'Transform your home with intelligent devices and automation systems that make life easier and more convenient.', 1],
        ['Professional Audio', 'professional-audio', 'Premium audio equipment for professionals, musicians, and audio enthusiasts.', 2],
        ['Mobile Accessories', 'mobile-accessories', 'Essential accessories for mobile devices, smartphones, and tablets.', 3],
        ['Wearable Technology', 'wearable-tech', 'Smart watches, fitness trackers, and wearable devices for health and productivity.', 4],
        ['Gaming Accessories', 'gaming-accessories', 'Professional gaming equipment, peripherals, and accessories for gamers.', 5],
        ['Digital Products', 'digital-products', 'Software, apps, digital services, and online tools for productivity.', 6],
        ['Home Office', 'home-office', 'Everything you need to create the perfect home office setup.', 7],
        ['Security & Safety', 'security-safety', 'Smart security systems and safety devices for your home and family.', 8]
    ];
    
    foreach ($categories as $category) {
        $stmt = $conn->prepare("INSERT IGNORE INTO product_categories (name, slug, description, display_order, is_active) VALUES (?, ?, ?, ?, 1)");
        $stmt->bind_param("sssi", $category[0], $category[1], $category[2], $category[3]);
        $stmt->execute();
        $stmt->close();
        echo "<p>✅ Created category: {$category[0]}</p>";
    }
    
    echo "<h3>Step 3: Creating Sample Products</h3>";
    
    // Sample products
    $products = [
        // Smart Home Devices
        ['SmartHome Hub Pro', 'smarthome-hub-pro', 'Central control hub for all your smart home devices with voice control and automation.', 'Smart home control hub', 'SHH-001', 199.99, 179.99, 25, 'instock', 'physical', 1, 'smart-home-hub.jpg', 1, 1],
        ['SmartLight Bulbs (4-Pack)', 'smartlight-bulbs-4pack', 'WiFi-enabled smart LED bulbs with 16M colors and voice control compatibility.', 'Smart LED light bulbs', 'SLB-004', 79.99, 69.99, 50, 'instock', 'physical', 1, 'smart-bulbs.jpg', 1, 1],
        ['SmartDoor Lock', 'smart-door-lock', 'Keyless entry system with fingerprint recognition and mobile app control.', 'Smart door lock system', 'SDL-001', 249.99, 229.99, 15, 'instock', 'physical', 1, 'smart-lock.jpg', 1, 1],
        
        // Professional Audio
        ['ProAudio Studio Headphones', 'proaudio-studio-headphones', 'Professional studio headphones with noise cancellation and premium sound quality.', 'Studio quality headphones', 'PAH-001', 299.99, 279.99, 30, 'instock', 'physical', 2, 'studio-headphones.jpg', 1, 1],
        ['Wireless Microphone System', 'wireless-microphone-system', 'Professional wireless microphone system for presentations and performances.', 'Wireless microphone kit', 'WMS-001', 199.99, 179.99, 20, 'instock', 'physical', 2, 'wireless-mic.jpg', 1, 1],
        ['Audio Interface Pro', 'audio-interface-pro', 'High-quality audio interface for professional recording and streaming.', 'Professional audio interface', 'AIP-001', 399.99, 349.99, 12, 'instock', 'physical', 2, 'audio-interface.jpg', 1, 1],
        
        // Mobile Accessories
        ['Wireless Charging Pad', 'wireless-charging-pad', 'Fast wireless charging pad compatible with all Qi-enabled devices.', 'Fast wireless charger', 'WCP-001', 49.99, 39.99, 75, 'instock', 'physical', 3, 'wireless-charger.jpg', 1, 1],
        ['Bluetooth Car Kit', 'bluetooth-car-kit', 'Hands-free Bluetooth car kit with voice commands and music streaming.', 'Car Bluetooth adapter', 'BCK-001', 89.99, 79.99, 40, 'instock', 'physical', 3, 'car-bluetooth.jpg', 1, 1],
        ['Phone Case Pro', 'phone-case-pro', 'Protective phone case with built-in wallet and card storage.', 'Protective phone case', 'PCP-001', 34.99, 29.99, 100, 'instock', 'physical', 3, 'phone-case.jpg', 1, 1],
        
        // Wearable Technology
        ['SmartWatch Fitness', 'smartwatch-fitness', 'Fitness-focused smartwatch with heart rate monitoring and GPS tracking.', 'Fitness smartwatch', 'SWF-001', 199.99, 179.99, 35, 'instock', 'physical', 4, 'fitness-watch.jpg', 1, 1],
        ['Sleep Tracker Ring', 'sleep-tracker-ring', 'Discrete sleep tracking ring that monitors your sleep patterns and quality.', 'Sleep tracking device', 'STR-001', 149.99, 129.99, 25, 'instock', 'physical', 4, 'sleep-ring.jpg', 1, 1],
        
        // Gaming Accessories
        ['Mechanical Gaming Keyboard', 'mechanical-gaming-keyboard', 'RGB mechanical gaming keyboard with customizable lighting and tactile switches.', 'Gaming mechanical keyboard', 'MGK-001', 129.99, 119.99, 45, 'instock', 'physical', 5, 'gaming-keyboard.jpg', 1, 1],
        ['Gaming Mouse Pro', 'gaming-mouse-pro', 'High-precision gaming mouse with customizable buttons and RGB lighting.', 'Professional gaming mouse', 'GMP-001', 79.99, 69.99, 60, 'instock', 'physical', 5, 'gaming-mouse.jpg', 1, 1],
        
        // Digital Products
        ['Productivity App Suite', 'productivity-app-suite', 'Complete suite of productivity apps for task management, time tracking, and collaboration.', 'Digital productivity suite', 'PAS-001', 49.99, 39.99, 999, 'instock', 'digital', 6, '', 1, 1],
        ['Website Template Pack', 'website-template-pack', 'Professional website templates for businesses and personal use.', 'Website template collection', 'WTP-001', 99.99, 79.99, 999, 'instock', 'digital', 6, '', 1, 1]
    ];
    
    foreach ($products as $product) {
        $stmt = $conn->prepare("INSERT IGNORE INTO products (name, slug, description, short_description, sku, price, sale_price, stock_quantity, stock_status, product_type, category_id, featured_image, is_featured, is_published) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssddissisii", $product[0], $product[1], $product[2], $product[3], $product[4], $product[5], $product[6], $product[7], $product[8], $product[9], $product[10], $product[11], $product[12], $product[13]);
        $stmt->execute();
        $stmt->close();
        echo "<p>✅ Created product: {$product[0]}</p>";
    }
    
    echo "<h3>Step 4: Creating Sample Testimonials</h3>";
    
    // Sample testimonials
    $testimonials = [
        ['Sarah Johnson', 'sarah@example.com', 'TechStart Inc.', 'CEO', 5, 'SmartProZen has revolutionized our office setup. The smart home devices we purchased have increased our productivity by 40%. The customer service is exceptional!', '', 1, 1],
        ['Mike Chen', 'mike@example.com', 'Audio Productions', 'Sound Engineer', 5, 'The professional audio equipment from SmartProZen is top-notch. The wireless microphone system has become essential for our studio recordings. Highly recommend!', '', 1, 1],
        ['Emily Rodriguez', 'emily@example.com', 'Fitness Coach', 'Personal Trainer', 5, 'My SmartWatch Fitness has been a game-changer for tracking my clients\' workouts. The heart rate monitoring is incredibly accurate and the battery life is impressive.', '', 1, 1],
        ['David Thompson', 'david@example.com', 'Gaming Studio', 'Game Developer', 5, 'The gaming accessories from SmartProZen have elevated our development setup. The mechanical keyboard and gaming mouse are perfect for long coding sessions.', '', 1, 1],
        ['Lisa Wang', 'lisa@example.com', 'Digital Agency', 'Creative Director', 5, 'We\'ve been using SmartProZen\'s digital products for our client projects. The website template pack saved us countless hours and the quality is outstanding.', '', 1, 1],
        ['James Wilson', 'james@example.com', 'Home Automation', 'Systems Integrator', 5, 'SmartProZen\'s smart home devices are reliable and easy to install. The SmartHome Hub Pro has been the centerpiece of many successful home automation projects.', '', 1, 1],
        ['Maria Garcia', 'maria@example.com', 'Mobile Tech', 'App Developer', 5, 'The mobile accessories from SmartProZen are well-designed and durable. The wireless charging pad and phone case have been daily essentials for my team.', '', 1, 1],
        ['Alex Kim', 'alex@example.com', 'Wearable Tech', 'Product Manager', 5, 'SmartProZen\'s wearable technology products are innovative and user-friendly. The sleep tracker ring provides valuable insights for our health monitoring projects.', '', 1, 1]
    ];
    
    foreach ($testimonials as $testimonial) {
        $stmt = $conn->prepare("INSERT IGNORE INTO testimonials (name, email, company, position, rating, testimonial, avatar, is_featured, is_published) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssisssi", $testimonial[0], $testimonial[1], $testimonial[2], $testimonial[3], $testimonial[4], $testimonial[5], $testimonial[6], $testimonial[7], $testimonial[8]);
        $stmt->execute();
        $stmt->close();
        echo "<p>✅ Created testimonial: {$testimonial[0]} from {$testimonial[2]}</p>";
    }
    
    echo "<h3>Step 5: Creating Sample Menus</h3>";
    
    // Sample main menu
    $main_menu = [
        [
            'title' => 'Home',
            'url' => '/',
            'children' => []
        ],
        [
            'title' => 'Products',
            'url' => '/products_list.php',
            'children' => [
                ['title' => 'Smart Home', 'url' => '/products_list.php?category=smart-home'],
                ['title' => 'Audio Equipment', 'url' => '/products_list.php?category=professional-audio'],
                ['title' => 'Mobile Accessories', 'url' => '/products_list.php?category=mobile-accessories'],
                ['title' => 'Wearable Tech', 'url' => '/products_list.php?category=wearable-tech'],
                ['title' => 'Gaming', 'url' => '/products_list.php?category=gaming-accessories'],
                ['title' => 'Digital Products', 'url' => '/products_list.php?category=digital-products']
            ]
        ],
        [
            'title' => 'About',
            'url' => '/about',
            'children' => []
        ],
        [
            'title' => 'Services',
            'url' => '/services',
            'children' => []
        ],
        [
            'title' => 'Support',
            'url' => '/support',
            'children' => []
        ],
        [
            'title' => 'Contact',
            'url' => '/contact.php',
            'children' => []
        ]
    ];
    
    $main_menu_json = json_encode($main_menu);
    $stmt = $conn->prepare("INSERT IGNORE INTO menus (name, location, menu_items, is_active) VALUES ('Main Menu', 'header', ?, 1)");
    $stmt->bind_param("s", $main_menu_json);
    $stmt->execute();
    $stmt->close();
    echo "<p>✅ Created main navigation menu</p>";
    
    // Sample footer menu
    $footer_menu = [
        ['title' => 'About Us', 'url' => '/about'],
        ['title' => 'Contact', 'url' => '/contact.php'],
        ['title' => 'Privacy Policy', 'url' => '/privacy-policy'],
        ['title' => 'Terms of Service', 'url' => '/terms-of-service'],
        ['title' => 'Support', 'url' => '/support'],
        ['title' => 'Shipping Info', 'url' => '/shipping-info'],
        ['title' => 'Returns', 'url' => '/returns'],
        ['title' => 'FAQ', 'url' => '/faq']
    ];
    
    $footer_menu_json = json_encode($footer_menu);
    $stmt = $conn->prepare("INSERT IGNORE INTO menus (name, location, menu_items, is_active) VALUES ('Footer Menu', 'footer', ?, 1)");
    $stmt->bind_param("s", $footer_menu_json);
    $stmt->execute();
    $stmt->close();
    echo "<p>✅ Created footer menu</p>";
    
    echo "<h3>Step 6: Creating Sample Coupons</h3>";
    
    // Sample coupons
    $coupons = [
        ['WELCOME10', 'Welcome Discount', 'Get 10% off your first order', 'percentage', 10, null, null, '2025-12-31', 100, 0, 1],
        ['SAVE20', 'Flash Sale', 'Save $20 on orders over $100', 'fixed', null, 20, 100, '2025-12-31', 50, 0, 1],
        ['STUDENT15', 'Student Discount', '15% off for students with valid ID', 'percentage', 15, null, null, '2025-12-31', 25, 0, 1],
        ['BULK25', 'Bulk Purchase', '25% off orders of 5 or more items', 'percentage', 25, null, null, '2025-12-31', 30, 0, 1]
    ];
    
    foreach ($coupons as $coupon) {
        $stmt = $conn->prepare("INSERT IGNORE INTO coupons (code, description, discount_type, discount_value, minimum_amount, maximum_discount, usage_limit, valid_until, used_count, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdddssii", $coupon[0], $coupon[1], $coupon[2], $coupon[3], $coupon[4], $coupon[5], $coupon[6], $coupon[7], $coupon[8], $coupon[9]);
        $stmt->execute();
        $stmt->close();
        echo "<p>✅ Created coupon: {$coupon[0]} - {$coupon[1]}</p>";
    }
    
    echo "<h3>Step 7: Creating Sample Reviews</h3>";
    
    // Sample reviews for products
    $reviews = [
        [1, 1, 5, 'Excellent smart hub! Easy to set up and works perfectly with all my devices.', 1],
        [2, 2, 5, 'Great audio quality. These headphones are perfect for professional work.', 1],
        [3, 3, 4, 'Good wireless charging pad. Charges my phone quickly and looks great on my desk.', 1],
        [4, 4, 5, 'Love this smartwatch! The fitness tracking features are incredibly accurate.', 1],
        [5, 5, 5, 'Best gaming keyboard I have ever used. The RGB lighting is amazing!', 1]
    ];
    
    foreach ($reviews as $review) {
        $stmt = $conn->prepare("INSERT IGNORE INTO reviews (product_id, user_id, rating, comment, is_approved) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiisi", $review[0], $review[1], $review[2], $review[3], $review[4]);
        $stmt->execute();
        $stmt->close();
    }
    echo "<p>✅ Created sample product reviews</p>";
    
    echo "<h3>🎉 Template Bundle Installation Complete!</h3>";
    
    // Summary
    $result = $conn->query("SELECT COUNT(*) as count FROM pages WHERE is_published = 1");
    $page_count = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM products WHERE is_published = 1");
    $product_count = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM product_categories WHERE is_active = 1");
    $category_count = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM testimonials WHERE is_published = 1");
    $testimonial_count = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM coupons WHERE is_active = 1");
    $coupon_count = $result->fetch_assoc()['count'];
    
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4>✅ Installation Summary:</h4>";
    echo "<ul>";
    echo "<li><strong>$page_count Pages</strong> - About, Services, Support, Privacy Policy, Terms of Service</li>";
    echo "<li><strong>$product_count Products</strong> - Smart home, audio, mobile, wearable, gaming, digital products</li>";
    echo "<li><strong>$category_count Categories</strong> - Complete product categorization</li>";
    echo "<li><strong>$testimonial_count Testimonials</strong> - Customer reviews and feedback</li>";
    echo "<li><strong>$coupon_count Coupons</strong> - Welcome discounts and promotional codes</li>";
    echo "<li><strong>Navigation Menus</strong> - Header and footer menus with proper links</li>";
    echo "<li><strong>Product Reviews</strong> - Sample reviews for featured products</li>";
    echo "<li><strong>Page Sections</strong> - Hero, features, and content sections for pages</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div style='background: #cce5ff; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4>🔗 Test Your New Content:</h4>";
    echo "<p><a href='" . SITE_URL . "' target='_blank'>🏠 Homepage</a> - Should now show real content</p>";
    echo "<p><a href='" . SITE_URL . "/about' target='_blank'>📖 About Page</a> - Complete with sections</p>";
    echo "<p><a href='" . SITE_URL . "/products_list.php' target='_blank'>🛍️ Products</a> - Browse all categories</p>";
    echo "<p><a href='" . SITE_URL . "/contact.php' target='_blank'>📞 Contact</a> - Contact information</p>";
    echo "<p><a href='" . SITE_URL . "/admin/dashboard.php' target='_blank'>⚙️ Admin Panel</a> - Manage all content</p>";
    echo "</div>";
    
    echo "<div style='background: #fff3cd; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4>💡 What This Solves:</h4>";
    echo "<ul>";
    echo "<li>✅ <strong>No more empty messages</strong> - All sections now have real content</li>";
    echo "<li>✅ <strong>Professional appearance</strong> - Complete website with sample data</li>";
    echo "<li>✅ <strong>Demo-ready</strong> - Perfect for showcasing to clients</li>";
    echo "<li>✅ <strong>Admin-friendly</strong> - Easy to customize and manage</li>";
    echo "<li>✅ <strong>SEO-ready</strong> - Proper meta titles and descriptions</li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
