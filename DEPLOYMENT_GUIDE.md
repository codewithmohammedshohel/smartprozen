# SmartProZen CMS E-commerce Deployment Guide

## Overview
SmartProZen is a fully customizable CMS e-commerce platform designed for both physical and digital products. This guide covers deployment for both XAMPP (local development) and cPanel (production hosting).

## Features
- ✅ **100% Customizable CMS** - Complete control over content, design, and functionality
- ✅ **E-commerce Ready** - Support for both physical and digital products
- ✅ **Page Builder** - Drag-and-drop page builder with customizable sections
- ✅ **Multi-product Support** - Physical products, digital downloads, subscriptions
- ✅ **Admin Panel** - Comprehensive admin dashboard with role-based permissions
- ✅ **Payment Gateways** - Stripe, PayPal, and custom payment methods
- ✅ **SEO Optimized** - Built-in SEO tools and meta management
- ✅ **Responsive Design** - Mobile-first responsive templates
- ✅ **Multi-language Ready** - Framework for multiple languages
- ✅ **Security Features** - CSRF protection, SQL injection prevention, secure sessions

## Quick Start

### For XAMPP (Local Development)

1. **Download and Install XAMPP**
   - Download XAMPP from https://www.apachefriends.org/
   - Install and start Apache and MySQL services

2. **Setup Project**
   ```bash
   # Copy project to htdocs folder
   cp -r smartprozen/ C:/xampp/htdocs/smartprozen/
   
   # Or extract ZIP file directly to htdocs
   ```

3. **Database Setup**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create database: `smartprozen_db`
   - Import the SQL file: `smartprozen_db.sql`

4. **Configuration**
   - The system auto-detects XAMPP environment
   - Default configuration works out of the box
   - Access: http://localhost/smartprozen

5. **Admin Setup**
   - Run setup: http://localhost/smartprozen/setup.php
   - Create admin account
   - Access admin panel: http://localhost/smartprozen/admin/

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
   - Update database credentials:
     ```php
     define('DB_USER', 'your_cpanel_db_user');
     define('DB_PASS', 'your_cpanel_db_password');
     define('DB_NAME', 'your_cpanel_db_name');
     define('SITE_URL', 'https://yourdomain.com');
     ```

4. **File Permissions**
   ```bash
   chmod 755 uploads/
   chmod 755 logs/
   chmod 644 config.php
   ```

5. **SSL Certificate**
   - Enable SSL in cPanel
   - Update SITE_URL to use HTTPS

## Detailed Configuration

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
define('SMTP_FROM_EMAIL', 'noreply@yourdomain.com');
define('SMTP_FROM_NAME', 'Your Store Name');
```

### Security Configuration
```php
// Generate random keys
define('ENCRYPTION_KEY', 'your-32-character-random-key');
define('JWT_SECRET', 'your-jwt-secret-key');
```

## Admin Panel Features

### Dashboard
- Sales analytics and charts
- Order management
- Customer insights
- Product performance
- Recent activity logs

### Product Management
- Add/edit physical and digital products
- Product categories and tags
- Image galleries
- SEO metadata
- Inventory tracking

### Page Builder
- Drag-and-drop interface
- Pre-built sections:
  - Hero sections
  - Product showcases
  - Testimonials
  - FAQ sections
  - Rich text blocks
  - Custom HTML

### Content Management
- Pages and posts
- Media library
- Menu management
- SEO optimization
- Multi-language support

### E-commerce Features
- Order management
- Payment gateway integration
- Coupon system
- Customer management
- Digital product delivery
- Download tracking

## Customization

### Themes
- Located in `css/` folder
- Multiple theme options available
- Custom CSS support
- Responsive design

### Templates
- Page templates in `templates/` folder
- Section templates in `templates/sections/`
- Fully customizable layouts

### Modules
- Blog system
- E-commerce
- SEO tools
- Analytics
- User management

## Sample Data

The system includes sample data:
- Sample products (physical and digital)
- Sample pages with sections
- Sample categories
- Sample testimonials
- Sample menus

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check database credentials
   - Ensure MySQL service is running
   - Verify database exists

2. **File Upload Issues**
   - Check file permissions
   - Verify upload directory exists
   - Check PHP upload limits

3. **Email Not Working**
   - Verify SMTP settings
   - Check email provider credentials
   - Test with different email providers

4. **Admin Login Issues**
   - Reset admin password via database
   - Check session configuration
   - Clear browser cache

### Error Logs
- PHP errors: `logs/php-error.log`
- Application logs: `logs/` folder
- Enable debug mode for detailed errors

## Security Best Practices

1. **File Permissions**
   ```bash
   chmod 644 config.php
   chmod 755 uploads/
   chmod 755 logs/
   ```

2. **Database Security**
   - Use strong passwords
   - Regular backups
   - Limit database user permissions

3. **Server Security**
   - Keep PHP updated
   - Use HTTPS
   - Regular security updates

4. **Application Security**
   - Change default admin credentials
   - Regular password updates
   - Monitor activity logs

## Performance Optimization

1. **Caching**
   - Enable caching in config
   - Use CDN for static assets
   - Optimize images

2. **Database**
   - Regular database optimization
   - Index optimization
   - Query optimization

3. **Server**
   - PHP OPcache
   - Gzip compression
   - Browser caching

## Backup and Maintenance

### Automated Backups
```php
define('BACKUP_ENABLED', true);
define('BACKUP_RETENTION_DAYS', 30);
```

### Manual Backup
1. Export database
2. Download all files
3. Store in secure location

### Maintenance Mode
```php
define('MAINTENANCE_MODE', true);
define('MAINTENANCE_MESSAGE', 'Site under maintenance');
```

## Support and Updates

### Getting Help
- Check error logs first
- Review this documentation
- Test in local environment
- Check PHP and MySQL versions

### Updates
- Backup before updating
- Test updates locally first
- Check compatibility
- Update dependencies

## License
This project is open source. Please check the license file for details.

## Contributing
Contributions are welcome! Please follow the coding standards and submit pull requests.

---

**Note**: This is a comprehensive CMS e-commerce platform. Take time to explore all features and customize according to your needs. The admin panel provides extensive customization options for creating a unique online store.
