<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once('../connection.php');

// Check if already logged in
if (isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$error = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        // Prepare and execute query
        $stmt = $con->prepare("SELECT id, username, password FROM admin_users WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Debug output - remove this after fixing
            echo "<!-- Debug Info: \n";
            echo "Submitted password: " . htmlspecialchars($password) . "\n";
            echo "Stored hash: " . $user['password'] . "\n";
            echo "Verification result: " . (password_verify($password, $user['password']) ? 'true' : 'false') . "\n";
            echo "-->";
            
            if (password_verify($password, $user['password'])) {
                // Password is correct, set session variables
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);
                
                header('Location: index.php');
                exit;
            }
        }
        
        // If we get here, login failed
        $error = 'Invalid username or password';
        // Log failed login attempt (you might want to implement rate limiting)
        error_log("Failed login attempt for username: " . $username . " from IP: " . $_SERVER['REMOTE_ADDR']);
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
