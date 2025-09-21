-- Recommended Indexes for SmartProzen Database

-- Users Table
ALTER TABLE `users` ADD INDEX `idx_users_email` (`email`);

-- Products Table
ALTER TABLE `products` ADD INDEX `idx_products_name` (`name`);
-- Assuming 'category_id' might be used for filtering/joining
-- ALTER TABLE `products` ADD INDEX `idx_products_category_id` (`category_id`);

-- Orders Table
ALTER TABLE `orders` ADD INDEX `idx_orders_user_id` (`user_id`);
ALTER TABLE `orders` ADD INDEX `idx_orders_status` (`status`);
ALTER TABLE `orders` ADD INDEX `idx_orders_created_at` (`created_at`);

-- Posts Table
ALTER TABLE `posts` ADD INDEX `idx_posts_slug` (`slug`);
ALTER TABLE `posts` ADD INDEX `idx_posts_created_at` (`created_at`);

-- Pages Table
ALTER TABLE `pages` ADD INDEX `idx_pages_slug` (`slug`);

-- Activity Logs Table
ALTER TABLE `activity_logs` ADD INDEX `idx_activity_logs_timestamp` (`timestamp`);

-- Reviews Table
ALTER TABLE `reviews` ADD INDEX `idx_reviews_product_id` (`product_id`);
ALTER TABLE `reviews` ADD INDEX `idx_reviews_user_id` (`user_id`);
ALTER TABLE `reviews` ADD INDEX `idx_reviews_is_approved` (`is_approved`);

-- Wishlist Table
ALTER TABLE `wishlist` ADD INDEX `idx_wishlist_user_id` (`user_id`);
ALTER TABLE `wishlist` ADD INDEX `idx_wishlist_product_id` (`product_id`);

-- Coupons Table
ALTER TABLE `coupons` ADD INDEX `idx_coupons_code` (`code`);
ALTER TABLE `coupons` ADD INDEX `idx_coupons_expires_at` (`expires_at`);
ALTER TABLE `coupons` ADD INDEX `idx_coupons_is_active` (`is_active`);

-- Admin Users Table
ALTER TABLE `admin_users` ADD INDEX `idx_admin_users_username` (`username`);
ALTER TABLE `admin_users` ADD INDEX `idx_admin_users_email` (`email`);

-- Page Sections Table
ALTER TABLE `page_sections` ADD INDEX `idx_page_sections_page_id` (`page_id`);
ALTER TABLE `page_sections` ADD INDEX `idx_page_sections_display_order` (`display_order`);

-- Menu Items Table
ALTER TABLE `menu_items` ADD INDEX `idx_menu_items_display_order` (`display_order`);

-- Password Resets Table
ALTER TABLE `password_resets` ADD INDEX `idx_password_resets_token` (`token`);
ALTER TABLE `password_resets` ADD INDEX `idx_password_resets_created_at` (`created_at`);
