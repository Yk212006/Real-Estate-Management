<?php
require_once('../connection.php');

// Create admin_users table if it doesn't exist
$create_table_sql = "CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($con->query($create_table_sql) === TRUE) {
    echo "Admin users table created successfully<br>";
} else {
    die("Error creating table: " . $con->error);
}

// Create a default admin user if none exists
$check_user = $con->query("SELECT id FROM admin_users WHERE username = 'admin' LIMIT 1");

if ($check_user->num_rows === 0) {
    // Default admin credentials (change these in production!)
    $username = 'admin';
    $password = 'Admin@1234'; // Strong default password
    $email = 'admin@example.com';
    
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert the admin user
    $stmt = $con->prepare("INSERT INTO admin_users (username, password, email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $hashed_password, $email);
    
    if ($stmt->execute()) {
        echo "Default admin user created successfully!<br>";
        echo "Username: admin<br>";
        echo "Password: Admin@1234<br>";
        echo "<strong>IMPORTANT:</strong> Please change this password immediately after first login!<br>";
    } else {
        echo "Error creating admin user: " . $stmt->error . "<br>";
    }
} else {
    echo "Admin user already exists.<br>";
}

echo "<a href='login.php'>Go to login page</a>";
?>
