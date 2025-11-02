<?php
// Newsletter subscription processor
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't show errors in JSON response

// Include database connection
include_once "connection.php";

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$email = trim(strip_tags($_POST['email'] ?? ''));

// Validate email
if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Please enter your email address']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address']);
    exit;
}

// Create newsletter_subscribers table if it doesn't exist
$create_table = "CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'unsubscribed') DEFAULT 'active',
    INDEX idx_email (email)
)";

if (!mysqli_query($con, $create_table)) {
    error_log("Failed to create newsletter_subscribers table: " . mysqli_error($con));
}

// Check if email already exists
$check_stmt = mysqli_prepare($con, "SELECT id, status FROM newsletter_subscribers WHERE email = ?");
if ($check_stmt) {
    mysqli_stmt_bind_param($check_stmt, "s", $email);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        mysqli_stmt_close($check_stmt);
        
        if ($row['status'] === 'active') {
            echo json_encode(['success' => false, 'message' => 'This email is already subscribed']);
            exit;
        } else {
            // Reactivate subscription
            $update_stmt = mysqli_prepare($con, "UPDATE newsletter_subscribers SET status = 'active', subscribed_at = NOW() WHERE email = ?");
            mysqli_stmt_bind_param($update_stmt, "s", $email);
            
            if (mysqli_stmt_execute($update_stmt)) {
                mysqli_stmt_close($update_stmt);
                echo json_encode(['success' => true, 'message' => 'Welcome back! Your subscription has been reactivated']);
                exit;
            } else {
                mysqli_stmt_close($update_stmt);
                echo json_encode(['success' => false, 'message' => 'Failed to update subscription']);
                exit;
            }
        }
    }
    mysqli_stmt_close($check_stmt);
}

// Insert new subscription
$insert_stmt = mysqli_prepare($con, "INSERT INTO newsletter_subscribers (email) VALUES (?)");

if ($insert_stmt) {
    mysqli_stmt_bind_param($insert_stmt, "s", $email);
    
    if (mysqli_stmt_execute($insert_stmt)) {
        mysqli_stmt_close($insert_stmt);
        
        // Log subscription to file as backup
        $logfile = __DIR__ . '/newsletter_log.txt';
        $entry = "[" . date('Y-m-d H:i:s') . "] Subscribed: $email\n";
        @file_put_contents($logfile, $entry, FILE_APPEND | LOCK_EX);
        
        echo json_encode(['success' => true, 'message' => 'Successfully subscribed! Thank you for joining our newsletter']);
    } else {
        mysqli_stmt_close($insert_stmt);
        echo json_encode(['success' => false, 'message' => 'Failed to subscribe. Please try again']);
    }
} else {
    // Fallback: log to file if database insert fails
    $logfile = __DIR__ . '/newsletter_log.txt';
    $entry = "[" . date('Y-m-d H:i:s') . "] Subscribed: $email\n";
    if (@file_put_contents($logfile, $entry, FILE_APPEND | LOCK_EX)) {
        echo json_encode(['success' => true, 'message' => 'Successfully subscribed! Thank you for joining our newsletter']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Unable to process subscription. Please try again later']);
    }
}
?>
