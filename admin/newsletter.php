<?php
// Admin Newsletter Subscribers Page
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Include database connection
include_once "../connection.php";

// Create or update newsletter_subscribers table
$table_check = mysqli_query($con, "SHOW TABLES LIKE 'newsletter_subscribers'");

if(mysqli_num_rows($table_check) == 0) {
    // Table doesn't exist, create it
    $create_table = "CREATE TABLE newsletter_subscribers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL,
        name VARCHAR(100) NULL,
        subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        is_active BOOLEAN DEFAULT 1,
        last_sent TIMESTAMP NULL,
        token VARCHAR(64) NULL,
        UNIQUE KEY unique_email (email)
    )";
    
    if (!mysqli_query($con, $create_table)) {
        die("Error creating table: " . mysqli_error($con));
    }
} else {
    // Table exists, check and add missing columns
    $columns_to_add = [
        'is_active' => [
            'type' => 'BOOLEAN',
            'default' => 'DEFAULT 1'
        ],
        'last_sent' => [
            'type' => 'TIMESTAMP',
            'default' => 'NULL'
        ],
        'token' => [
            'type' => 'VARCHAR(64)',
            'default' => 'NULL'
        ]
    ];
    
    foreach ($columns_to_add as $column => $def) {
        $check_column = mysqli_query($con, "SHOW COLUMNS FROM newsletter_subscribers LIKE '$column'");
        if(mysqli_num_rows($check_column) == 0) {
            $sql = "ALTER TABLE newsletter_subscribers ADD COLUMN $column {$def['type']} {$def['default']}";
            if (!mysqli_query($con, $sql)) {
                die("Error adding column $column: " . mysqli_error($con));
            }
        }
    }
    
    // Add unique constraint if it doesn't exist
    $check_index = mysqli_query($con, "SHOW INDEX FROM newsletter_subscribers WHERE Key_name = 'unique_email'");
    if(mysqli_num_rows($check_index) == 0) {
        if (!mysqli_query($con, "ALTER TABLE newsletter_subscribers ADD UNIQUE KEY unique_email (email)")) {
            die("Error adding unique constraint: " . mysqli_error($con));
        }
    }
}

// Handle subscriber deletion
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $query = "DELETE FROM newsletter_subscribers WHERE id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    header('Location: newsletter.php?deleted=1');
    exit;
}

// Toggle subscriber status
if (isset($_GET['toggle_status'])) {
    $id = intval($_GET['toggle_status']);
    $query = "UPDATE newsletter_subscribers SET is_active = 1 - is_active WHERE id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    header('Location: newsletter.php?updated=1');
    exit;
}

// Handle export to CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=newsletter_subscribers_' . date('Y-m-d') . '.csv');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Name', 'Email', 'Subscribed At', 'Status']);
    
    $query = "SELECT id, name, email, subscribed_at, is_active FROM newsletter_subscribers ORDER BY subscribed_at DESC";
    $result = mysqli_query($con, $query);
    
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            $row['id'],
            $row['name'],
            $row['email'],
            $row['subscribed_at'],
            $row['is_active'] ? 'Active' : 'Inactive'
        ]);
    }
    
    fclose($output);
    exit;
}

// Get all subscribers
$query = "SELECT * FROM newsletter_subscribers ORDER BY subscribed_at DESC";
$result = mysqli_query($con, $query);

// Check if query was successful
if ($result === false) {
    die("Error fetching subscribers: " . mysqli_error($con));
}

$total_subscribers = mysqli_num_rows($result);

// Reset result pointer to the beginning
mysqli_data_seek($result, 0);

// Get active subscribers count
$active_query = "SELECT COUNT(*) as count FROM newsletter_subscribers WHERE is_active = 1";
$active_result = mysqli_query($con, $active_query);

if ($active_result === false) {
    die("Error counting active subscribers: " . mysqli_error($con));
}

$active_row = mysqli_fetch_assoc($active_result);
$active_subscribers = $active_row['count'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Newsletter Subscribers - Admin Panel</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap.min.css">
    <style>
        body { padding-top: 60px; }
        .subscriber-active { color: #3c763d; }
        .subscriber-inactive { color: #a94442; }
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-active { background-color: #dff0d8; color: #3c763d; }
        .status-inactive { background-color: #f2dede; color: #a94442; }
        .stats-card {
            border-left: 4px solid #337ab7;
            padding: 15px;
            margin-bottom: 20px;
            background: #f9f9f9;
            border-radius: 0 4px 4px 0;
        }
        .stats-card h3 { margin-top: 0; }
        .stats-card .count { font-size: 24px; font-weight: bold; }
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
                    <li><a href="index.php">Dashboard</a></li>
                    <li><a href="properties.php">Properties</a></li>
                    <li><a href="messages.php">Messages</a></li>
                    <li class="active"><a href="newsletter.php">Newsletter</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Newsletter Subscribers</h2>
                <hr>
                
                <?php if (isset($_GET['updated'])): ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        Subscriber status updated successfully.
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['deleted'])): ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        Subscriber has been removed.
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="stats-card">
                            <h3>Total Subscribers</h3>
                            <div class="count"><?php echo $total_subscribers; ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="stats-card">
                            <h3>Active Subscribers</h3>
                            <div class="count"><?php echo $active_subscribers; ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="panel panel-default">
                    <div class="panel-heading clearfix">
                        <div class="pull-right">
                            <a href="?export=csv" class="btn btn-success">
                                <i class="fas fa-file-export"></i> Export to CSV
                            </a>
                        </div>
                    </div>
                    <div class="panel-body">
                        <?php if ($total_subscribers > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" id="subscribersTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Subscribed On</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($subscriber = mysqli_fetch_assoc($result)): ?>
                                            <tr>
                                                <td><?php echo $subscriber['id']; ?></td>
                                                <td><?php echo htmlspecialchars($subscriber['name'] ?? 'N/A'); ?></td>
                                                <td>
                                                    <a href="mailto:<?php echo htmlspecialchars($subscriber['email']); ?>">
                                                        <?php echo htmlspecialchars($subscriber['email']); ?>
                                                    </a>
                                                </td>
                                                <td><?php echo date('M j, Y', strtotime($subscriber['subscribed_at'])); ?></td>
                                                <td>
                                                    <span class="status-badge status-<?php echo $subscriber['is_active'] ? 'active' : 'inactive'; ?>">
                                                        <?php echo $subscriber['is_active'] ? 'Active' : 'Inactive'; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="?toggle_status=<?php echo $subscriber['id']; ?>" 
                                                       class="btn btn-xs btn-<?php echo $subscriber['is_active'] ? 'warning' : 'success'; ?>"
                                                       title="<?php echo $subscriber['is_active'] ? 'Deactivate' : 'Activate'; ?>"
                                                       onclick="return confirm('Are you sure you want to <?php echo $subscriber['is_active'] ? 'deactivate' : 'activate'; ?> this subscriber?')">
                                                        <i class="fas fa-<?php echo $subscriber['is_active'] ? 'times' : 'check'; ?>"></i>
                                                    </a>
                                                    <a href="?delete_id=<?php echo $subscriber['id']; ?>" 
                                                       class="btn btn-xs btn-danger"
                                                       title="Delete"
                                                       onclick="return confirm('Are you sure you want to delete this subscriber?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                No newsletter subscribers found.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/jquery-1.9.1.min.js"></script>
    <script src="../assets/bootstrap/js/bootstrap.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#subscribersTable').DataTable({
                "order": [[0, "desc"]],
                "pageLength": 25,
                "responsive": true
            });
        });
    </script>
</body>
</html>
