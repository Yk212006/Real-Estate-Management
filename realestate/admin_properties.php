<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "connection.php";

// No login/logout functionality needed

$success_message = '';
$error_message = '';

// Handle property addition
if (isset($_POST['add_property'])) {
    $title = mysqli_real_escape_string($con, $_POST['property_title']);
    $details = mysqli_real_escape_string($con, $_POST['property_details']);
    $delivery_type = mysqli_real_escape_string($con, $_POST['delivery_type']);
    $availability = intval($_POST['availability']);
    $price = floatval($_POST['price']);
    $address = mysqli_real_escape_string($con, $_POST['property_address']);
    $bed_room = intval($_POST['bed_room']);
    $liv_room = intval($_POST['liv_room']);
    $parking = intval($_POST['parking']);
    $kitchen = intval($_POST['kitchen']);
    $utility = mysqli_real_escape_string($con, $_POST['utility']);
    $property_type = mysqli_real_escape_string($con, $_POST['property_type']);
    $floor_space = mysqli_real_escape_string($con, $_POST['floor_space']);
    $agent_id = intval($_POST['agent_id']);
    
    // Handle image upload
    $property_img = 'images/properties/default.jpg';
    if (isset($_FILES['property_img']) && $_FILES['property_img']['error'] == 0) {
        $upload_dir = 'images/properties/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_ext = strtolower(pathinfo($_FILES['property_img']['name'], PATHINFO_EXTENSION));
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        
        if (in_array($file_ext, $allowed)) {
            $new_filename = 'property_' . time() . '.' . $file_ext;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['property_img']['tmp_name'], $upload_path)) {
                $property_img = $upload_path;
            }
        }
    }
    
    $query = "INSERT INTO properties (property_title, property_details, delivery_type, availablility, price, property_address, property_img, bed_room, liv_room, parking, kitchen, utility, property_type, floor_space, agent_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "sssidssiiiisssi", $title, $details, $delivery_type, $availability, $price, $address, $property_img, $bed_room, $liv_room, $parking, $kitchen, $utility, $property_type, $floor_space, $agent_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $success_message = "Property added successfully!";
    } else {
        $error_message = "Failed to add property: " . mysqli_error($con);
    }
    mysqli_stmt_close($stmt);
}

// Handle property update
if (isset($_POST['update_property'])) {
    $property_id = intval($_POST['property_id']);
    $title = mysqli_real_escape_string($con, $_POST['property_title']);
    $details = mysqli_real_escape_string($con, $_POST['property_details']);
    $delivery_type = mysqli_real_escape_string($con, $_POST['delivery_type']);
    $availability = intval($_POST['availability']);
    $price = floatval($_POST['price']);
    $address = mysqli_real_escape_string($con, $_POST['property_address']);
    $bed_room = intval($_POST['bed_room']);
    $liv_room = intval($_POST['liv_room']);
    $parking = intval($_POST['parking']);
    $kitchen = intval($_POST['kitchen']);
    $utility = mysqli_real_escape_string($con, $_POST['utility']);
    $property_type = mysqli_real_escape_string($con, $_POST['property_type']);
    $floor_space = mysqli_real_escape_string($con, $_POST['floor_space']);
    $agent_id = intval($_POST['agent_id']);
    
    // Handle image upload
    $property_img = $_POST['current_img'];
    if (isset($_FILES['property_img']) && $_FILES['property_img']['error'] == 0) {
        $upload_dir = 'images/properties/';
        $file_ext = strtolower(pathinfo($_FILES['property_img']['name'], PATHINFO_EXTENSION));
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        
        if (in_array($file_ext, $allowed)) {
            $new_filename = 'property_' . time() . '.' . $file_ext;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['property_img']['tmp_name'], $upload_path)) {
                $property_img = $upload_path;
            }
        }
    }
    
    $query = "UPDATE properties SET property_title=?, property_details=?, delivery_type=?, availablility=?, price=?, property_address=?, property_img=?, bed_room=?, liv_room=?, parking=?, kitchen=?, utility=?, property_type=?, floor_space=?, agent_id=? WHERE property_id=?";
    
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "sssidssiiiisssii", $title, $details, $delivery_type, $availability, $price, $address, $property_img, $bed_room, $liv_room, $parking, $kitchen, $utility, $property_type, $floor_space, $agent_id, $property_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $success_message = "Property updated successfully!";
    } else {
        $error_message = "Failed to update property: " . mysqli_error($con);
    }
    mysqli_stmt_close($stmt);
}

// Handle availability toggle
if (isset($_POST['toggle_availability'])) {
    $property_id = intval($_POST['property_id']);
    $new_status = intval($_POST['new_status']);
    
    $stmt = mysqli_prepare($con, "UPDATE properties SET availablility = ? WHERE property_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $new_status, $property_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $success_message = "Property status updated!";
    }
    mysqli_stmt_close($stmt);
}

// Handle delete
if (isset($_POST['delete_property'])) {
    $property_id = intval($_POST['property_id']);
    $stmt = mysqli_prepare($con, "DELETE FROM properties WHERE property_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $property_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $success_message = "Property deleted successfully!";
    }
    mysqli_stmt_close($stmt);
}

