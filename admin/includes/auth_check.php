<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Store the requested URL for redirecting after login
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    
    // Redirect to login page
    header('Location: login.php');
    exit;
}

// Optional: Verify the user still exists in the database
require_once('../connection.php');
if (isset($_SESSION['admin_id'])) {
    $stmt = $con->prepare("SELECT id FROM admin_users WHERE id = ? AND is_active = 1 LIMIT 1");
    $stmt->bind_param("i", $_SESSION['admin_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // User no longer exists or is inactive
        session_destroy();
        header('Location: login.php?error=session_expired');
        exit;
    }
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Optional: Implement session timeout (30 minutes)
$inactive = 1800; // 30 minutes in seconds
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $inactive)) {
    session_unset();
    session_destroy();
    header('Location: login.php?error=session_timeout');
    exit;
}
?>
