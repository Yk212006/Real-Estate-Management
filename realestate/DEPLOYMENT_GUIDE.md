# Real Estate Management System - Deployment Guide

## Pre-Deployment Checklist

### 1. **Database Setup**
- [ ] Export your local database from phpMyAdmin
- [ ] Note: Database name is `realestate`
- [ ] Tables: `properties`, `agent`, `property_image`, `contact_messages`, `newsletter_subscribers`

### 2. **Required Files**
All files in the `resw/` folder including:
- PHP files (*.php)
- HTML files (*.html)
- Assets folder (CSS, JS, images)
- SQL file: `realestate.sql`

---

## Deployment Options

### **Option 1: Shared Hosting (Recommended for Beginners)**

#### Popular Providers:
- **Hostinger** (Budget-friendly, ~$2-3/month)
- **Bluehost** (~$3-7/month)
- **SiteGround** (~$4-15/month)
- **A2 Hosting** (~$3-10/month)

#### Steps:
1. **Purchase Hosting & Domain**
   - Choose a plan with PHP 7.4+ and MySQL support
   - Register domain (e.g., yourrealestate.com)

2. **Upload Files via FTP/File Manager**
   - Use FileZilla, cPanel File Manager, or hosting control panel
   - Upload all files from `resw/` folder to `public_html/` or `www/`

3. **Create Database**
   - Go to cPanel → MySQL Databases
   - Create database: `realestate`
   - Create user and grant all privileges
   - Note database name, username, password

4. **Import Database**
   - Go to phpMyAdmin
   - Select your database
   - Click Import → Choose `realestate.sql`
   - Click Go

5. **Update Database Connection**
   - Edit `connection.php` with your hosting credentials:
   ```php
   $con = mysqli_connect("localhost", "your_db_user", "your_db_password", "your_db_name");
   ```

6. **Set File Permissions**
   - `images/properties/` folder: 755 or 775 (writable for uploads)

7. **Test Your Site**
   - Visit: `http://yourdomain.com`
   - Test contact form, newsletter, property management

---

### **Option 2: VPS/Cloud Hosting (More Control)**

#### Providers:
- **DigitalOcean** (~$5-10/month)
- **Linode** (~$5-10/month)
- **Vultr** (~$5-10/month)
- **AWS Lightsail** (~$3.50-10/month)

#### Requirements:
- Ubuntu 20.04/22.04 LTS
- Apache/Nginx
- PHP 7.4+ with mysqli extension
- MySQL 5.7+ or MariaDB 10.3+

#### Steps:
1. **Server Setup**
   ```bash
   # Update system
   sudo apt update && sudo apt upgrade -y
   
   # Install LAMP stack
   sudo apt install apache2 mysql-server php php-mysqli php-gd php-xml php-mbstring -y
   
   # Enable Apache modules
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   ```

2. **Upload Files**
   ```bash
   # Using SCP from your local machine
   scp -r C:\xampp\htdocs\resw/* user@your-server-ip:/var/www/html/
   
   # Or use SFTP client like FileZilla
   ```

3. **Database Setup**
   ```bash
   # Login to MySQL
   sudo mysql -u root -p
   
   # Create database and user
   CREATE DATABASE realestate;
   CREATE USER 'realestate_user'@'localhost' IDENTIFIED BY 'strong_password_here';
   GRANT ALL PRIVILEGES ON realestate.* TO 'realestate_user'@'localhost';
   FLUSH PRIVILEGES;
   EXIT;
   
   # Import database
   mysql -u root -p realestate < /path/to/realestate.sql
   ```

4. **Configure Apache**
   ```bash
   sudo nano /etc/apache2/sites-available/000-default.conf
   ```
   
   Add:
   ```apache
   <Directory /var/www/html>
       Options Indexes FollowSymLinks
       AllowOverride All
       Require all granted
   </Directory>
   ```

5. **Set Permissions**
   ```bash
   sudo chown -R www-data:www-data /var/www/html
   sudo chmod -R 755 /var/www/html
   sudo chmod -R 775 /var/www/html/images/properties
   ```

6. **Enable HTTPS (Free SSL)**
   ```bash
   sudo apt install certbot python3-certbot-apache -y
   sudo certbot --apache -d yourdomain.com -d www.yourdomain.com
   ```

---

### **Option 3: Free Hosting (Testing/Demo)**

#### Providers:
- **InfinityFree** (Free, with limitations)
- **000webhost** (Free tier available)
- **AwardSpace** (Free tier available)

**Note:** Free hosting often has:
- Limited bandwidth
- Ads on your site
- No custom domain (or paid extra)
- Limited support
- Not recommended for production

---