// Get filter
$filter = $_GET['filter'] ?? 'all';
$where = "";
if ($filter === 'available') {
    $where = "WHERE availablility = 0";
} elseif ($filter === 'sold') {
    $where = "WHERE availablility = 1";
} elseif ($filter === 'sale') {
    $where = "WHERE delivery_type = 'Sale'";
} elseif ($filter === 'rent') {
    $where = "WHERE delivery_type = 'Rent'";
}

// Fetch properties
$query = "SELECT * FROM properties $where ORDER BY property_id DESC";
$result = mysqli_query($con, $query);

// Get agents for dropdown
$agents_query = "SELECT * FROM agent ORDER BY agent_name";
$agents_result = mysqli_query($con, $agents_query);
$agents = [];
while ($agent = mysqli_fetch_assoc($agents_result)) {
    $agents[] = $agent;
}

// Get property being edited
$edit_property = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_query = "SELECT * FROM properties WHERE property_id = $edit_id";
    $edit_result = mysqli_query($con, $edit_query);
    $edit_property = mysqli_fetch_assoc($edit_result);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Properties - Admin</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.css" />
    <script src="assets/jquery-1.9.1.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.js"></script>
    <style>
        body { padding-top: 20px; padding-bottom: 20px; }
        .property-card { margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .property-card.available { border-left: 4px solid #5cb85c; }
        .property-card.sold { border-left: 4px solid #d9534f; }
        .property-img { width: 100%; max-width: 200px; height: auto; }
        .form-section { background: #f9f9f9; padding: 20px; border-radius: 5px; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="pull-right">
                    <a href="admin_view_messages.php" class="btn btn-default btn-sm">Contact Messages</a>
                    <a href="admin_newsletter.php" class="btn btn-default btn-sm">Newsletter</a>
                    <a href="index.php" class="btn btn-default btn-sm">View Site</a>
                </div>
                <h2>Manage Properties</h2>
                <hr>
            </div>
        </div>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <!-- Add/Edit Property Form -->
        <div class="form-section">
            <h3><?php echo $edit_property ? 'Edit Property' : 'Add New Property'; ?></h3>
            <form method="post" enctype="multipart/form-data">
                <?php if ($edit_property): ?>
                    <input type="hidden" name="property_id" value="<?php echo $edit_property['property_id']; ?>">
                    <input type="hidden" name="current_img" value="<?php echo $edit_property['property_img']; ?>">
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Property Title *</label>
                            <input type="text" name="property_title" class="form-control" value="<?php echo $edit_property['property_title'] ?? ''; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Price *</label>
                            <input type="number" name="price" class="form-control" value="<?php echo $edit_property['price'] ?? ''; ?>" step="0.01" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Status *</label>
                            <select name="availability" class="form-control" required>
                                <option value="0" <?php echo (isset($edit_property) && $edit_property['availablility'] == 0) ? 'selected' : ''; ?>>Available</option>
                                <option value="1" <?php echo (isset($edit_property) && $edit_property['availablility'] == 1) ? 'selected' : ''; ?>>Sold/Not Available</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Type *</label>
                            <select name="delivery_type" class="form-control" required>
                                <option value="Sale" <?php echo (isset($edit_property) && $edit_property['delivery_type'] == 'Sale') ? 'selected' : ''; ?>>For Sale</option>
                                <option value="Rent" <?php echo (isset($edit_property) && $edit_property['delivery_type'] == 'Rent') ? 'selected' : ''; ?>>For Rent</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Property Type *</label>
                            <select name="property_type" class="form-control" required>
                                <option value="Apartment" <?php echo (isset($edit_property) && $edit_property['property_type'] == 'Apartment') ? 'selected' : ''; ?>>Apartment</option>
                                <option value="Building" <?php echo (isset($edit_property) && $edit_property['property_type'] == 'Building') ? 'selected' : ''; ?>>Building</option>
                                <option value="Office-Space" <?php echo (isset($edit_property) && $edit_property['property_type'] == 'Office-Space') ? 'selected' : ''; ?>>Office-Space</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Agent</label>
                            <select name="agent_id" class="form-control">
                                <option value="1">Select Agent</option>
                                <?php foreach ($agents as $agent): ?>
                                    <option value="<?php echo $agent['agent_id']; ?>" <?php echo (isset($edit_property) && $edit_property['agent_id'] == $agent['agent_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($agent['agent_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Property Address *</label>
                    <input type="text" name="property_address" class="form-control" value="<?php echo $edit_property['property_address'] ?? ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Property Details *</label>
                    <textarea name="property_details" class="form-control" rows="4" required><?php echo $edit_property['property_details'] ?? ''; ?></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Bedrooms</label>
                            <input type="number" name="bed_room" class="form-control" value="<?php echo $edit_property['bed_room'] ?? 0; ?>" min="0">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Living Rooms</label>
                            <input type="number" name="liv_room" class="form-control" value="<?php echo $edit_property['liv_room'] ?? 0; ?>" min="0">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Parking</label>
                            <input type="number" name="parking" class="form-control" value="<?php echo $edit_property['parking'] ?? 0; ?>" min="0">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Kitchen</label>
                            <input type="number" name="kitchen" class="form-control" value="<?php echo $edit_property['kitchen'] ?? 0; ?>" min="0">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Floor Space</label>
                            <input type="text" name="floor_space" class="form-control" value="<?php echo $edit_property['floor_space'] ?? ''; ?>" placeholder="e.g. 1600 X 1400">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Utilities</label>
                            <input type="text" name="utility" class="form-control" value="<?php echo $edit_property['utility'] ?? ''; ?>" placeholder="e.g. Electricity, Gas, Water">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Property Image</label>
                            <input type="file" name="property_img" class="form-control" accept="image/*">
                            <?php if ($edit_property && $edit_property['property_img']): ?>
                                <small class="text-muted">Current: <?php echo basename($edit_property['property_img']); ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <?php if ($edit_property): ?>
                        <button type="submit" name="update_property" class="btn btn-primary">Update Property</button>
                        <a href="admin_properties.php" class="btn btn-default">Cancel</a>
                    <?php else: ?>
                        <button type="submit" name="add_property" class="btn btn-success">Add Property</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <!-- Filter -->
        <div class="row">
            <div class="col-md-12">
                <div class="btn-group" role="group">
                    <a href="?filter=all" class="btn btn-default <?php echo $filter === 'all' ? 'active' : ''; ?>">All</a>
                    <a href="?filter=available" class="btn btn-success <?php echo $filter === 'available' ? 'active' : ''; ?>">Available</a>
                    <a href="?filter=sold" class="btn btn-danger <?php echo $filter === 'sold' ? 'active' : ''; ?>">Sold</a>
                    <a href="?filter=sale" class="btn btn-info <?php echo $filter === 'sale' ? 'active' : ''; ?>">For Sale</a>
                    <a href="?filter=rent" class="btn btn-warning <?php echo $filter === 'rent' ? 'active' : ''; ?>">For Rent</a>
                </div>
                <hr>
            </div>
        </div>
        
        <!-- Properties List -->
        <h3>All Properties</h3>
        <?php if (mysqli_num_rows($result) === 0): ?>
            <div class="alert alert-info">No properties found.</div>
        <?php else: ?>
            <?php while($property = mysqli_fetch_assoc($result)): ?>
                <div class="property-card <?php echo $property['availablility'] == 0 ? 'available' : 'sold'; ?>">
                    <div class="row">
                        <div class="col-md-2">
                            <?php if ($property['property_img']): ?>
                                <img src="<?php echo $property['property_img']; ?>" class="property-img" alt="Property">
                            <?php endif; ?>
                        </div>
                        <div class="col-md-7">
                            <h4>
                                <?php echo htmlspecialchars($property['property_title']); ?>
                                <span class="label label-<?php echo $property['availablility'] == 0 ? 'success' : 'danger'; ?>">
                                    <?php echo $property['availablility'] == 0 ? 'Available' : 'Sold/Not Available'; ?>
                                </span>
                                <span class="label label-info"><?php echo $property['delivery_type']; ?></span>
                            </h4>
                            <p><strong>Price:</strong> $<?php echo number_format($property['price'], 2); ?></p>
                            <p><strong>Address:</strong> <?php echo htmlspecialchars($property['property_address']); ?></p>
                            <p><strong>Type:</strong> <?php echo $property['property_type']; ?> | 
                               <strong>Beds:</strong> <?php echo $property['bed_room']; ?> | 
                               <strong>Living:</strong> <?php echo $property['liv_room']; ?> | 
                               <strong>Parking:</strong> <?php echo $property['parking']; ?></p>
                        </div>
                        <div class="col-md-3">
                            <form method="post" style="margin-bottom: 10px;">
                                <input type="hidden" name="property_id" value="<?php echo $property['property_id']; ?>">
                                <input type="hidden" name="new_status" value="<?php echo $property['availablility'] == 0 ? 1 : 0; ?>">
                                <button type="submit" name="toggle_availability" class="btn btn-<?php echo $property['availablility'] == 0 ? 'warning' : 'success'; ?> btn-sm btn-block">
                                    Mark as <?php echo $property['availablility'] == 0 ? 'Sold' : 'Available'; ?>
                                </button>
                            </form>
                            
                            <a href="?edit=<?php echo $property['property_id']; ?>" class="btn btn-primary btn-sm btn-block">Edit</a>
                            <a href="property-detail.php?id=<?php echo $property['property_id']; ?>" target="_blank" class="btn btn-info btn-sm btn-block">View</a>
                            
                            <form method="post" onsubmit="return confirm('Delete this property?');" style="margin-top: 10px;">
                                <input type="hidden" name="property_id" value="<?php echo $property['property_id']; ?>">
                                <button type="submit" name="delete_property" class="btn btn-danger btn-sm btn-block">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</body>
</html>
