-- SmartProZen Sample Data
-- This file contains sample data to populate the website with realistic content

-- Insert sample products
INSERT INTO `products` (`name`, `description`, `short_description`, `price`, `sale_price`, `sku`, `stock_quantity`, `is_digital`, `delivery_type`, `is_active`, `is_featured`, `image_filename`, `category_id`, `tags`, `meta_title`, `meta_description`, `weight`, `dimensions`, `created_at`, `updated_at`) VALUES
('SmartProZen Premium Theme', 'A complete WordPress theme with modern design, responsive layout, and built-in e-commerce functionality. Perfect for online stores and business websites.', 'Modern WordPress theme with e-commerce features', 89.99, 69.99, 'SPZ-THEME-001', 0, 1, 'automatic', 1, 1, 'theme-preview.jpg', 4, 'wordpress,theme,ecommerce,responsive', 'SmartProZen Premium Theme - Modern WordPress Theme', 'Get the SmartProZen Premium Theme with modern design and e-commerce features. Responsive layout perfect for online stores.', NULL, NULL, NOW(), NOW()),

('Digital Marketing Course', 'Complete digital marketing course covering SEO, social media marketing, email marketing, and paid advertising. Includes 50+ video lessons and practical exercises.', 'Complete digital marketing training course', 199.99, 149.99, 'SPZ-COURSE-001', 0, 1, 'automatic', 1, 1, 'digital-marketing-course.jpg', 2, 'marketing,seo,social-media,training', 'Digital Marketing Course - Complete Training', 'Master digital marketing with our comprehensive course covering SEO, social media, email marketing, and paid advertising.', NULL, NULL, NOW(), NOW()),

('Business Plan Template', 'Professional business plan template with financial projections, market analysis, and executive summary. Available in Word and PDF formats.', 'Professional business plan template', 49.99, 29.99, 'SPZ-TEMPLATE-001', 0, 1, 'automatic', 1, 0, 'business-plan-template.jpg', 4, 'business,plan,template,finance', 'Business Plan Template - Professional Format', 'Create professional business plans with our comprehensive template including financial projections and market analysis.', NULL, NULL, NOW(), NOW()),

('Logo Design Package', 'Custom logo design package including 3 initial concepts, 2 revisions, and final files in multiple formats (AI, EPS, PNG, JPG).', 'Custom logo design with multiple concepts', 299.99, 199.99, 'SPZ-DESIGN-001', 0, 1, 'manual', 1, 1, 'logo-design-package.jpg', 4, 'logo,design,branding,custom', 'Custom Logo Design Package - Professional Branding', 'Get a professional custom logo design with multiple concepts, revisions, and final files in all formats.', NULL, NULL, NOW(), NOW()),

('SEO Audit Tool', 'Comprehensive SEO audit tool that analyzes your website and provides detailed recommendations for improvement. Includes competitor analysis.', 'Professional SEO audit and analysis tool', 79.99, 59.99, 'SPZ-SEO-001', 0, 1, 'automatic', 1, 0, 'seo-audit-tool.jpg', 3, 'seo,audit,analysis,website', 'SEO Audit Tool - Website Analysis', 'Analyze your website SEO with our comprehensive audit tool. Get detailed recommendations and competitor insights.', NULL, NULL, NOW(), NOW()),

('Social Media Templates', 'Collection of 100+ social media post templates for Instagram, Facebook, Twitter, and LinkedIn. Includes Canva and Photoshop files.', '100+ social media post templates', 39.99, 24.99, 'SPZ-SOCIAL-001', 0, 1, 'automatic', 1, 0, 'social-media-templates.jpg', 4, 'social-media,templates,instagram,facebook', 'Social Media Templates - 100+ Designs', 'Boost your social media presence with 100+ professional templates for all major platforms.', NULL, NULL, NOW(), NOW()),

('Email Marketing Guide', 'Complete guide to email marketing including best practices, automation strategies, and campaign optimization. Includes templates and examples.', 'Complete email marketing guide and templates', 59.99, 39.99, 'SPZ-EMAIL-001', 0, 1, 'automatic', 1, 0, 'email-marketing-guide.jpg', 2, 'email,marketing,automation,guide', 'Email Marketing Guide - Complete Strategy', 'Master email marketing with our comprehensive guide including automation strategies and campaign optimization.', NULL, NULL, NOW(), NOW()),

