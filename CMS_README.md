# SmartProZen Complete CMS System

A comprehensive Content Management System designed for product selling websites with complete frontend customization capabilities. Perfect for deployment on XAMPP local server and cPanel file manager.

## 🚀 Features

### Complete CMS Functionality
- **Full Frontend Customization**: Every aspect of the frontend is customizable through the admin panel
- **Page Builder**: Drag-and-drop page builder with customizable sections
- **Theme Customization**: Live theme editor with color schemes, fonts, and layouts
- **Customizable Components**: Header, footer, and menu components that adapt to theme settings
- **Section Templates**: Pre-built section templates for quick page building

### E-commerce Features
- **Product Management**: Physical and digital products with inventory tracking
- **Order Management**: Complete order processing and tracking system
- **Shopping Cart**: Full-featured cart with AJAX functionality
- **Payment Integration**: Multiple payment gateway support
- **Coupon System**: Discount codes and promotional offers
- **User Accounts**: Customer registration, profiles, and order history

### Content Management
- **Dynamic Pages**: Create unlimited pages with custom sections
- **Section Types**: Hero, Featured Products, Testimonials, FAQ, Rich Text, Custom HTML
- **Media Library**: File upload and management system
- **SEO Optimization**: Meta tags, descriptions, and SEO-friendly URLs
- **Multi-language Support**: Built-in support for multiple languages

### Admin Features
- **Dashboard**: Comprehensive analytics and metrics
- **User Management**: Admin and customer user management
- **Settings Panel**: Complete site configuration
- **Activity Logs**: Track all admin and user activities
- **Reports**: Sales reports and analytics

## 📁 Project Structure

```
smartprozen/
├── admin/                          # Admin panel files
│   ├── dashboard.php              # Main dashboard
│   ├── manage_homepage.php        # Homepage builder
│   ├── manage_theme.php           # Theme customization
│   ├── manage_pages.php           # Page management
│   ├── manage_products.php        # Product management
│   ├── manage_orders.php          # Order management
│   └── section_forms/             # Section form templates
├── api/                           # API endpoints
├── assets/                        # Static assets
├── auth/                          # Authentication system
├── cart/                          # Shopping cart functionality
├── core/                          # Core system files
│   ├── db.php                     # Database connection
│   ├── functions.php              # Core functions
│   └── email_handler.php          # Email system
├── css/                           # Stylesheets
├── includes/                      # Reusable components
│   ├── customizable_header.php    # Dynamic header component
│   ├── customizable_footer.php    # Dynamic footer component
│   ├── header.php                 # Main header
│   └── footer.php                 # Main footer
├── templates/                     # Page templates
│   └── sections/                  # Section templates
├── uploads/                       # File uploads
├── user/                          # User dashboard
├── config.php                     # Configuration file
├── database_schema.sql            # Complete database schema
├── sample_data_part1.sql          # Sample data - Part 1
├── sample_data_part2.sql          # Sample data - Part 2
├── sample_data_part3.sql          # Sample data - Part 3
├── preloaded_pages.sql            # Preloaded pages with sections
└── setup_cms.php                  # Installation script
```

## 🛠️ Installation

### For XAMPP Local Server

1. **Download and Extract**
   ```bash
   # Extract the project to your XAMPP htdocs folder
   C:\xampp\htdocs\smartprozen\
   ```

2. **Database Setup**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named `smartprozen_db`
   - Run the setup script: http://localhost/smartprozen/setup_cms.php

3. **Configuration**
   - The system will auto-detect XAMPP environment
   - Database connection will be automatically configured
   - Admin user will be created automatically

### For cPanel Hosting

1. **Upload Files**
   - Upload all files to your `public_html` folder via cPanel File Manager
   - Ensure proper file permissions (755 for folders, 644 for files)

2. **Database Setup**
   - Create a MySQL database in cPanel
   - Update database credentials in `config.php`
   - Run the setup script: https://yourdomain.com/setup_cms.php

3. **Configuration**
   - Update site URL in `config.php`
   - Configure email settings
   - Set up SSL certificate if available

## 🎨 Customization Features

### Theme Customization
- **Color Schemes**: Primary and secondary color customization
- **Typography**: Font family selection from popular web fonts
- **Layout Options**: Multiple header and footer layouts
- **Custom CSS**: Add custom styles without editing files
- **Header/Footer Code**: Add custom HTML, CSS, or JavaScript

### Page Builder
- **Drag & Drop**: Reorder sections with drag-and-drop interface
- **Section Types**: 8+ different section types available
- **Templates**: Pre-built section templates for quick setup
- **Live Preview**: See changes in real-time
- **Responsive Design**: All sections are mobile-responsive

