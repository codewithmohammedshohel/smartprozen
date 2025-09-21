-- SmartProZen Sample Data - Part 2
-- Menus, categories, and products

-- Insert main navigation menu
INSERT INTO `menus` (`name`, `location`, `menu_items`, `is_active`) VALUES
('Main Navigation', 'header', '[
  {"title": "Home", "url": "/", "type": "page"},
  {"title": "Products", "url": "/products", "type": "page", "children": [
    {"title": "Smart Home", "url": "/category/smart-home", "type": "category"},
    {"title": "Audio", "url": "/category/audio", "type": "category"},
    {"title": "Mobile Accessories", "url": "/category/mobile-accessories", "type": "category"},
    {"title": "Wearables", "url": "/category/wearables", "type": "category"}
  ]},
  {"title": "About", "url": "/about", "type": "page"},
  {"title": "Contact", "url": "/contact", "type": "page"},
  {"title": "Blog", "url": "/blog", "type": "page"}
]', 1);

-- Insert footer menu
INSERT INTO `menus` (`name`, `location`, `menu_items`, `is_active`) VALUES
('Footer Menu', 'footer', '[
  {"title": "About Us", "url": "/about", "type": "page"},
  {"title": "Contact", "url": "/contact", "type": "page"},
  {"title": "Shipping Info", "url": "/shipping", "type": "page"},
  {"title": "Returns", "url": "/returns", "type": "page"},
  {"title": "Privacy Policy", "url": "/privacy", "type": "page"},
  {"title": "Terms of Service", "url": "/terms", "type": "page"}
]', 1);

-- Insert product categories
INSERT INTO `product_categories` (`name`, `slug`, `description`, `image`, `meta_title`, `meta_description`, `display_order`, `is_active`) VALUES
('Smart Home Devices', 'smart-home', 'Transform your home with intelligent devices that make life easier and more efficient.', '/uploads/categories/smart-home.jpg', 'Smart Home Devices | SmartProZen', 'Discover our range of smart home devices including lights, security systems, and automation tools.', 1, 1),
('Professional Audio', 'audio', 'Premium audio equipment for professionals and enthusiasts who demand the best sound quality.', '/uploads/categories/audio.jpg', 'Professional Audio Equipment | SmartProZen', 'High-quality audio equipment including headphones, speakers, and recording gear.', 2, 1),
('Mobile Accessories', 'mobile-accessories', 'Essential accessories for your mobile devices to enhance functionality and protection.', '/uploads/categories/mobile-accessories.jpg', 'Mobile Accessories | SmartProZen', 'Protect and enhance your mobile devices with our premium accessories.', 3, 1),
('Wearable Technology', 'wearables', 'Stay connected and track your health with our innovative wearable devices.', '/uploads/categories/wearables.jpg', 'Wearable Technology | SmartProZen', 'Smartwatches, fitness trackers, and other wearable tech for modern lifestyles.', 4, 1),
('Gaming Accessories', 'gaming', 'Level up your gaming experience with professional-grade gaming accessories.', '/uploads/categories/gaming.jpg', 'Gaming Accessories | SmartProZen', 'Enhance your gaming setup with our premium gaming accessories and peripherals.', 5, 1),
('Digital Products', 'digital', 'Software, apps, and digital services to boost your productivity and creativity.', '/uploads/categories/digital.jpg', 'Digital Products | SmartProZen', 'Digital products including software, apps, and online services.', 6, 1);