('Web Development Course', 'Learn web development from scratch with HTML, CSS, JavaScript, PHP, and MySQL. Includes 80+ video lessons and practical projects.', 'Complete web development training course', 299.99, 199.99, 'SPZ-WEBDEV-001', 0, 1, 'automatic', 1, 1, 'web-development-course.jpg', 2, 'web-development,html,css,javascript,php', 'Web Development Course - Complete Training', 'Learn web development from scratch with our comprehensive course covering frontend and backend technologies.', NULL, NULL, NOW(), NOW()),

('Content Calendar Template', '12-month content calendar template with planning tools, content ideas, and scheduling features. Available in Excel and Google Sheets.', '12-month content planning calendar', 29.99, 19.99, 'SPZ-CALENDAR-001', 0, 1, 'automatic', 1, 0, 'content-calendar-template.jpg', 4, 'content,calendar,planning,social-media', 'Content Calendar Template - 12 Month Planning', 'Plan your content strategy with our comprehensive 12-month calendar template and planning tools.', NULL, NULL, NOW(), NOW()),

('Analytics Dashboard', 'Custom analytics dashboard for tracking website performance, user behavior, and conversion metrics. Includes Google Analytics integration.', 'Custom analytics dashboard and reporting', 149.99, 99.99, 'SPZ-ANALYTICS-001', 0, 1, 'manual', 1, 0, 'analytics-dashboard.jpg', 3, 'analytics,dashboard,reporting,google-analytics', 'Analytics Dashboard - Custom Reporting', 'Track your website performance with our custom analytics dashboard and comprehensive reporting tools.', NULL, NULL, NOW(), NOW());