### Content Management
- **Dynamic Menus**: Create and manage navigation menus
- **Page Templates**: Multiple page layout options
- **Media Library**: Upload and organize images and files
- **SEO Tools**: Meta tags, descriptions, and keywords
- **Multilingual**: Built-in support for multiple languages

## 📊 Sample Data Included

### Products (6 Sample Products)
- **ZenBuds Pro 3**: Premium wireless earbuds ($89.99)
- **SmartGlow Ambient Light**: Smart LED light ($59.99)
- **ProCharge Wireless Stand**: Fast charging stand ($45.00)
- **ZenWatch Sport**: Smart fitness watch ($199.99)
- **GameMaster Pro Keyboard**: Mechanical gaming keyboard ($129.99)
- **Productivity Suite Pro**: Digital productivity suite ($49.99)

### Categories (6 Categories)
- Smart Home Devices
- Professional Audio
- Mobile Accessories
- Wearable Technology
- Gaming Accessories
- Digital Products

### Preloaded Pages (9 Pages)
- Home (with customizable sections)
- About Us
- Contact
- Products
- Blog
- Shipping Information
- Returns & Exchanges
- Privacy Policy
- Terms of Service

### Sample Users & Orders
- 3 sample customer accounts
- 3 sample orders with different statuses
- Customer testimonials and reviews

## 🔧 Admin Panel Features

### Dashboard
- Sales analytics and metrics
- Recent orders and customers
- Top-selling products
- Low stock alerts
- Activity logs

### Homepage Builder
- Visual section management
- Drag-and-drop reordering
- Section templates
- Live preview
- Quick actions

### Theme Customization
- Live color picker
- Font selection
- Layout options
- Custom CSS editor
- Header/footer code injection

### Product Management
- Add/edit products (physical & digital)
- Category management
- Inventory tracking
- Product images
- SEO optimization

### Order Management
- Order processing
- Status updates
- Customer communication
- Shipping tracking
- Payment processing

## 🌐 Frontend Features

### Responsive Design
- Mobile-first approach
- Bootstrap 5 framework
- Custom responsive components
- Touch-friendly interface

### Performance Optimized
- Minified CSS and JavaScript
- Image optimization
- Lazy loading
- Caching strategies

### SEO Friendly
- Clean URLs
- Meta tags management
- Sitemap generation
- Schema markup ready

### User Experience
- Fast loading times
- Intuitive navigation
- Shopping cart persistence
- Wishlist functionality
- Product reviews and ratings

## 🔐 Security Features

- SQL injection prevention
- XSS protection
- CSRF tokens
- Secure file uploads
- User authentication
- Role-based permissions
- Activity logging

## 📱 Mobile Support

- Responsive design
- Touch-optimized interface
- Mobile payment integration
- Progressive Web App (PWA) ready
- Offline functionality

## 🚀 Deployment Checklist

### Before Going Live
1. ✅ Update database credentials
2. ✅ Configure site URL
3. ✅ Set up SSL certificate
4. ✅ Configure email settings
5. ✅ Upload your logo and branding
6. ✅ Customize theme colors
7. ✅ Add your products
8. ✅ Test all functionality
9. ✅ Set up payment gateways
10. ✅ Configure shipping settings

### Post-Deployment
1. ✅ Monitor site performance
2. ✅ Set up regular backups
3. ✅ Monitor security logs
4. ✅ Update content regularly
5. ✅ Monitor analytics

## 📞 Support & Documentation

### Admin Login
- **URL**: `/admin/login.php`
- **Username**: `admin`
- **Password**: `admin123`

### Key Files to Customize
- `config.php` - Site configuration
- `css/enhanced.css` - Custom styles
- `includes/customizable_header.php` - Header customization
- `includes/customizable_footer.php` - Footer customization

### Database Tables
- `pages` - Dynamic pages
- `page_sections` - Page sections
- `products` - Product catalog
- `orders` - Order management
- `users` - User accounts
- `settings` - Site settings

## 🎯 Perfect For

- **E-commerce Stores**: Complete online store functionality
- **Product Showcases**: Display and sell physical/digital products
- **Service Businesses**: Showcase services with booking capabilities
- **Content Sites**: Blog and content management
- **Portfolio Sites**: Professional portfolio presentation
- **Corporate Websites**: Business websites with e-commerce

## 🔄 Updates & Maintenance

The system is designed for easy maintenance and updates:
- Modular architecture
- Clean code structure
- Comprehensive documentation
- Regular security updates
- Performance optimizations

---

**SmartProZen CMS** - Complete frontend customization for modern websites. Built for XAMPP and cPanel deployment with professional e-commerce capabilities.
