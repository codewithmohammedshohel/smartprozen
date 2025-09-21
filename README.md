# SmartProZen CMS E-commerce Platform

A comprehensive, fully customizable Content Management System (CMS) and E-commerce platform built with PHP, MySQL, and modern web technologies. Perfect for both physical and digital product sales.

## 🚀 Key Features

### 🎨 **100% Customizable Design**
- **Dynamic Theme System**: Real-time theme customization with color picker, fonts, and styling
- **Page Builder**: Drag-and-drop page builder with customizable sections
- **Responsive Templates**: Mobile-first responsive design that works on all devices
- **Multiple Themes**: Built-in themes (Default, Dark, Modern) with easy switching
- **Custom CSS**: Advanced CSS customization options

### 🛒 **Complete E-commerce Solution**
- **Physical & Digital Products**: Support for both product types with automatic delivery
- **Product Management**: Categories, tags, images, galleries, and SEO optimization
- **Order Management**: Complete order processing, tracking, and status updates
- **Payment Gateways**: Stripe, PayPal, and custom payment methods
- **Shopping Cart**: Advanced cart functionality with AJAX updates
- **Coupon System**: Discount codes and promotional offers
- **Wishlist**: Save products for later purchase
- **Reviews & Ratings**: Customer feedback system

### 👥 **User Management**
- **User Registration**: Secure user registration with email verification
- **Role-Based Access**: Admin, Editor, and Viewer roles with permissions
- **User Dashboard**: Personal dashboard with orders, downloads, and profile
- **Guest Checkout**: Allow purchases without registration
- **Account Management**: Profile editing and password reset

### 📊 **Admin Panel**
- **Dashboard**: Comprehensive analytics and metrics
- **Product Management**: Add, edit, and manage products with media
- **Order Management**: Process orders and update statuses
- **Customer Management**: View and manage customer accounts
- **Content Management**: Pages, posts, and media library
- **Settings**: Site configuration, theme settings, and preferences
- **Reports**: Sales reports and analytics
- **Activity Logs**: Track all admin and user activities

### 🔧 **Technical Features**
- **SEO Optimized**: Meta tags, sitemaps, and search engine optimization
- **Security**: CSRF protection, SQL injection prevention, secure sessions
- **Performance**: Optimized database queries and caching
- **Multi-language Ready**: Framework for multiple languages
- **API Ready**: RESTful API endpoints for integrations
- **Backup System**: Automated backup functionality
- **Error Handling**: Comprehensive error logging and handling

## 📋 Requirements

- **PHP**: 7.4 or higher (8.0+ recommended)
- **MySQL**: 5.7 or higher (8.0+ recommended)
- **Web Server**: Apache/Nginx
- **Extensions**: GD, cURL, JSON, PDO, OpenSSL
- **Memory**: 128MB+ PHP memory limit
- **Storage**: 100MB+ for installation and uploads

## 🚀 Quick Installation

### For XAMPP (Local Development)

1. **Download XAMPP**
   ```bash
   # Download from https://www.apachefriends.org/
   # Install and start Apache + MySQL
   ```

2. **Setup Project**
   ```bash
   # Copy to htdocs folder
   cp -r smartprozen/ C:/xampp/htdocs/smartprozen/
   ```

3. **Database Setup**
   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Create database: `smartprozen_db`
   - Import: `smartprozen_db.sql`

4. **Configuration**
   - System auto-detects XAMPP environment
   - Default settings work out of the box
   - Access: http://localhost/smartprozen

5. **Admin Setup**
   - Run setup: http://localhost/smartprozen/setup.php
   - Create admin account
   - Access admin: http://localhost/smartprozen/admin/

### For cPanel (Production Hosting)

1. **Upload Files**
   - Upload all files to `public_html` folder
   - Maintain folder structure

2. **Database Setup**
   - Create MySQL database in cPanel
   - Create database user with full permissions
   - Import `smartprozen_db.sql`

3. **Configuration**
   - Copy `config.php.template` to `config.php`
   - Update database credentials and site URL
   - Set file permissions: `chmod 755 uploads/ logs/`

4. **SSL Certificate**
   - Enable SSL in cPanel
   - Update SITE_URL to use HTTPS

## 🎨 Customization Guide

### Theme Customization
1. **Access Theme Settings**: Admin Panel → Settings → Theme Settings
2. **Color Palette**: Customize primary, secondary, and accent colors
3. **Typography**: Choose from Google Fonts (Poppins, Roboto, Open Sans, etc.)
4. **Layout**: Adjust button radius, card radius, and shadows
5. **Live Preview**: See changes in real-time

### Page Builder
1. **Create Pages**: Admin Panel → Pages → Add New Page
2. **Add Sections**: Choose from Hero, Rich Text, Products, FAQ, etc.
3. **Customize Content**: Edit text, images, and settings for each section
4. **Drag & Drop**: Reorder sections with drag-and-drop interface

### Product Management
1. **Add Products**: Admin Panel → Products → Add New Product
2. **Product Types**: Choose Physical or Digital products
3. **Media**: Upload main image and gallery images
4. **SEO**: Set meta title, description, and keywords
5. **Categories**: Organize products with categories and tags

