<?php
// Admin Dashboard
require_once 'includes/auth_check.php';

// Update last login time if not set
if (!isset($_SESSION['last_activity'])) {
    $stmt = $con->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['admin_id']);
    $stmt->execute();
}

// Include database connection
include_once "../connection.php";

// Get admin stats
$stats = [
    'total_properties' => 0,
    'active_properties' => 0,
    'total_messages' => 0,
    'unread_messages' => 0
];

// Get property counts
$result = $con->query("SELECT COUNT(*) as total FROM properties");
if ($result) $stats['total_properties'] = $result->fetch_assoc()['total'];

$result = $con->query("SELECT COUNT(*) as active FROM properties WHERE status = 'active'");
if ($result) $stats['active_properties'] = $result->fetch_assoc()['active'];

// Get message counts
$result = $con->query("SELECT COUNT(*) as total FROM contact_messages");
if ($result) $stats['total_messages'] = $result->fetch_assoc()['total'];

$result = $con->query("SELECT COUNT(*) as unread FROM contact_messages WHERE is_read = 0");
if ($result) $stats['unread_messages'] = $result->fetch_assoc()['unread'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Real Estate Management</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.css" />
    <style>
        body { padding-top: 60px; }
        .admin-panel { margin-top: 20px; }
        .dashboard-card { 
            padding: 20px; 
            margin-bottom: 20px; 
            border-radius: 5px; 
            text-align: center;
            background: #f8f9fa;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .dashboard-card i { 
            font-size: 36px; 
            margin-bottom: 15px;
            color: #0BE0FD;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-inverse navbar-fixed-top" style="background-color: #0BE0FD">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">Admin Panel</a>
            </div>
            <div id="navbar" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="index.php">Dashboard</a></li>
                    <li><a href="properties.php">Properties</a></li>
                    <li><a href="messages.php">Messages</a></li>
                    <li><a href="newsletter.php">Newsletter</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container admin-panel">
        <h2>Dashboard</h2>
        <hr>
        
        <div class="row">
            <div class="col-md-4">
                <div class="dashboard-card">
                    <i class="glyphicon glyphicon-home"></i>
                    <h3>Properties</h3>
                    <p>Manage property listings</p>
                    <a href="properties.php" class="btn btn-primary">Manage</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card">
                    <i class="glyphicon glyphicon-envelope"></i>
                    <h3>Messages</h3>
                    <p>View contact messages</p>
                    <a href="messages.php" class="btn btn-primary">View Messages</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card">
                    <i class="glyphicon glyphicon-send"></i>
                    <h3>Newsletter</h3>
                    <p>Manage subscribers</p>
                    <a href="newsletter.php" class="btn btn-primary">Manage</a>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/jquery-1.9.1.min.js"></script>
    <script src="../assets/bootstrap/js/bootstrap.js"></script>
</body>
</html>