-- Insert sample product categories
INSERT INTO `product_categories` (`name`, `description`, `slug`, `parent_id`, `image_filename`, `is_active`, `sort_order`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
('Digital Products', 'Digital downloads and software products', 'digital-products', NULL, NULL, 1, 1, 'Digital Products - Downloads & Software', 'Browse our collection of digital products including software, templates, and digital downloads.', NOW(), NOW()),
('E-books', 'Electronic books and publications', 'e-books', NULL, NULL, 1, 2, 'E-books - Digital Publications', 'Discover our collection of e-books covering various topics from business to technology.', NOW(), NOW()),
('Software', 'Software applications and tools', 'software', NULL, NULL, 1, 3, 'Software - Applications & Tools', 'Find software applications and tools to boost your productivity and business.', NOW(), NOW()),
('Templates', 'Design templates and themes', 'templates', NULL, NULL, 1, 4, 'Templates - Design & Themes', 'Professional design templates and themes for websites, presentations, and marketing materials.', NOW(), NOW()),
('Courses', 'Online courses and training materials', 'courses', NULL, NULL, 1, 5, 'Courses - Online Training', 'Comprehensive online courses and training materials to enhance your skills.', NOW(), NOW()),
('Marketing Tools', 'Digital marketing tools and resources', 'marketing-tools', NULL, NULL, 1, 6, 'Marketing Tools - Digital Resources', 'Professional marketing tools and resources to grow your business online.', NOW(), NOW());

-- Insert sample pages with sections
INSERT INTO `pages` (`title`, `content`, `template_slug`, `slug`, `created_at`, `updated_at`) VALUES
('Home', '{}', 'default_page', 'home', NOW(), NOW()),
('About Us', '<h2>About SmartProZen</h2><p>Welcome to SmartProZen, your ultimate destination for digital products and online shopping. We are committed to providing high-quality digital products and exceptional customer service.</p><h3>Our Mission</h3><p>To make digital products accessible to everyone while maintaining the highest standards of quality and customer satisfaction.</p><h3>Our Vision</h3><p>To become the leading platform for digital product sales, known for innovation, reliability, and customer excellence.</p><h3>Why Choose Us?</h3><ul><li>High-quality digital products</li><li>Instant download delivery</li><li>24/7 customer support</li><li>Money-back guarantee</li><li>Regular updates and new products</li></ul>', 'default_page', 'about-us', NOW(), NOW()),
('Services', '<h2>Our Services</h2><p>We offer a comprehensive range of digital services to help you succeed online.</p><h3>Digital Products</h3><p>Browse our extensive collection of digital products including themes, templates, courses, and software.</p><h3>Custom Development</h3><p>Need something custom? We provide bespoke development services for websites, applications, and digital solutions.</p><h3>Consulting</h3><p>Get expert advice on digital strategy, marketing, and technology implementation.</p>', 'default_page', 'services', NOW(), NOW()),
('Contact', '<h2>Contact Us</h2><p>Get in touch with us for any questions, support, or custom requests.</p><h3>Contact Information</h3><p><strong>Email:</strong> info@smartprozen.com<br><strong>Phone:</strong> +1-555-0123<br><strong>Address:</strong> 123 Digital Street, Tech City, TC 12345</p><h3>Business Hours</h3><p>Monday - Friday: 9:00 AM - 6:00 PM<br>Saturday: 10:00 AM - 4:00 PM<br>Sunday: Closed</p>', 'default_page', 'contact', NOW(), NOW()),
('Privacy Policy', '<h2>Privacy Policy</h2><p>Last updated: September 19, 2025</p><h3>Information We Collect</h3><p>We collect information you provide directly to us, such as when you create an account, make a purchase, or contact us for support.</p><h3>How We Use Your Information</h3><p>We use the information we collect to provide, maintain, and improve our services, process transactions, and communicate with you.</p><h3>Information Sharing</h3><p>We do not sell, trade, or otherwise transfer your personal information to third parties without your consent, except as described in this policy.</p><h3>Data Security</h3><p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p><h3>Contact Us</h3><p>If you have any questions about this Privacy Policy, please contact us.</p>', 'default_page', 'privacy-policy', NOW(), NOW()),
('Terms & Conditions', '<h2>Terms & Conditions</h2><p>Last updated: September 19, 2025</p><h3>Acceptance of Terms</h3><p>By accessing and using this website, you accept and agree to be bound by the terms and provision of this agreement.</p><h3>Use License</h3><p>Permission is granted to temporarily download one copy of the materials on SmartProZen for personal, non-commercial transitory viewing only.</p><h3>Disclaimer</h3><p>The materials on SmartProZen are provided on an \'as is\' basis. SmartProZen makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties.</p><h3>Limitations</h3><p>In no event shall SmartProZen or its suppliers be liable for any damages arising out of the use or inability to use the materials on SmartProZen.</p><h3>Accuracy of Materials</h3><p>The materials appearing on SmartProZen could include technical, typographical, or photographic errors. SmartProZen does not warrant that any of the materials on its website are accurate, complete, or current.</p><h3>Modifications</h3><p>SmartProZen may revise these terms of service for its website at any time without notice.</p>', 'default_page', 'terms-conditions', NOW(), NOW());

-- Insert sample page sections for home page
INSERT INTO `page_sections` (`page_id`, `section_template_id`, `section_type`, `content_json`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 2, 'hero', '{"title": {"en": "Welcome to SmartProZen!"}, "subtitle": {"en": "Your ultimate destination for digital products and online shopping."}, "image_id": "", "image_filename": "", "button_text": {"en": "Shop Now"}, "button_link": "/products_list.php"}', 0, NOW(), NOW()),
(1, 1, 'rich_text', '{"text": {"en": "<h2>About Us</h2><p>SmartProZen is dedicated to providing you with the best online shopping experience. We offer a wide range of digital products to meet your needs.</p><p>From premium themes and templates to comprehensive courses and software, we have everything you need to succeed online.</p>"}}', 1, NOW(), NOW()),
(1, 3, 'featured_products', '{"product_ids": [1, 2, 4, 8]}', 2, NOW(), NOW()),
(1, 5, 'features', '{"title": {"en": "Why Choose SmartProZen?"}, "subtitle": {"en": "We provide the best digital products and services."}, "features": [{"icon": "bi bi-lightning-charge", "title": {"en": "Instant Download"}, "description": {"en": "Get your digital products instantly after purchase."}}, {"icon": "bi bi-shield-check", "title": {"en": "Secure Platform"}, "description": {"en": "Your data is protected with our industry-leading security."}}, {"icon": "bi bi-headset", "title": {"en": "24/7 Support"}, "description": {"en": "Our dedicated support team is always here to help."}}]}', 3, NOW(), NOW()),
(1, 4, 'faq', '{"items": [{"question": {"en": "How do I download my purchase?"}, "answer": {"en": "After completing your purchase, you will receive an email with download links. You can also access your downloads from your account dashboard."}}, {"question": {"en": "What file formats do you provide?"}, "answer": {"en": "We provide files in various formats including ZIP, PDF, DOC, PSD, AI, and more depending on the product."}}, {"question": {"en": "Do you offer refunds?"}, "answer": {"en": "Yes, we offer a 30-day money-back guarantee for all digital products."}}, {"question": {"en": "Can I use these products commercially?"}, "answer": {"en": "Most of our products come with commercial licenses. Please check the product description for specific licensing terms."}}]}', 4, NOW(), NOW());

-- Insert sample testimonials
INSERT INTO `testimonials` (`author_name`, `author_title`, `testimonial_text`, `rating`, `is_approved`, `created_at`, `updated_at`) VALUES
('Sarah Johnson', 'Web Designer', 'SmartProZen has been a game-changer for my business. The quality of their digital products is outstanding, and the customer support is exceptional.', 5, 1, NOW(), NOW()),
('Michael Chen', 'Digital Marketer', 'I\'ve purchased several courses from SmartProZen and they have significantly improved my skills. The content is comprehensive and well-structured.', 5, 1, NOW(), NOW()),
('Emily Rodriguez', 'Small Business Owner', 'The templates and themes from SmartProZen helped me create a professional website quickly. Highly recommended for anyone starting an online business.', 5, 1, NOW(), NOW()),
('David Thompson', 'Freelancer', 'Great selection of digital products at reasonable prices. The instant download feature is very convenient and the quality is top-notch.', 4, 1, NOW(), NOW()),
('Lisa Wang', 'Marketing Manager', 'SmartProZen provides excellent value for money. Their digital marketing tools have helped me improve my campaigns significantly.', 5, 1, NOW(), NOW());

-- Insert sample posts
INSERT INTO `posts` (`author_id`, `title`, `content`, `slug`, `created_at`, `updated_at`) VALUES
(1, '10 Essential Digital Marketing Tools for 2025', 'Digital marketing is constantly evolving, and staying ahead requires the right tools. Here are 10 essential digital marketing tools that every marketer should have in their arsenal for 2025...', 'essential-digital-marketing-tools-2025', NOW(), NOW()),
(1, 'How to Choose the Perfect WordPress Theme', 'Choosing the right WordPress theme is crucial for your website\'s success. Here\'s a comprehensive guide to help you select the perfect theme for your needs...', 'choose-perfect-wordpress-theme', NOW(), NOW()),
(1, 'Complete Guide to E-commerce SEO', 'E-commerce SEO is different from regular SEO. Learn the specific strategies and techniques to optimize your online store for search engines...', 'complete-guide-ecommerce-seo', NOW(), NOW()),
(1, 'Building a Successful Online Course', 'Creating and selling online courses can be a lucrative business. Here\'s everything you need to know about building a successful online course...', 'building-successful-online-course', NOW(), NOW()),
(1, 'Web Design Trends for 2025', 'Stay ahead of the curve with these emerging web design trends for 2025. From dark mode to micro-interactions, discover what\'s shaping the future of web design...', 'web-design-trends-2025', NOW(), NOW());

-- Update menu with more items
UPDATE `menus` SET `items` = '[{"label":"Home","url":"/"},{"label":"Products","url":"products_list.php"},{"label":"Categories","url":"products_list.php?category=digital-products"},{"label":"About","url":"page.php?slug=about-us"},{"label":"Services","url":"page.php?slug=services"},{"label":"Blog","url":"post.php"},{"label":"Contact","url":"contact.php"}]' WHERE `name` = 'main-menu';

-- Insert sample coupons
INSERT INTO `coupons` (`code`, `discount_type`, `discount_value`, `expiry_date`, `usage_limit`, `used_count`, `created_at`, `updated_at`) VALUES
('WELCOME10', 'percentage', 10.00, '2025-12-31', 100, 0, NOW(), NOW()),
('SAVE20', 'percentage', 20.00, '2025-12-31', 50, 0, NOW(), NOW()),
('FIRST50', 'fixed', 50.00, '2025-12-31', 25, 0, NOW(), NOW()),
('STUDENT15', 'percentage', 15.00, '2025-12-31', 200, 0, NOW(), NOW());

-- Insert sample contact messages
INSERT INTO `contact_messages` (`name`, `email`, `subject`, `message`, `sent_at`, `is_read`) VALUES
('John Smith', 'john.smith@email.com', 'Product Inquiry', 'Hi, I\'m interested in your digital marketing course. Can you provide more details about the curriculum?', NOW(), 0),
('Maria Garcia', 'maria.garcia@email.com', 'Custom Development', 'I need a custom website for my business. Do you offer development services?', NOW(), 0),
('Alex Johnson', 'alex.johnson@email.com', 'Support Request', 'I\'m having trouble downloading my purchase. Can you help me resolve this issue?', NOW(), 0);

-- Update settings with more comprehensive values
UPDATE `settings` SET `setting_value` = 'SmartProZen - Digital Products & E-commerce Platform' WHERE `setting_key` = 'site_name';
UPDATE `settings` SET `setting_value` = 'Your ultimate destination for digital products, themes, courses, and software. Quality products with instant delivery and 24/7 support.' WHERE `setting_key` = 'site_description';
UPDATE `settings` SET `setting_value` = 'SmartProZen Digital Solutions' WHERE `setting_key` = 'business_name';
UPDATE `settings` SET `setting_value` = 'info@smartprozen.com' WHERE `setting_key` = 'business_email';
UPDATE `settings` SET `setting_value` = '+1-555-0123' WHERE `setting_key` = 'business_phone';
UPDATE `settings` SET `setting_value` = '123 Digital Street, Tech City, TC 12345' WHERE `setting_key` = 'business_address';
UPDATE `settings` SET `setting_value` = 'digital products, e-commerce, themes, templates, courses, software, online shopping, instant download' WHERE `setting_key` = 'meta_keywords';
UPDATE `settings` SET `setting_value` = 'orders@smartprozen.com' WHERE `setting_key` = 'order_notification_email';
UPDATE `settings` SET `setting_value` = 'support@smartprozen.com' WHERE `setting_key` = 'support_email';

-- Insert additional section templates
INSERT INTO `section_templates` (`name`, `template_type`, `default_content_json`, `thumbnail_image`, `created_at`, `updated_at`) VALUES
('Pricing Section', 'pricing', '{"title": {"en": "Choose Your Plan"}, "subtitle": {"en": "Select the perfect plan for your needs"}, "plans": [{"name": {"en": "Basic"}, "price": "29", "currency": "$", "period": "month", "features": [{"en": "5 Products"}, {"en": "Basic Support"}, {"en": "Email Templates"}], "button_text": {"en": "Get Started"}, "button_link": "#"}, {"name": {"en": "Pro"}, "price": "59", "currency": "$", "period": "month", "features": [{"en": "Unlimited Products"}, {"en": "Priority Support"}, {"en": "Advanced Templates"}, {"en": "Analytics"}], "button_text": {"en": "Get Started"}, "button_link": "#", "featured": true}, {"name": {"en": "Enterprise"}, "price": "99", "currency": "$", "period": "month", "features": [{"en": "Everything in Pro"}, {"en": "Custom Development"}, {"en": "Dedicated Support"}, {"en": "White Label"}], "button_text": {"en": "Contact Sales"}, "button_link": "#"}]}', NULL, NOW(), NOW()),
('Product Showcase', 'product_showcase', '{"title": {"en": "Featured Products"}, "subtitle": {"en": "Discover our most popular digital products"}, "product_ids": [], "layout": "grid", "show_prices": true, "show_ratings": true}', NULL, NOW(), NOW()),
('Testimonials Carousel', 'testimonials', '{"title": {"en": "What Our Customers Say"}, "subtitle": {"en": "Read testimonials from our satisfied customers"}, "testimonials": [], "layout": "carousel", "show_ratings": true}', NULL, NOW(), NOW());

-- Insert sample product reviews
INSERT INTO `product_reviews` (`product_id`, `user_id`, `rating`, `comment`, `reviewer_name`, `status`, `created_at`) VALUES
(1, NULL, 5, 'Excellent theme! Very well designed and easy to customize. Highly recommended!', 'Sarah M.', 'approved', NOW()),
(1, NULL, 4, 'Great theme with good documentation. Minor issues with mobile responsiveness but overall satisfied.', 'Mike R.', 'approved', NOW()),
(2, NULL, 5, 'Comprehensive course with practical examples. Worth every penny!', 'Jennifer L.', 'approved', NOW()),
(2, NULL, 5, 'Best digital marketing course I\'ve taken. The instructor explains everything clearly.', 'David K.', 'approved', NOW()),
(4, NULL, 5, 'Amazing logo design service. Professional and creative designs delivered on time.', 'Lisa P.', 'approved', NOW()),
(8, NULL, 4, 'Good web development course. Covers all the basics and more advanced topics.', 'Tom W.', 'approved', NOW());

-- Insert sample wishlist items (assuming user_id 1 exists)
INSERT INTO `wishlist` (`user_id`, `product_id`, `created_at`) VALUES
(1, 1, NOW()),
(1, 2, NOW()),
(1, 4, NOW()),
(1, 8, NOW());

-- Insert sample subscribers
INSERT INTO `subscribers` (`email`, `subscribed_at`) VALUES
('subscriber1@email.com', NOW()),
('subscriber2@email.com', NOW()),
('subscriber3@email.com', NOW()),
('subscriber4@email.com', NOW()),
('subscriber5@email.com', NOW());

-- Insert sample SEO metadata for products
INSERT INTO `seo_metadata` (`entity_type`, `entity_id`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
('product', 1, 'SmartProZen Premium Theme - Modern WordPress Theme', 'Get the SmartProZen Premium Theme with modern design and e-commerce features. Responsive layout perfect for online stores.', NOW(), NOW()),
('product', 2, 'Digital Marketing Course - Complete Training', 'Master digital marketing with our comprehensive course covering SEO, social media, email marketing, and paid advertising.', NOW(), NOW()),
('product', 4, 'Custom Logo Design Package - Professional Branding', 'Get a professional custom logo design with multiple concepts, revisions, and final files in all formats.', NOW(), NOW()),
('product', 8, 'Web Development Course - Complete Training', 'Learn web development from scratch with our comprehensive course covering frontend and backend technologies.', NOW(), NOW());

-- Insert sample email templates
INSERT INTO `email_templates` (`name`, `subject`, `body`, `created_at`, `updated_at`) VALUES
('Welcome Email', 'Welcome to SmartProZen!', 'Dear {{user_name}},\n\nWelcome to SmartProZen! Thank you for joining our community.\n\nWe\'re excited to have you on board and look forward to providing you with amazing digital products.\n\nBest regards,\nSmartProZen Team', NOW(), NOW()),
('Product Download', 'Your Download is Ready', 'Dear {{user_name}},\n\nThank you for your purchase! Your digital product is ready for download.\n\nProduct: {{product_name}}\nDownload Link: {{download_link}}\n\nIf you have any questions, please don\'t hesitate to contact our support team.\n\nBest regards,\nSmartProZen Team', NOW(), NOW()),
('Newsletter', 'SmartProZen Newsletter - {{date}}', 'Dear {{user_name}},\n\nHere\'s your weekly update from SmartProZen!\n\n{{newsletter_content}}\n\nThank you for being part of our community.\n\nBest regards,\nSmartProZen Team', NOW(), NOW());

-- Insert sample activity logs
INSERT INTO `activity_logs` (`user_type`, `user_id`, `action`, `details`, `ip_address`, `timestamp`) VALUES
('admin', 1, 'product_create', 'Created sample product: SmartProZen Premium Theme', '127.0.0.1', NOW()),
('admin', 1, 'product_create', 'Created sample product: Digital Marketing Course', '127.0.0.1', NOW()),
('admin', 1, 'page_create', 'Created sample page: About Us', '127.0.0.1', NOW()),
('admin', 1, 'page_create', 'Created sample page: Services', '127.0.0.1', NOW()),
('admin', 1, 'testimonial_approve', 'Approved testimonial from Sarah Johnson', '127.0.0.1', NOW()),
('admin', 1, 'coupon_create', 'Created coupon: WELCOME10', '127.0.0.1', NOW()),
('user', 1, 'user_register', 'New user registration: John Doe', '127.0.0.1', NOW()),
('user', 1, 'product_purchase', 'Purchased product: SmartProZen Premium Theme', '127.0.0.1', NOW()),
('user', 1, 'wishlist_add', 'Added product to wishlist: Digital Marketing Course', '127.0.0.1', NOW()),
('user', 1, 'review_submit', 'Submitted review for SmartProZen Premium Theme', '127.0.0.1', NOW());
