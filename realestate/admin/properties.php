<?php
// Admin Properties Management
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Include database connection
include_once "../connection.php";

// Handle property deletion
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $query = "DELETE FROM properties WHERE property_id = $id";
    mysqli_query($con, $query);
    header('Location: properties.php?deleted=1');
    exit;
}

// Handle property status toggle
if (isset($_GET['toggle_status'])) {
    $id = intval($_GET['toggle_status']);
    $query = "UPDATE properties SET availablility = 1 - availablility WHERE property_id = $id";
    mysqli_query($con, $query);
    header('Location: properties.php');
    exit;
}

// Get all properties
$query = "SELECT * FROM properties ORDER BY property_id DESC";
$result = mysqli_query($con, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Properties - Admin Panel</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { padding-top: 60px; }
        .property-image { width: 80px; height: 60px; object-fit: cover; }
        .status-badge { padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; }
        .status-available { background-color: #d4edda; color: #155724; }
        .status-sold { background-color: #f8d7da; color: #721c24; }
        .action-btns .btn { margin-right: 5px; }
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
                    <li class="active"><a href="properties.php">Properties</a></li>
                    <li><a href="messages.php">Messages</a></li>
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
                <div class="pull-right" style="margin-bottom: 20px;">
                    <a href="property_edit.php" class="btn btn-success"><i class="fas fa-plus"></i> Add New Property</a>
                </div>
                <h2>Manage Properties</h2>
                <hr>
                
                <?php if (isset($_GET['deleted'])): ?>
                    <div class="alert alert-success">Property has been deleted successfully.</div>
                <?php endif; ?>
                
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Price</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($property = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $property['property_id']; ?></td>
                                <td>
                                    <img src="../<?php echo htmlspecialchars($property['property_img']); ?>" alt="Property" class="property-image">
                                </td>
                                <td><?php echo htmlspecialchars($property['property_title']); ?></td>
                                <td>$<?php echo number_format($property['price']); ?></td>
                                <td><?php echo htmlspecialchars($property['property_type']); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $property['availablility'] == 0 ? 'status-available' : 'status-sold'; ?>">
                                        <?php echo $property['availablility'] == 0 ? 'Available' : 'Sold'; ?>
                                    </span>
                                </td>
                                <td class="action-btns">
                                    <a href="property_edit.php?id=<?php echo $property['property_id']; ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="?toggle_status=<?php echo $property['property_id']; ?>" class="btn btn-<?php echo $property['availablility'] == 0 ? 'warning' : 'success'; ?> btn-sm">
                                        <i class="fas fa-<?php echo $property['availablility'] == 0 ? 'times' : 'check'; ?>"></i> 
                                        <?php echo $property['availablility'] == 0 ? 'Mark as Sold' : 'Mark as Available'; ?>
                                    </a>
                                    <a href="?delete_id=<?php echo $property['property_id']; ?>" class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Are you sure you want to delete this property?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/jquery-1.9.1.min.js"></script>
    <script src="../assets/bootstrap/js/bootstrap.js"></script>
</body>
</html>
