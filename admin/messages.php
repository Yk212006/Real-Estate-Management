<?php
// Admin Messages Page
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Include database connection
include_once "../connection.php";

// Handle message status update
if (isset($_POST['update_status'])) {
    $message_id = intval($_POST['message_id']);
    $status = $_POST['status'];
    
    $query = "UPDATE contact_messages SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "si", $status, $message_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    header('Location: messages.php?updated=1');
    exit;
}

// Handle message deletion
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $query = "DELETE FROM contact_messages WHERE id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    header('Location: messages.php?deleted=1');
    exit;
}

// Get filter parameter
$filter = $_GET['filter'] ?? 'all';
$where_clause = '';
if (in_array($filter, ['new', 'read', 'replied'])) {
    $where_clause = "WHERE status = '$filter'";
}

// Get messages from database
$query = "SELECT * FROM contact_messages $where_clause ORDER BY created_at DESC";
$result = mysqli_query($con, $query);
$total_messages = mysqli_num_rows($result);

// Get counts for each status
$status_counts = [
    'all' => mysqli_num_rows(mysqli_query($con, "SELECT id FROM contact_messages")),
    'new' => mysqli_num_rows(mysqli_query($con, "SELECT id FROM contact_messages WHERE status = 'new'")),
    'read' => mysqli_num_rows(mysqli_query($con, "SELECT id FROM contact_messages WHERE status = 'read'")),
    'replied' => mysqli_num_rows(mysqli_query($con, "SELECT id FROM contact_messages WHERE status = 'replied'"))
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Messages - Admin Panel</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { padding-top: 60px; }
        .message-card { 
            margin-bottom: 20px; 
            border-left: 4px solid #ddd;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 0 4px 4px 0;
        }
        .message-card.new { border-left-color: #5bc0de; }
        .message-card.read { border-left-color: #5cb85c; }
        .message-card.replied { border-left-color: #5cb85c; }
        .message-meta { 
            color: #777; 
            font-size: 0.9em;
            margin-bottom: 10px;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-new { background-color: #d9edf7; color: #31708f; }
        .status-read { background-color: #dff0d8; color: #3c763d; }
        .status-replied { background-color: #d9f7d9; color: #3c763d; }
        .filter-buttons { margin-bottom: 20px; }
        .no-messages { 
            padding: 40px;
            text-align: center;
            color: #777;
            font-size: 1.2em;
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
                    <li><a href="index.php">Dashboard</a></li>
                    <li><a href="properties.php">Properties</a></li>
                    <li class="active"><a href="messages.php">Messages</a></li>
                    <li><a href="newsletter.php">Newsletter</a></li>
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
                <h2>Contact Messages</h2>
                <hr>
                
                <?php if (isset($_GET['updated'])): ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        Message status updated successfully.
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['deleted'])): ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        Message has been deleted.
                    </div>
                <?php endif; ?>
                
                <div class="filter-buttons btn-group" role="group">
                    <a href="?filter=all" class="btn btn-default <?php echo $filter === 'all' ? 'active' : ''; ?>">
                        All <span class="badge"><?php echo $status_counts['all']; ?></span>
                    </a>
                    <a href="?filter=new" class="btn btn-default <?php echo $filter === 'new' ? 'active' : ''; ?>">
                        New <span class="badge"><?php echo $status_counts['new']; ?></span>
                    </a>
                    <a href="?filter=read" class="btn btn-default <?php echo $filter === 'read' ? 'active' : ''; ?>">
                        Read <span class="badge"><?php echo $status_counts['read']; ?></span>
                    </a>
                    <a href="?filter=replied" class="btn btn-default <?php echo $filter === 'replied' ? 'active' : ''; ?>">
                        Replied <span class="badge"><?php echo $status_counts['replied']; ?></span>
                    </a>
                </div>
                
                <?php if ($total_messages > 0): ?>
                    <?php while ($message = mysqli_fetch_assoc($result)): ?>
                        <div class="message-card <?php echo $message['status']; ?>">
                            <div class="row">
                                <div class="col-md-8">
                                    <h4><?php echo htmlspecialchars($message['fullname']); ?></h4>
                                    <div class="message-meta">
                                        <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($message['email']); ?> | 
                                        <i class="fas fa-phone"></i> <?php echo $message['phone'] ? htmlspecialchars($message['phone']) : 'N/A'; ?> | 
                                        <i class="far fa-clock"></i> <?php echo date('M j, Y g:i A', strtotime($message['created_at'])); ?>
                                        <span class="status-badge status-<?php echo $message['status']; ?>">
                                            <?php echo ucfirst($message['status']); ?>
                                        </span>
                                    </div>
                                    <p><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
                                </div>
                                <div class="col-md-4 text-right">
                                    <form method="post" style="display: inline-block;">
                                        <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                        <select name="status" class="form-control input-sm" onchange="this.form.submit()" style="width: auto; display: inline-block; margin-bottom: 10px;">
                                            <option value="new" <?php echo $message['status'] === 'new' ? 'selected' : ''; ?>>New</option>
                                            <option value="read" <?php echo $message['status'] === 'read' ? 'selected' : ''; ?>>Read</option>
                                            <option value="replied" <?php echo $message['status'] === 'replied' ? 'selected' : ''; ?>>Replied</option>
                                        </select>
                                        <input type="hidden" name="update_status" value="1">
                                    </form>
                                    <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>?subject=Re: Your Message" class="btn btn-primary btn-sm">
                                        <i class="fas fa-reply"></i> Reply
                                    </a>
                                    <a href="?delete_id=<?php echo $message['id']; ?>" class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Are you sure you want to delete this message?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-messages">
                        <i class="far fa-envelope-open fa-3x" style="opacity: 0.5; margin-bottom: 20px;"></i>
                        <p>No messages found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="../assets/jquery-1.9.1.min.js"></script>
    <script src="../assets/bootstrap/js/bootstrap.js"></script>
</body>
</html>
