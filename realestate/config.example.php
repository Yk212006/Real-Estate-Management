<?php
/**
 * Configuration Template for Deployment
 * 
 * 1. Copy this file to 'config.php'
 * 2. Update with your production values
 * 3. Keep config.php secure (add to .gitignore if using git)
 */

// Database Configuration
define('DB_HOST', 'localhost');           // Database host (usually 'localhost')
define('DB_USER', 'your_db_username');    // Database username
define('DB_PASS', 'your_db_password');    // Database password
define('DB_NAME', 'realestate');          // Database name

// Site Configuration
define('SITE_URL', 'https://yourdomain.com');  // Your site URL (no trailing slash)
define('SITE_NAME', 'Real Estate Management System');
define('ADMIN_EMAIL', 'admin@yourdomain.com');

// Admin Credentials (CHANGE THESE!)
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'change_this_password_212006');  // Use a strong password!

// Security Settings
define('SESSION_TIMEOUT', 3600);  // Session timeout in seconds (1 hour)
define('MAX_LOGIN_ATTEMPTS', 5);  // Max failed login attempts before lockout

// Upload Settings
define('UPLOAD_DIR', 'images/properties/');
define('MAX_FILE_SIZE', 10485760);  // 10MB in bytes
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Email Configuration (for contact form)
define('SMTP_ENABLED', false);  // Set to true to enable SMTP email
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@example.com');
define('SMTP_PASS', 'your-email-password');
define('SMTP_FROM', 'noreply@yourdomain.com');
define('SMTP_FROM_NAME', 'Real Estate System');

// Development vs Production
define('ENVIRONMENT', 'production');  // 'development' or 'production'

// Error Reporting
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/logs/error.log');
}

// Timezone
date_default_timezone_set('Asia/Kolkata');  // Adjust to your timezone

// Database Connection Function
function getDbConnection() {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if (!$conn) {
        if (ENVIRONMENT === 'development') {
            die("Connection failed: " . mysqli_connect_error());
        } else {
            die("Database connection error. Please contact the administrator.");
        }
    }
    
    mysqli_set_charset($conn, "utf8mb4");
    return $conn;
}
?>
