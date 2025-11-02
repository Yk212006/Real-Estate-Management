<?php
// Connect to database
$con = mysqli_connect("localhost", "root", "212006", "realestate") or die("Database Connection Failed: " . mysqli_connect_error());

// Create admin_users table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS `admin_users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(50) NOT NULL,
    `password` varchar(255) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `last_login` timestamp NULL DEFAULT NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if (mysqli_query($con, $sql)) {
    echo "Table created/verified successfully<br>";
} else {
    die("Error creating table: " . mysqli_error($con) . "<br>");
}

// Set admin credentials
$username = 'admin';
$password = 'admin123'; // This will be hashed
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert or update admin user
$sql = "INSERT INTO admin_users (username, password, is_active) 
        VALUES (?, ?, 1)
        ON DUPLICATE KEY UPDATE 
            password = VALUES(password),
            is_active = 1";

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "ss", $username, $hashed_password);

if (mysqli_stmt_execute($stmt)) {
    echo "<h2>Admin account has been reset!</h2>";
    echo "<p>You can now log in with:</p>";
    echo "<p><strong>Username:</strong> admin</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "<p><a href='login.php'>Go to Login Page</a></p>";
} else {
    echo "Error: " . mysqli_error($con);
}

// Close the statement and connection
mysqli_stmt_close($stmt);
mysqli_close($con);
?>

<!-- Simple styling for better visibility -->
<style>
    body { 
        font-family: Arial, sans-serif; 
        line-height: 1.6; 
        margin: 20px; 
        max-width: 800px; 
        margin: 0 auto;
        padding: 20px;
    }
    h2 { color: #2c3e50; }
    p { margin: 10px 0; }
    a {
        display: inline-block;
        background: #3498db;
        color: white;
        padding: 10px 15px;
        text-decoration: none;
        border-radius: 4px;
        margin-top: 10px;
    }
    a:hover { background: #2980b9; }
</style>
