<?php
// Admin page to view contact messages
ini_set('display_errors', 1);

// Include database connection
include_once "connection.php";

// No login required for admin access

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin_view_messages.php');
}

// Handle status update
if (isset($_POST['update_status'])) {
    $id = intval($_POST['message_id']);
    $status = $_POST['status'];
    $stmt = mysqli_prepare($con, "UPDATE contact_messages SET status = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $status, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// Handle delete
if (isset($_POST['delete_message'])) {
    $id = intval($_POST['message_id']);
    $stmt = mysqli_prepare($con, "DELETE FROM contact_messages WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// Get filter
$filter = $_GET['filter'] ?? 'all';
$where = "";
if ($filter === 'new') {
    $where = "WHERE status = 'new'";
} elseif ($filter === 'read') {
    $where = "WHERE status = 'read'";
} elseif ($filter === 'replied') {
    $where = "WHERE status = 'replied'";
}

// Fetch messages
$query = "SELECT * FROM contact_messages $where ORDER BY created_at DESC";
$result = mysqli_query($con, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Contact Messages - Admin</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.css" />
    <script src="assets/jquery-1.9.1.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.js"></script>
    <style>
        body { padding-top: 20px; padding-bottom: 20px; }
        .message-card { margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .message-card.new { border-left: 4px solid #5cb85c; }
        .message-card.read { border-left: 4px solid #5bc0de; }
        .message-card.replied { border-left: 4px solid #f0ad4e; }
        .badge { margin-left: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="pull-right">
                    <a href="admin_properties.php" class="btn btn-default">Manage Properties</a>
                    <a href="admin_newsletter.php" class="btn btn-default">Newsletter</a>
                    <a href="index.php" class="btn btn-default">Back to Site</a>
                </div>
                <h2>Contact Messages</h2>
                <hr>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="btn-group" role="group">
                    <a href="?filter=all" class="btn btn-default <?php echo $filter === 'all' ? 'active' : ''; ?>">
                        All Messages
                    </a>
                    <a href="?filter=new" class="btn btn-success <?php echo $filter === 'new' ? 'active' : ''; ?>">
                        New
                    </a>
                    <a href="?filter=read" class="btn btn-info <?php echo $filter === 'read' ? 'active' : ''; ?>">
                        Read
                    </a>
                    <a href="?filter=replied" class="btn btn-warning <?php echo $filter === 'replied' ? 'active' : ''; ?>">
                        Replied
                    </a>
                </div>
                <hr>
            </div>
        </div>
        
        <?php if (mysqli_num_rows($result) === 0): ?>
            <div class="alert alert-info">No messages found.</div>
        <?php else: ?>
            <?php while($message = mysqli_fetch_assoc($result)): ?>
                <div class="message-card <?php echo $message['status']; ?>">
                    <div class="row">
                        <div class="col-md-8">
                            <h4>
                                <?php echo htmlspecialchars($message['fullname']); ?>
                                <span class="badge badge-<?php 
                                    echo $message['status'] === 'new' ? 'success' : 
                                         ($message['status'] === 'read' ? 'info' : 'warning'); 
                                ?>">
                                    <?php echo ucfirst($message['status']); ?>
                                </span>
                            </h4>
                            <p>
                                <strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>">
                                    <?php echo htmlspecialchars($message['email']); ?>
                                </a><br>
                                <?php if ($message['phone']): ?>
                                    <strong>Phone:</strong> <?php echo htmlspecialchars($message['phone']); ?><br>
                                <?php endif; ?>
                                <strong>Date:</strong> <?php echo date('F j, Y g:i A', strtotime($message['created_at'])); ?>
                            </p>
                            <div class="well well-sm">
                                <strong>Message:</strong><br>
                                <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <form method="post" style="margin-bottom: 10px;">
                                <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                <div class="form-group">
                                    <label>Update Status:</label>
                                    <select name="status" class="form-control">
                                        <option value="new" <?php echo $message['status'] === 'new' ? 'selected' : ''; ?>>New</option>
                                        <option value="read" <?php echo $message['status'] === 'read' ? 'selected' : ''; ?>>Read</option>
                                        <option value="replied" <?php echo $message['status'] === 'replied' ? 'selected' : ''; ?>>Replied</option>
                                    </select>
                                </div>
                                <button type="submit" name="update_status" class="btn btn-primary btn-sm">Update Status</button>
                            </form>
                            
                            <form method="post" onsubmit="return confirm('Are you sure you want to delete this message?');">
                                <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                <button type="submit" name="delete_message" class="btn btn-danger btn-sm">Delete Message</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</body>
</html>