## 📁 Project Structure

```
smartprozen/
├── admin/                 # Admin panel files
│   ├── dashboard.php     # Admin dashboard
│   ├── manage_products.php # Product management
│   ├── settings.php      # Site settings
│   ├── theme_settings.php # Theme customization
│   └── ...
├── auth/                 # Authentication files
├── cart/                 # Shopping cart functionality
├── core/                 # Core system files
│   ├── db.php           # Database connection
│   ├── functions.php    # Core functions
│   └── ...
├── css/                  # Stylesheets
│   ├── enhanced.css     # Main theme
│   ├── dark.css         # Dark theme
│   └── modern-components.css
├── includes/             # Template includes
├── templates/            # Page templates
├── uploads/              # File uploads
├── config.php            # Configuration
├── setup.php             # Installation script
└── sample_data.sql       # Sample data
```

## 🔧 Configuration

### Environment Detection
The system automatically detects your environment:
- **Local**: localhost, 127.0.0.1, 192.168.x.x
- **Production**: Any domain with dots

### Database Configuration
```php
// Local (XAMPP)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'smartprozen_db');

// Production (cPanel)
define('DB_HOST', 'localhost');
define('DB_USER', 'your_cpanel_user');
define('DB_PASS', 'your_cpanel_password');
define('DB_NAME', 'your_cpanel_db');
```

### Email Configuration
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
define('SMTP_PORT', 587);
```

## 📊 Sample Data

The system includes comprehensive sample data:
- **10 Sample Products**: Mix of physical and digital products
- **6 Product Categories**: Organized product categories
- **5 Sample Pages**: Home, About, Services, Contact, etc.
- **5 Testimonials**: Customer testimonials
- **5 Blog Posts**: Sample blog content
- **4 Coupons**: Discount codes
- **Sample Menus**: Navigation menus
- **Sample Reviews**: Product reviews and ratings

## 🛡️ Security Features

- **CSRF Protection**: All forms protected against CSRF attacks
- **SQL Injection Prevention**: Prepared statements throughout
- **XSS Protection**: Input sanitization and output escaping
- **Secure Sessions**: Session security configuration
- **File Upload Security**: Type and size validation
- **Password Hashing**: bcrypt password hashing
- **Access Control**: Role-based permissions

## 🚀 Performance Optimization

- **Database Optimization**: Indexed queries and optimized structure
- **Image Optimization**: Automatic image resizing and compression
- **Caching**: Built-in caching system
- **CDN Ready**: Compatible with CDN services
- **Minified Assets**: Optimized CSS and JavaScript
- **Lazy Loading**: Images and content lazy loading

## 📱 Mobile Responsive

- **Mobile-First Design**: Optimized for mobile devices
- **Touch-Friendly**: Touch-optimized interface
- **Responsive Images**: Adaptive image sizing
- **Mobile Navigation**: Collapsible mobile menu
- **Fast Loading**: Optimized for mobile networks

## 🔌 API Integration

- **RESTful API**: Built-in API endpoints
- **Payment Gateways**: Stripe, PayPal integration
- **Email Services**: SMTP configuration
- **Social Media**: Social sharing integration
- **Analytics**: Google Analytics integration

## 📈 Analytics & Reporting

- **Sales Analytics**: Revenue and order tracking
- **Customer Insights**: User behavior analysis
- **Product Performance**: Best-selling products
- **Traffic Analysis**: Page views and user flow
- **Conversion Tracking**: Order conversion rates

## 🆘 Support & Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check database credentials in `config.php`
   - Ensure MySQL service is running
   - Verify database exists

2. **File Upload Issues**
   - Check file permissions: `chmod 755 uploads/`
   - Verify upload directory exists
   - Check PHP upload limits

3. **Theme Not Loading**
   - Clear browser cache
   - Check CSS file permissions
   - Verify theme settings in admin

4. **Admin Login Issues**
   - Reset admin password via database
   - Check session configuration
   - Clear browser cache

### Getting Help
- Check error logs in `logs/` folder
- Review this documentation
- Test in local environment first
- Check PHP and MySQL versions

## 🔄 Updates & Maintenance

### Regular Maintenance
- **Database Backups**: Automated daily backups
- **Security Updates**: Regular security patches
- **Performance Monitoring**: Track site performance
- **Content Updates**: Regular content refresh

### Updates
- **Backup First**: Always backup before updating
- **Test Locally**: Test updates in local environment
- **Check Compatibility**: Verify PHP/MySQL compatibility
- **Update Dependencies**: Keep libraries updated

## 📄 License

This project is open source. Please check the license file for details.

## 🤝 Contributing

Contributions are welcome! Please follow these guidelines:
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📞 Contact

For support, questions, or custom development:
- **Email**: support@smartprozen.com
- **Website**: https://smartprozen.com
- **Documentation**: Check this README and DEPLOYMENT_GUIDE.md

---

**SmartProZen** - Your complete solution for digital commerce and content management. Built with modern technologies and designed for scalability, security, and ease of use.