## Post-Deployment Configuration

### 1. **Update Database Credentials**
Edit `connection.php`:
```php
<?php
$con = mysqli_connect(
    "localhost",           // Host (usually localhost)
    "your_database_user",  // Database username
    "your_password",       // Database password
    "your_database_name"   // Database name
) or die ("Database Connection Failed!!!");
?>
```

### 2. **Security Updates**

#### Change Admin Password
The default admin credentials are:
- Username: `admin`
- Password: `212006`

**Update in all admin files:**
- `admin_properties.php`
- `admin_view_messages.php`
- `admin_newsletter.php`

Change line:
```php
if ($username === 'admin' && $password === 'YOUR_NEW_STRONG_PASSWORD') {
```

#### Secure Uploads Directory
Create `.htaccess` in `images/properties/`:
```apache
Options -Indexes
<FilesMatch "\.(php|php3|php4|php5|phtml)$">
    Order Deny,Allow
    Deny from all
</FilesMatch>
```

### 3. **Email Configuration (Optional)**
To send real emails from contact form, edit `contact_config.php`:
```php
return [
    'smtp_enabled' => true,
    'smtp' => [
        'host' => 'smtp.gmail.com',  // Your SMTP host
        'port' => 587,
        'username' => 'your-email@gmail.com',
        'password' => 'your-app-password',
        'secure' => 'tls',
        'from_email' => 'noreply@yourdomain.com',
        'to_email' => 'contact@yourdomain.com',
    ],
];
```

### 4. **Backup Strategy**
- **Database**: Export weekly from phpMyAdmin
- **Files**: Use FTP to download entire site monthly
- Consider automated backup solutions from your hosting provider

---

## Testing After Deployment

### Functionality Checklist:
- [ ] Homepage loads correctly
- [ ] Properties page shows listings
- [ ] Property detail pages work
- [ ] Contact form submits successfully
- [ ] Newsletter subscription works
- [ ] Admin login works at `/admin_properties.php`
- [ ] Can add/edit/delete properties
- [ ] Images upload properly
- [ ] All navigation links work
- [ ] Search functionality works

### Performance Optimization:
- [ ] Enable gzip compression
- [ ] Optimize images (compress before upload)
- [ ] Enable browser caching via .htaccess
- [ ] Consider CDN for assets (optional)

---

## Troubleshooting Common Issues

### **Database Connection Error**
- Check `connection.php` credentials
- Verify database exists
- Check if user has proper permissions
- Ensure mysqli extension is enabled

### **500 Internal Server Error**
- Check Apache error logs: `/var/log/apache2/error.log`
- Verify file permissions (755 for folders, 644 for files)
- Check `.htaccess` syntax

### **Images Not Uploading**
- Check `images/properties/` folder exists
- Verify folder permissions (775)
- Check PHP upload limits in `php.ini`:
  ```ini
  upload_max_filesize = 10M
  post_max_size = 10M
  ```

### **Email Not Sending**
- Configure SMTP settings in `contact_config.php`
- Check PHP mail function is enabled
- Verify firewall allows SMTP ports (587, 465)

---

## Recommended Domain Registrars

- **Namecheap** - Good pricing, free WhoisGuard
- **Google Domains** - Simple, integrated with Google services
- **Cloudflare** - Domain + free CDN/SSL
- **GoDaddy** - Popular, but upsells often

---

## Cost Estimation

### Minimal Setup:
- Domain: $10-15/year
- Shared Hosting: $2-5/month
- SSL Certificate: Free (Let's Encrypt)
- **Total: ~$35-75/year**

### Professional Setup:
- Domain: $10-15/year
- VPS Hosting: $5-10/month
- SSL Certificate: Free (Let's Encrypt)
- Email Hosting: $1-3/month (optional)
- **Total: ~$80-170/year**

---

## Need Help?

### Support Resources:
- Hosting provider documentation
- cPanel video tutorials (YouTube)
- PHP/MySQL forums
- Stack Overflow for technical issues

### Common Admin URLs:
- Admin Properties: `https://yourdomain.com/admin_properties.php`
- Contact Messages: `https://yourdomain.com/admin_view_messages.php`
- Newsletter: `https://yourdomain.com/admin_newsletter.php`

---

## Quick Deploy Command Reference

### Export Database (Local):
```bash
# From XAMPP MySQL bin directory
cd C:\xampp\mysql\bin
.\mysqldump.exe -u root -p212006 realestate > realestate_export.sql
```

### Compress Files for Upload:
```bash
# Zip entire project
zip -r realestate_deploy.zip resw/*
```

---

**Ready to deploy? Choose your hosting option above and follow the steps!**
