# SmartProZen - Digital Product E-commerce CMS

A complete, modern, and feature-rich Content Management System (CMS) designed specifically for selling digital products online. Built with PHP, MySQL, and Bootstrap 5, SmartProZen provides a robust platform for digital product sales with an intuitive admin panel and customer-facing store.

## 🚀 Features

### Core E-commerce Features
- **Digital Product Management** - Upload, manage, and sell digital products
- **Shopping Cart & Checkout** - Complete e-commerce functionality
- **Order Management** - Track and manage customer orders
- **User Management** - Customer registration, login, and profiles
- **Payment Integration** - Support for multiple payment gateways
- **Coupon System** - Create and manage discount coupons
- **Wishlist** - Allow customers to save favorite products
- **Download Management** - Secure digital product downloads
- **Review System** - Customer reviews and ratings

### Content Management
- **Page Builder** - Drag-and-drop page builder with sections
- **Blog System** - Complete blogging functionality
- **Media Library** - Centralized media management
- **SEO Tools** - Built-in SEO optimization
- **Multi-language Support** - English and Bengali language support
- **Template System** - Customizable page templates

### Admin Features
- **Dashboard Analytics** - Sales reports and statistics
- **Role-based Access Control** - Granular permission system
- **Activity Logging** - Track all system activities
- **Settings Management** - Comprehensive site configuration
- **Email Templates** - Customizable email notifications
- **Backup & Restore** - Database backup functionality

### Technical Features
- **Responsive Design** - Mobile-first, fully responsive
- **Modern UI/UX** - Clean, professional interface
- **Security** - CSRF protection, SQL injection prevention
- **Performance** - Optimized for speed and efficiency
- **XAMPP Ready** - Perfect for local development

## 📋 Requirements

- **PHP** 7.4 or higher
- **MySQL** 5.7 or higher
- **Apache** web server
- **XAMPP** (recommended for local development)
- **Web Browser** (Chrome, Firefox, Safari, Edge)

## 🛠️ Installation

### Using XAMPP (Recommended)

1. **Download XAMPP**
   - Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
   - Install XAMPP on your system

2. **Setup Project**
   ```bash
   # Navigate to XAMPP htdocs directory
   cd C:\xampp\htdocs
   
   # Clone or extract SmartProZen
   # Place the smartprozen folder in htdocs
   ```

3. **Database Setup**
   - Start XAMPP Control Panel
   - Start Apache and MySQL services
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named `smartprozen_db`

4. **Configuration**
   - Copy `config.php.template` to `config.php`
   - Update database credentials in `config.php`:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('DB_NAME', 'smartprozen_db');
     define('SITE_URL', 'http://localhost/smartprozen');
     ```

5. **Installation Wizard**
   - Open your browser and go to `http://localhost/smartprozen/setup.php`
   - Follow the installation wizard
   - Create your admin account
   - Complete the setup

6. **Access Your Store**
   - Frontend: `http://localhost/smartprozen/`
   - Admin Panel: `http://localhost/smartprozen/admin/`

## 📁 Project Structure

```
smartprozen/
├── admin/                  # Admin panel files
│   ├── dashboard.php      # Admin dashboard
│   ├── manage_products.php # Product management
│   ├── manage_orders.php  # Order management
│   └── ...
├── api/                   # API endpoints
├── assets/                # Static assets
├── auth/                  # Authentication files
├── cart/                  # Shopping cart functionality
├── core/                  # Core system files
│   ├── db.php            # Database connection
│   ├── functions.php     # Core functions
│   └── media_handler.php # Media handling
├── css/                   # Stylesheets
├── includes/              # Common includes
├── lang/                  # Language files
├── uploads/               # Uploaded files
│   ├── media/            # Product images
│   ├── files/            # Digital products
│   └── logos/            # Site logos
├── user/                  # User dashboard
├── config.php             # Configuration
├── schema.sql            # Database schema
└── setup.php             # Installation wizard
```

## 🎨 Customization

### Themes
- Edit CSS files in the `css/` directory
- Modify `enhanced.css` for main styling
- Use Bootstrap 5 classes for quick styling

### Languages
- Add new language files in `lang/` directory
- Follow the JSON format of existing language files
- Update language switcher in header

### Templates
- Page templates are in `templates/` directory
- Section templates in `templates/sections/`
- Modify or create new templates as needed

## 🔧 Configuration

### Site Settings
Access admin panel → Settings to configure:
- Site name and description
- Business information
- Email settings
- Payment gateways
- SEO settings
- Social media links

### Product Management
- Add products with images and digital files
- Set categories and tags
- Configure pricing and discounts
- Manage inventory

### User Management
- Create user accounts
- Set up roles and permissions
- Manage customer data

## 🚀 Deployment

### Production Setup

1. **Server Requirements**
   - PHP 7.4+ with MySQLi extension
   - MySQL 5.7+
   - Apache/Nginx web server
   - SSL certificate (recommended)

2. **File Upload**
   - Upload all files to your web server
   - Set proper file permissions (755 for directories, 644 for files)
   - Ensure `uploads/` directory is writable

3. **Database Setup**
   - Create database on your server
   - Import `schema.sql` file
   - Update `config.php` with production credentials

4. **Security**
   - Change default admin password
   - Enable HTTPS
   - Set up regular backups
   - Update PHP and MySQL to latest versions

## 📱 Mobile Responsiveness

SmartProZen is fully responsive and works perfectly on:
- Desktop computers
- Tablets
- Mobile phones
- All modern browsers

## 🔒 Security Features

- **SQL Injection Prevention** - Prepared statements
- **XSS Protection** - Input sanitization
- **CSRF Protection** - Token-based protection
- **Password Hashing** - Secure password storage
- **Session Management** - Secure session handling
- **File Upload Security** - Type and size validation

## 🛠️ Development

### Adding New Features

1. **Database Changes**
   - Update `schema.sql` with new tables/fields
   - Create migration scripts if needed

2. **Backend Development**
   - Add new PHP files in appropriate directories
   - Follow existing code structure and naming conventions
   - Use prepared statements for database queries

3. **Frontend Development**
   - Use Bootstrap 5 classes
   - Follow responsive design principles
   - Test on multiple devices

### Code Standards

- Use meaningful variable and function names
- Comment complex code sections
- Follow PSR-12 coding standards
- Validate all user inputs
- Use prepared statements for database queries

## 📊 Analytics & Reporting

The admin dashboard provides:
- Sales analytics
- Order statistics
- User activity logs
- Product performance metrics
- Revenue reports

## 🆘 Support

### Common Issues

1. **Database Connection Error**
   - Check database credentials in `config.php`
   - Ensure MySQL service is running
   - Verify database exists

2. **File Upload Issues**
   - Check `uploads/` directory permissions
   - Verify PHP upload settings
   - Check file size limits

3. **Page Not Found Errors**
   - Check Apache mod_rewrite is enabled
   - Verify .htaccess file exists
   - Check file permissions

### Getting Help

- Check the documentation
- Review error logs in `logs/` directory
- Test with default settings first
- Ensure all requirements are met

## 📄 License

This project is open source and available under the MIT License.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📞 Contact

For support or questions:
- Email: support@smartprozen.com
- Website: https://smartprozen.com

---

**SmartProZen** - Your complete digital product e-commerce solution! 🚀
