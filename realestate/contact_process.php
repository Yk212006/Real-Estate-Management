<?php
// Contact form processor with database storage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include_once "connection.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.php');
    exit;
}

$fullname = trim(strip_tags($_POST['fullname'] ?? ''));
$email = trim(strip_tags($_POST['email'] ?? ''));
$phone = trim(strip_tags($_POST['phone'] ?? ''));
$message = trim(strip_tags($_POST['message'] ?? ''));

// Validate required fields
if (!$fullname || !$email || !$message) {
    header('Location: contact.php?error=' . urlencode('Please fill in all required fields.'));
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: contact.php?error=' . urlencode('Please enter a valid email address.'));
    exit;
}

// Create contact_messages table if it doesn't exist
$create_table = "CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('new', 'read', 'replied') DEFAULT 'new'
)";

if (!mysqli_query($con, $create_table)) {
    // Table creation failed, but continue
    error_log("Failed to create contact_messages table: " . mysqli_error($con));
}

// Insert message into database
$stmt = mysqli_prepare($con, "INSERT INTO contact_messages (fullname, email, phone, message) VALUES (?, ?, ?, ?)");

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ssss", $fullname, $email, $phone, $message);
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        
        // Also log to file as backup
        $logfile = __DIR__ . '/contact_log.txt';
        $entry = "[" . date('Y-m-d H:i:s') . "] Name: $fullname | Email: $email | Phone: $phone\nMessage: $message\n\n";
        @file_put_contents($logfile, $entry, FILE_APPEND | LOCK_EX);
        
        // Redirect with success message
        header('Location: contact.php?success=1');
        exit;
    } else {
        mysqli_stmt_close($stmt);
        header('Location: contact.php?error=' . urlencode('Failed to save your message. Please try again.'));
        exit;
    }
} else {
    // Fallback: log to file if database insert fails
    $logfile = __DIR__ . '/contact_log.txt';
    $entry = "[" . date('Y-m-d H:i:s') . "] Name: $fullname | Email: $email | Phone: $phone\nMessage: $message\n\n";
    if (@file_put_contents($logfile, $entry, FILE_APPEND | LOCK_EX)) {
        header('Location: contact.php?success=1');
    } else {
        header('Location: contact.php?error=' . urlencode('Unable to process your message. Please try again later.'));
    }
    exit;
}
?>
