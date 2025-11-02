<?php
session_start();
require_once('../connection.php');

// Check if already logged in
if (isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$needs_setup = false;

// Check if admin_users table exists
$table_check = $con->query("SHOW TABLES LIKE 'admin_users'");
if ($table_check->num_rows === 0) {
    $needs_setup = true;
    $error = 'Admin system needs initial setup. Please run the setup script first.';
}

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$needs_setup) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        try {
            // Prepare and execute query
            $stmt = $con->prepare("SELECT id, username, password, is_active FROM admin_users WHERE username = ? LIMIT 1");
            if ($stmt === false) {
                throw new Exception('Database error. Please check if the admin_users table exists.');
            }
            
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    if ($user['is_active'] == 1) {
                        // Password is correct and account is active
                        $_SESSION['admin_logged_in'] = true;
                        $_SESSION['admin_id'] = $user['id'];
                        $_SESSION['admin_username'] = $user['username'];
                        
                        // Update last login time
                        $update = $con->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
                        $update->bind_param("i", $user['id']);
                        $update->execute();
                        
                        // Regenerate session ID to prevent session fixation
                        session_regenerate_id(true);
                        
                        header('Location: index.php');
                        exit;
                    } else {
                        $error = 'This account has been deactivated.';
                    }
                } else {
                    $error = 'Invalid username or password';
                }
            } else {
                $error = 'Invalid username or password';
            }
        } catch (Exception $e) {
            $error = 'Database error. Please contact the administrator.';
            error_log("Login error: " . $e->getMessage());
        }
        
        // Log failed login attempt
        error_log(sprintf(
            "Failed login attempt - Username: %s, IP: %s, User-Agent: %s",
            $username,
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ));
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - Real Estate Management</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.css" />
    <style>
        body { 
            background-color: #f5f5f5;
            padding-top: 100px;
        }
        .login-form {
            max-width: 400px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .login-form h2 {
            margin-bottom: 30px;
            color: #333;
        }
        .form-control {
            height: 45px;
            padding: 10px 15px;
            margin-bottom: 20px;
        }
        .btn-login {
            background: #0BE0FD;
            border: none;
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
        }
        .btn-login:hover {
            background: #09c7e4;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="login-form">
                    <h2 class="text-center">Admin Login</h2>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    
                    <form method="post" action="">
                        <div class="form-group">
                            <input type="text" name="username" class="form-control" placeholder="Username" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block btn-login">Login</button>
                        </div>
                    </form>
                    
                    <div class="text-center">
                        <a href="../index.php" class="text-muted">← Back to Website</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../assets/jquery-1.9.1.min.js"></script>
    <script src="../assets/bootstrap/js/bootstrap.js"></script>
</body>
</html>
