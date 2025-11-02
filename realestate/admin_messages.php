<?php
// Simple admin page to view contact_log.txt
$config = [];
$configFile = __DIR__ . '/contact_config.php';
if (file_exists($configFile)) {
    $config = include $configFile;
}

// Basic HTTP auth
$user = $config['admin_user'] ?? 'admin';
$pass = $config['admin_pass'] ?? 'changeme';

if (!isset($_SERVER['PHP_AUTH_USER']) || !($_SERVER['PHP_AUTH_USER'] === $user && $_SERVER['PHP_AUTH_PW'] === $pass)) {
    header('WWW-Authenticate: Basic realm="Messages"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Authentication required.';
    exit;
}

$logfile = __DIR__ . '/contact_log.txt';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'clear') {
    file_put_contents($logfile, '');
}

$entries = '';
if (file_exists($logfile)) {
    $entries = file_get_contents($logfile);
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contact Messages</title>
    <style>body{font-family:Arial,Helvetica,sans-serif; padding:20px;} pre{white-space:pre-wrap; word-wrap:break-word; background:#f4f4f4; padding:12px; border:1px solid #ddd;}</style>
</head>
<body>
    <h1>Contact Messages</h1>
    <form method="post" onsubmit="return confirm('Clear all messages?');">
        <button type="submit" name="action" value="clear">Clear Messages</button>
    </form>
    <p>Log file: <code><?php echo htmlspecialchars($logfile); ?></code></p>
    <h2>Messages</h2>
    <?php if ($entries === ''): ?>
        <p><em>No messages logged yet.</em></p>
    <?php else: ?>
        <pre><?php echo htmlspecialchars($entries); ?></pre>
    <?php endif; ?>
</body>
</html>
