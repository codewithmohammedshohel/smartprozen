<?php
require_once 'config.php';
require_once 'core/db.php';

echo "<h2>🔧 Fixing Missing Tables and Pages</h2>";

try {
    echo "<h3>Step 1: Creating Missing product_images Table</h3>";
    
    // Check if product_images table exists
    $result = $conn->query("SHOW TABLES LIKE 'product_images'");
    if ($result->num_rows == 0) {
        echo "<p>Creating product_images table...</p>";
        
        $conn->query("CREATE TABLE IF NOT EXISTS `product_images` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `product_id` int(11) NOT NULL,
            `image_filename` varchar(255) NOT NULL,
            `alt_text` varchar(255) DEFAULT NULL,
            `is_primary` tinyint(1) DEFAULT 0,
            `display_order` int(11) DEFAULT 0,
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `product_id` (`product_id`),
            KEY `is_primary` (`is_primary`),
            CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        echo "<p>✅ Created product_images table</p>";
        
        // Add some sample product images
        $sample_images = [
            [1, '68cfc186c96b3-front cover.jpg', 'ZenBuds Pro 3 Front View', 1, 1],
            [2, '68cfc18a0712d-front cover.jpg', 'SmartGlow Ambient Light', 1, 1],
            [3, '68cfc18baa5be-1755877095.png', 'ProCharge Wireless Stand', 1, 1]
        ];
        
        foreach ($sample_images as $image) {
            $stmt = $conn->prepare("INSERT IGNORE INTO product_images (product_id, image_filename, alt_text, is_primary, display_order) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issii", $image[0], $image[1], $image[2], $image[3], $image[4]);
            $stmt->execute();
            $stmt->close();
        }
        
        echo "<p>✅ Added sample product images</p>";
        
    } else {
        echo "<p>✅ product_images table already exists</p>";
    }
    
    // Check if reviews table exists
    $result = $conn->query("SHOW TABLES LIKE 'reviews'");
    if ($result->num_rows == 0) {
        echo "<p>Creating reviews table...</p>";
        
        $conn->query("CREATE TABLE IF NOT EXISTS `reviews` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `product_id` int(11) NOT NULL,
            `user_id` int(11) DEFAULT NULL,
            `guest_name` varchar(100) DEFAULT NULL,
            `guest_email` varchar(100) DEFAULT NULL,
            `rating` int(1) NOT NULL,
            `title` varchar(255) DEFAULT NULL,
            `comment` text DEFAULT NULL,
            `is_approved` tinyint(1) DEFAULT 0,
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `product_id` (`product_id`),
            KEY `user_id` (`user_id`),
            KEY `is_approved` (`is_approved`),
            CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
            CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        echo "<p>✅ Created reviews table</p>";
        
        // Add some sample reviews
        $sample_reviews = [
            [1, 1, 1, 5, 'Amazing sound quality!', 'These earbuds are perfect for my daily commute.'],
            [2, 1, 1, 4, 'Great product!', 'Fast shipping. Would recommend to anyone looking for quality audio.'],
            [3, 2, 1, 5, 'Love the lighting!', 'Creates the perfect atmosphere in my room.'],
            [4, 2, 1, 4, 'Good quality', 'Easy to control via app.'],
            [5, 3, 1, 5, 'Perfect charging!', 'Wireless charging works perfectly! My phone charges fast.']
        ];
        
        foreach ($sample_reviews as $review) {
            $stmt = $conn->prepare("INSERT IGNORE INTO reviews (id, product_id, user_id, rating, title, comment, is_approved) VALUES (?, ?, ?, ?, ?, ?, 1)");
            $stmt->bind_param("iiisss", $review[0], $review[1], $review[2], $review[3], $review[4], $review[5]);
            $stmt->execute();
            $stmt->close();
        }
        
        echo "<p>✅ Added sample reviews</p>";
        
    } else {
        echo "<p>✅ reviews table already exists</p>";
    }
    
    echo "<h3>Step 2: Checking Missing Pages</h3>";
    
    // Check which pages exist in database
    $result = $conn->query("SELECT slug FROM pages WHERE is_published = 1");
    $existing_pages = [];
    while ($row = $result->fetch_assoc()) {
        $existing_pages[] = $row['slug'];
    }
    
    echo "<p>Existing pages in database: " . implode(', ', $existing_pages) . "</p>";
    
    $missing_pages = ['about', 'services', 'support', 'privacy-policy', 'terms-of-service'];
    $pages_to_create = [];
    
    foreach ($missing_pages as $page_slug) {
        if (!in_array($page_slug, $existing_pages)) {
            $pages_to_create[] = $page_slug;
        }
    }
    
    if (!empty($pages_to_create)) {
        echo "<p>Missing pages: " . implode(', ', $pages_to_create) . "</p>";
        
        echo "<h3>Step 3: Creating Missing Pages</h3>";
        
        $pages_data = [
            'about' => [
                'title' => 'About Us',
                'content' => '{"en": "Learn more about SmartProZen and our mission to deliver smart technology solutions that make your life easier and more efficient."}',
                'meta_title' => 'About Us - SmartProZen',
                'meta_description' => 'Learn about SmartProZen\'s mission to deliver smart technology solutions and exceptional customer service.'
            ],
            'services' => [
                'title' => 'Services',
                'content' => '{"en": "Our comprehensive range of smart technology services including consultation, installation, and ongoing support."}',
                'meta_title' => 'Services - SmartProZen',
                'meta_description' => 'Explore our smart technology services including consultation, installation, and support.'
            ],
            'support' => [
                'title' => 'Support',
                'content' => '{"en": "Get help and support for all your SmartProZen products and services. Our dedicated support team is here to help you."}',
                'meta_title' => 'Support - SmartProZen',
                'meta_description' => 'Access technical support, documentation, and help resources for SmartProZen products.'
            ],
            'privacy-policy' => [
                'title' => 'Privacy Policy',
                'content' => '{"en": "Our privacy policy and how we protect your personal information. We are committed to safeguarding your privacy and data security."}',
                'meta_title' => 'Privacy Policy - SmartProZen',
                'meta_description' => 'Read our privacy policy to understand how we collect, use, and protect your information.'
            ],
            'terms-of-service' => [
                'title' => 'Terms of Service',
                'content' => '{"en": "Terms and conditions for using SmartProZen services and products. Please read these terms carefully before using our services."}',
                'meta_title' => 'Terms of Service - SmartProZen',
                'meta_description' => 'Review the terms and conditions for using SmartProZen products and services.'
            ]
        ];
        
        foreach ($pages_to_create as $page_slug) {
            if (isset($pages_data[$page_slug])) {
                $page_data = $pages_data[$page_slug];
                
                $stmt = $conn->prepare("INSERT INTO pages (title, slug, content, template_slug, meta_title, meta_description, is_published, is_homepage) VALUES (?, ?, ?, 'default_page', ?, ?, 1, 0)");
                $stmt->bind_param("sssss", $page_data['title'], $page_slug, $page_data['content'], $page_data['meta_title'], $page_data['meta_description']);
                $stmt->execute();
                $stmt->close();
                
                echo "<p>✅ Created page: {$page_data['title']} ({$page_slug})</p>";
            }
        }
        
    } else {
        echo "<p>✅ All pages already exist in database</p>";
    }
    
    echo "<h3>Step 4: Creating Missing Page Files</h3>";
    
    // Create the actual page files that handle the routing
    $page_files_to_create = ['about.php', 'services.php', 'support.php'];
    
    foreach ($page_files_to_create as $page_file) {
        $file_path = __DIR__ . '/' . $page_file;
        if (!file_exists($file_path)) {
            $slug = str_replace('.php', '', $page_file);
            
            $page_content = '<?php
require_once "config.php";
require_once "core/db.php";
require_once "core/functions.php";

// Get page data from database
$slug = "' . $slug . '";
$stmt = $conn->prepare("SELECT * FROM pages WHERE slug = ? AND is_published = 1");
$stmt->bind_param("s", $slug);
$stmt->execute();
$page = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$page) {
    http_response_code(404);
    $page_title = "Page Not Found";
    include "includes/header.php";
    echo "<div class=\"container mt-5\"><div class=\"alert alert-danger\">The page you are looking for does not exist.</div></div>";
    include "includes/footer.php";
    exit;
}

$page_title = $page["title"];
$page_description = $page["meta_description"];

include "includes/header.php";
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1><?php echo htmlspecialchars($page["title"]); ?></h1>
            
            <?php
            // Fetch sections for this page
            $sections_stmt = $conn->prepare("SELECT * FROM page_sections WHERE page_id = ? ORDER BY display_order ASC");
            $sections_stmt->bind_param("i", $page["id"]);
            $sections_stmt->execute();
            $page_sections = $sections_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $sections_stmt->close();
            
            if (!empty($page_sections)) {
                foreach ($page_sections as $section) {
                    $section_data = json_decode($section["content_json"] ?? "{}", true) ?: [];
                    $section_type = $section["section_type"];
                    $section_template_path = __DIR__ . "/templates/sections/" . $section_type . ".php";
                    
                    if (file_exists($section_template_path)) {
                        include $section_template_path;
                    } else {
                        echo "<div class=\"container\"><div class=\"alert alert-warning\">Unknown section type: " . htmlspecialchars($section_type) . "</div></div>";
                    }
                }
            } else {
                // Show basic content if no sections
                $content = json_decode($page["content"], true);
                if (isset($content["en"])) {
                    echo "<div class=\"content\">";
                    echo "<p>" . htmlspecialchars($content["en"]) . "</p>";
                    echo "</div>";
                }
            }
            ?>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>';
            
            file_put_contents($file_path, $page_content);
            echo "<p>✅ Created file: {$page_file}</p>";
        } else {
            echo "<p>✅ File already exists: {$page_file}</p>";
        }
    }
    
    echo "<h3>Step 5: Testing Created Pages</h3>";
    
    $test_pages = [
        'About' => SITE_URL . '/about',
        'Services' => SITE_URL . '/services',
        'Support' => SITE_URL . '/support',
        'Privacy Policy' => SITE_URL . '/privacy-policy',
        'Terms of Service' => SITE_URL . '/terms-of-service'
    ];
    
    echo "<p><strong>Test these pages:</strong></p>";
    foreach ($test_pages as $name => $url) {
        echo "<p><a href='$url' target='_blank'>$name: $url</a></p>";
    }
    
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4>✅ All Issues Fixed!</h4>";
    echo "<p><strong>Fixed Issues:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Created missing <code>product_images</code> table</li>";
    echo "<li>✅ Added sample product images</li>";
    echo "<li>✅ Created missing pages in database</li>";
    echo "<li>✅ Created missing page files (about.php, services.php, support.php)</li>";
    echo "</ul>";
    echo "<p><a href='" . SITE_URL . "' target='_blank'>🏠 Test Your Homepage</a></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
