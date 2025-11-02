<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session for messages
session_start();

// Include database connection
require_once('connection.php');

// Redirect to login if not admin
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin/login.php');
    exit();
}

// Function to sync with admin properties
function syncWithAdminProperties($con) {
    // Get current availability from admin
    $query = "SELECT property_id, availablility FROM properties";
    $result = mysqli_query($con, $query);
    
    $availability = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // In admin panel, 0 = Available, 1 = Sold
        // We'll keep the same values for consistency
        $availability[$row['property_id']] = $row['availablility'];
    }
    
    return $availability;
}

// Get current availability
$propertyAvailability = syncWithAdminProperties($con);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_availability'])) {
    mysqli_begin_transaction($con);
    
    try {
        // In admin panel: 0 = Available, 1 = Sold
        // Reset all to available first (0)
        $reset = mysqli_query($con, "UPDATE properties SET availablility = 0");
        if (!$reset) throw new Exception("Error resetting availability");
        
        // Mark selected as sold (1)
        if (!empty($_POST['property_ids'])) {
            $ids = array_map('intval', $_POST['property_ids']);
            $ids_str = implode(',', $ids);
            // Set to 0 for available (following admin panel's logic)
            $update = mysqli_query($con, "UPDATE properties SET availablility = 0 WHERE property_id IN ($ids_str)");
            if (!$update) throw new Exception("Error updating availability");
            
            // Update session message
            $_SESSION['success'] = "Successfully updated availability for " . count($ids) . " properties.";
        } else {
            $_SESSION['info'] = "No properties were selected. All properties are now marked as available.";
        }
        
        mysqli_commit($con);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
        
    } catch (Exception $e) {
        mysqli_rollback($con);
        $error = $e->getMessage();
    }
}

// Get all properties
$query = "SELECT * FROM properties ORDER BY property_id";
$result = mysqli_query($con, $query);
if (!$result) {
    die("Error: " . mysqli_error($con));
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Property Availability Manager</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .property { 
            padding: 15px; 
            margin: 10px 0; 
            border: 1px solid #ddd; 
            background: #f9f9f9;
            border-left: 5px solid #ddd;
        }
        .available { border-left-color: #5cb85c; }
        .status { 
            display: inline-block; 
            padding: 3px 8px; 
            border-radius: 3px; 
            color: white;
            font-size: 0.9em;
            margin-left: 10px;
        }
        .status-available { background: #5cb85c; }
        .status-sold { background: #d9534f; }
        button { 
            background: #337ab7; 
            color: white; 
            border: none; 
            padding: 8px 15px; 
            cursor: pointer; 
            margin: 10px 0;
        }
        .admin-link {
            display: inline-block;
            margin: 10px 0;
            padding: 8px 15px;
            background: #5bc0de;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .admin-link:hover {
            background: #46b8da;
        }
    </style>
</head>
<body>
    <h1>Property Availability Manager</h1>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div style="background: #dff0d8; color: #3c763d; padding: 10px; margin: 10px 0;">
            <?php 
            echo $_SESSION['success']; 
            unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['info'])): ?>
        <div style="background: #d9edf7; color: #31708f; padding: 10px; margin: 10px 0;">
            <?php 
            echo $_SESSION['info']; 
            unset($_SESSION['info']);
            ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div style="background: #f2dede; color: #a94442; padding: 10px; margin: 10px 0;">
            Error: <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <a href="admin/properties.php" class="admin-link">Manage Properties in Admin Panel</a>
    
    <form method="post" action="">
        <?php while ($property = mysqli_fetch_assoc($result)): 
            $is_available = $propertyAvailability[$property['property_id']] ?? 0;
        ?>
            <div class="property <?php echo $is_available ? 'available' : ''; ?>">
                <h3><?php echo htmlspecialchars($property['property_title']); ?></h3>
                <p>
                    ID: <?php echo $property['property_id']; ?>
                    <span class="status status-<?php echo $is_available == 0 ? 'available' : 'sold'; ?>">
                        <?php echo $is_available == 0 ? 'Available' : 'Sold'; ?>
                    </span>
                </p>
                <label>
                    <input type="checkbox" 
                           name="property_ids[]" 
                           value="<?php echo $property['property_id']; ?>"
                           <?php echo $is_available == 0 ? 'checked' : ''; ?>>
                    Mark as Available
                </label>
            </div>
        <?php endwhile; ?>
        
        <div>
            <button type="submit" name="update_availability">Update Availability</button>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" style="margin-left: 10px;">Refresh</a>
        </div>
    </form>
</body>
</html>