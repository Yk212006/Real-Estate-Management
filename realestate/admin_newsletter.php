<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "connection.php";

// No login required for admin access

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin_newsletter.php');
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Newsletter Subscribers - Admin</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.css" />
    <script src="assets/jquery-1.9.1.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.js"></script>
    <style>
        body { padding-top: 20px; padding-bottom: 20px; }
        .subscriber-card { margin-bottom: 15px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .subscriber-card.active { border-left: 4px solid #5cb85c; }
        .subscriber-card.unsubscribed { border-left: 4px solid #d9534f; }
        .stats { margin-bottom: 20px; }
        .stat-box { padding: 20px; background: #f5f5f5; border-radius: 5px; text-align: center; }
        .stat-number { font-size: 36px; font-weight: bold; color: #5cb85c; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="pull-right">
                    <a href="admin_properties.php" class="btn btn-default">Manage Properties</a>
                    <a href="admin_view_messages.php" class="btn btn-default">View Messages</a>
                    <a href="index.php" class="btn btn-default">Back to Site</a>
                </div>
                <h2>Newsletter Subscribers</h2>
                <hr>
            </div>
        </div>
        <div class="row stats">
            <div class="col-md-4">
                <div class="stat-box">
                    <div class="stat-number"><?php echo $total_subscribers; ?></div>
                    <div>Active Subscribers</div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="btn-group" role="group">
                    <a href="?filter=all" class="btn btn-default <?php echo $filter === 'all' ? 'active' : ''; ?>">
                        All
                    </a>
                    <a href="?filter=active" class="btn btn-success <?php echo $filter === 'active' ? 'active' : ''; ?>">
                        Active
                    </a>
                    <a href="?filter=unsubscribed" class="btn btn-danger <?php echo $filter === 'unsubscribed' ? 'active' : ''; ?>">
                        Unsubscribed
                    </a>
                </div>
                <a href="#" class="btn btn-info pull-right" onclick="exportEmails(); return false;">Export Emails</a>
                <hr>
            </div>
        </div>
        
        <?php if (mysqli_num_rows($result) === 0): ?>
            <div class="alert alert-info">No subscribers found.</div>
        <?php else: ?>
            <?php while($subscriber = mysqli_fetch_assoc($result)): ?>
                <div class="subscriber-card <?php echo $subscriber['status']; ?>">
                    <div class="row">
                        <div class="col-md-8">
                            <h4>
                                <?php echo htmlspecialchars($subscriber['email']); ?>
                                <span class="label label-<?php echo $subscriber['status'] === 'active' ? 'success' : 'danger'; ?>">
                                    <?php echo ucfirst($subscriber['status']); ?>
                                </span>
                            </h4>
                            <p>
                                <strong>Subscribed:</strong> <?php echo date('F j, Y g:i A', strtotime($subscriber['subscribed_at'])); ?>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <form method="post" style="margin-bottom: 10px;">
                                <input type="hidden" name="subscriber_id" value="<?php echo $subscriber['id']; ?>">
                                <div class="form-group">
                                    <select name="status" class="form-control input-sm">
                                        <option value="active" <?php echo $subscriber['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="unsubscribed" <?php echo $subscriber['status'] === 'unsubscribed' ? 'selected' : ''; ?>>Unsubscribed</option>
                                    </select>
                                </div>
                                <button type="submit" name="update_status" class="btn btn-primary btn-sm">Update</button>
                            </form>
                            
                            <form method="post" onsubmit="return confirm('Delete this subscriber?');">
                                <input type="hidden" name="subscriber_id" value="<?php echo $subscriber['id']; ?>">
                                <button type="submit" name="delete_subscriber" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
    
    <script>
    function exportEmails() {
        // Get all active email addresses
        var emails = [];
        <?php
        mysqli_data_seek($result, 0);
        while($sub = mysqli_fetch_assoc($result)) {
            if ($sub['status'] === 'active') {
                echo "emails.push('" . addslashes($sub['email']) . "');\n";
            }
        }
        ?>
        
        if (emails.length === 0) {
            alert('No active subscribers to export.');
            return;
        }
        
        // Create downloadable text
        var text = emails.join('\n');
        var element = document.createElement('a');
        element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
        element.setAttribute('download', 'newsletter_subscribers_' + new Date().toISOString().split('T')[0] + '.txt');
        element.style.display = 'none';
        document.body.appendChild(element);
        element.click();
        document.body.removeChild(element);
    }
    </script>
</body>
</html>
