<?php
require_once('../connection.php');

// Check if admin_users table exists
$table_check = $con->query("SHOW TABLES LIKE 'admin_users'");

if ($table_check->num_rows === 0) {
    // Create admin_users table
    $create_table = "
    CREATE TABLE IF NOT EXISTS `admin_users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `username` varchar(50) NOT NULL,
        `password` varchar(255) NOT NULL,
        `email` varchar(100) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `last_login` timestamp NULL DEFAULT NULL,
        `is_active` tinyint(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (`id`),
        UNIQUE KEY `username` (`username`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    if ($con->query($create_table)) {
        echo "<p>✓ Admin users table created successfully</p>";
        
        // Create default admin user
        $username = 'admin';
        $email = 'admin@example.com';
        $password = 'Admin@1234';
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $con->prepare("INSERT INTO admin_users (username, password, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hashed_password, $email);
        
        if ($stmt->execute()) {
            echo "<p>✓ Default admin user created successfully</p>";
            echo "<div class='alert alert-warning'><strong>Default Login Credentials:</strong><br>";
            echo "Username: <strong>admin</strong><br>";
            echo "Password: <strong>Admin@1234</strong><br><br>";
            echo "<strong>Important:</strong> Please change this password after first login!</div>";
            echo "<p><a href='login.php' class='btn btn-primary'>Go to Login Page</a></p>";
        } else {
            echo "<p class='text-danger'>Error creating admin user: " . $con->error . "</p>";
        }
    } else {
        echo "<p class='text-danger'>Error creating admin_users table: " . $con->error . "</p>";
    }
} else {
    echo "<p>Admin system is already set up. <a href='login.php'>Go to login page</a></p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Setup - Real Estate Management</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.css" />
    <style>
        body { padding: 20px; }
        .alert { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin System Setup</h2>
        <div class="card">
            <div class="card-body">
                <?php if (isset($message)) echo "<p>$message</p>"; ?>
            </div>
        </div>
    </div>
</body>
</html>
