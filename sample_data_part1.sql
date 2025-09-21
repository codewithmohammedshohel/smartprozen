-- SmartProZen Sample Data - Part 1
-- Default roles, admin user, and settings

-- Insert default roles
INSERT INTO `roles` (`name`, `permissions`) VALUES
('Super Admin', '["all"]'),
('Admin', '["manage_products","manage_orders","manage_users","manage_pages","view_reports"]'),
('Manager', '["manage_products","manage_orders","view_reports"]'),
('Editor', '["manage_pages","manage_posts"]');

-- Insert admin user (password: admin123)
INSERT INTO `admin_users` (`username`, `email`, `password`, `full_name`, `role_id`) VALUES
('admin', 'admin@smartprozen.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 1);

-- Insert default settings
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `category`, `description`, `is_public`) VALUES
('site_name', 'SmartProZen', 'text', 'general', 'Website name', 1),
('site_tagline', 'Smart Tech, Simplified Living', 'text', 'general', 'Website tagline', 1),
('site_description', 'Discover our curated collection of smart gadgets and professional accessories designed to elevate your everyday.', 'textarea', 'general', 'Website description', 1),
('contact_email', 'info@smartprozen.com', 'text', 'contact', 'Contact email', 1),
('contact_phone', '+1 (555) 123-4567', 'text', 'contact', 'Contact phone', 1),
('contact_address', '123 Tech Street, Innovation City, IC 12345', 'textarea', 'contact', 'Physical address', 1),
('currency', 'USD', 'text', 'shop', 'Default currency', 1),
('currency_symbol', '$', 'text', 'shop', 'Currency symbol', 1),
('shipping_cost', '9.99', 'number', 'shop', 'Default shipping cost', 0),
('free_shipping_threshold', '50.00', 'number', 'shop', 'Free shipping threshold', 0),
('tax_rate', '8.5', 'number', 'shop', 'Tax rate percentage', 0),
('products_per_page', '12', 'number', 'shop', 'Products per page', 0),
('enable_reviews', '1', 'boolean', 'shop', 'Enable product reviews', 0),
('enable_wishlist', '1', 'boolean', 'shop', 'Enable wishlist', 0),
('enable_guest_checkout', '1', 'boolean', 'shop', 'Allow guest checkout', 0);
