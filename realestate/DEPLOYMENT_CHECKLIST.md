# Pre-Deployment Checklist

## Before You Start
- [ ] Choose hosting provider and plan
- [ ] Register domain name
- [ ] Backup local database and files

## Files Preparation
- [ ] Test all functionality locally
- [ ] Export database from phpMyAdmin as `realestate.sql`
- [ ] Create ZIP archive of all files
- [ ] Review and update `connection.php` template
- [ ] Review `.htaccess` file

## Hosting Setup
- [ ] Access hosting control panel (cPanel/Plesk)
- [ ] Create MySQL database named `realestate`
- [ ] Create database user with strong password
- [ ] Grant all privileges to database user
- [ ] Note down: database name, username, password, host

## Upload Files
- [ ] Connect via FTP/SFTP or use File Manager
- [ ] Upload all files to `public_html/` or `www/` directory
- [ ] Verify all folders uploaded: assets/, images/, etc.
- [ ] Check file permissions (folders: 755, files: 644)
- [ ] Set `images/properties/` to 775 (writable)

## Database Setup
- [ ] Access phpMyAdmin from hosting control panel
- [ ] Select your database
- [ ] Import `realestate.sql` file
- [ ] Verify all tables imported successfully:
  - [ ] properties
  - [ ] agent
  - [ ] property_image
  - [ ] contact_messages (auto-created on first use)
  - [ ] newsletter_subscribers (auto-created on first use)

## Configuration
- [ ] Edit `connection.php` with production credentials:
  ```php
  $con = mysqli_connect("localhost", "db_user", "db_pass", "db_name");
  ```
- [ ] Update admin passwords in:
  - [ ] `admin_properties.php` (line ~14)
  - [ ] `admin_view_messages.php` (line ~14)
  - [ ] `admin_newsletter.php` (line ~14)
- [ ] Update contact email in `contact_config.php` (optional)

## Security Hardening
- [ ] Change default admin username and password
- [ ] Verify `.htaccess` file is uploaded and working
- [ ] Disable directory browsing
- [ ] Set proper file permissions
- [ ] Remove or secure `realestate.sql` file after import
- [ ] Remove `DEPLOYMENT_GUIDE.md` and `DEPLOYMENT_CHECKLIST.md` from live site
- [ ] Create `logs/` directory with write permissions (if using error logging)

## SSL Certificate (HTTPS)
- [ ] Install SSL certificate (free with Let's Encrypt or from hosting)
- [ ] Update `.htaccess` to force HTTPS (uncomment redirect rules)
- [ ] Verify site loads over HTTPS
- [ ] Update `SITE_URL` in configuration to use https://

## Testing
- [ ] Visit homepage: `https://yourdomain.com`
- [ ] Test navigation links (Home, Properties, About, Contact)
- [ ] View property listings page
- [ ] Click on individual property details
- [ ] Test contact form submission
- [ ] Test newsletter subscription
- [ ] Search properties functionality
- [ ] Filter properties (Sale, Rent)

## Admin Panel Testing
- [ ] Login to admin properties: `https://yourdomain.com/admin_properties.php`
- [ ] Verify credentials work
- [ ] Test adding new property
- [ ] Test uploading property image
- [ ] Test editing existing property
- [ ] Test marking property as Available/Sold
- [ ] Test deleting property
- [ ] Access admin contact messages
- [ ] Access admin newsletter subscribers
- [ ] Test logout

## Email Testing
- [ ] Submit contact form
- [ ] Check if email arrives at designated address
- [ ] Or verify message saved in database/log file
- [ ] Subscribe to newsletter
- [ ] Verify subscription recorded

## Performance & SEO
- [ ] Test page load speed (use GTmetrix or PageSpeed Insights)
- [ ] Verify images load correctly
- [ ] Check mobile responsiveness
- [ ] Test on different browsers (Chrome, Firefox, Safari, Edge)
- [ ] Submit sitemap to Google Search Console (optional)

## Monitoring Setup
- [ ] Set up Google Analytics (optional)
- [ ] Configure backup schedule (weekly database, monthly files)
- [ ] Set up uptime monitoring (e.g., UptimeRobot)
- [ ] Create admin contact list for alerts

## Documentation
- [ ] Document admin URLs and credentials (secure location)
- [ ] Note hosting account details
- [ ] Save FTP/SSH credentials
- [ ] Document database connection details
- [ ] Create user guide for property management (if needed)

## Post-Launch
- [ ] Announce site launch
- [ ] Monitor error logs for first 48 hours
- [ ] Test all forms again
- [ ] Verify emails/notifications working
- [ ] Check analytics tracking
- [ ] Create first database backup

## Maintenance Schedule
- [ ] Weekly: Check contact messages and newsletter subscribers
- [ ] Weekly: Database backup
- [ ] Monthly: Update property listings
- [ ] Monthly: Review and optimize images
- [ ] Quarterly: Update PHP/MySQL versions
- [ ] Yearly: Review security and update passwords

---

## Emergency Contacts
- Hosting Support: _________________________
- Domain Registrar Support: _________________________
- Developer Contact: _________________________

## Important URLs
- Live Site: https://yourdomain.com
- Admin Panel: https://yourdomain.com/admin_properties.php
- cPanel: https://yourdomain.com:2083
- phpMyAdmin: https://yourdomain.com/phpmyadmin
- FTP Host: ftp.yourdomain.com

---

**Date Deployed:** _______________
**Deployed By:** _______________
**Hosting Provider:** _______________
**Plan:** _______________
