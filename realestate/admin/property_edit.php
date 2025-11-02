<?php
// Property Add/Edit Page
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Include database connection
include_once "../connection.php";

$property = [
    'property_id' => '',
    'property_title' => '',
    'property_details' => '',
    'delivery_type' => 'Sale',
    'availablility' => 0,
    'price' => '',
    'property_img' => 'images/properties/default.jpg',
    'property_address' => '',
    'bed_room' => 0,
    'liv_room' => 0,
    'parking' => 0,
    'kitchen' => 0,
    'utility' => '',
    'property_type' => 'Apartment',
    'floor_space' => ''
];

$is_edit = false;
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $property_id = isset($_POST['property_id']) ? intval($_POST['property_id']) : 0;
    $property_title = trim($_POST['property_title'] ?? '');
    $property_details = trim($_POST['property_details'] ?? '');
    $delivery_type = $_POST['delivery_type'] === 'Rent' ? 'Rent' : 'Sale';
    $availablility = isset($_POST['availablility']) ? 1 : 0;
    $price = floatval($_POST['price'] ?? 0);
    $property_address = trim($_POST['property_address'] ?? '');
    $bed_room = intval($_POST['bed_room'] ?? 0);
    $liv_room = intval($_POST['liv_room'] ?? 0);
    $parking = intval($_POST['parking'] ?? 0);
    $kitchen = intval($_POST['kitchen'] ?? 0);
    $utility = trim($_POST['utility'] ?? '');
    $property_type = $_POST['property_type'];
    $floor_space = trim($_POST['floor_space'] ?? '');
    
    // Handle image upload
    $image_path = $_POST['current_image'] ?? 'images/properties/default.jpg';
    if (isset($_FILES['property_img']) && $_FILES['property_img']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../images/properties/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['property_img']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            $new_filename = 'property_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['property_img']['tmp_name'], $upload_path)) {
                $image_path = 'images/properties/' . $new_filename;
                
                // Delete old image if it's not the default one
                if (isset($_POST['current_image']) && $_POST['current_image'] !== 'images/properties/default.jpg') {
                    @unlink('../' . $_POST['current_image']);
                }
            }
        }
    }
    
    // Validate required fields
    if (empty($property_title) || empty($property_details) || empty($property_address)) {
        $error = 'Please fill in all required fields.';
    } else {
        // Save to database
        if ($property_id > 0) {
            // Update existing property
            $query = "UPDATE properties SET 
                property_title = ?, 
                property_details = ?, 
                delivery_type = ?, 
                availablility = ?, 
                price = ?, 
                property_img = ?, 
                property_address = ?, 
                bed_room = ?, 
                liv_room = ?, 
                parking = ?, 
                kitchen = ?, 
                utility = ?, 
                property_type = ?, 
                floor_space = ?
                WHERE property_id = ?";
            
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, 'sssdssiiiiissi', 
                $property_title, 
                $property_details, 
                $delivery_type, 
                $availablility, 
                $price, 
                $image_path, 
                $property_address, 
                $bed_room, 
                $liv_room, 
                $parking, 
                $kitchen, 
                $utility, 
                $property_type, 
                $floor_space,
                $property_id
            );
        } else {
            // Insert new property
            $query = "INSERT INTO properties (
                property_title, property_details, delivery_type, availablility, 
                price, property_img, property_address, bed_room, liv_room, 
                parking, kitchen, utility, property_type, floor_space
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, 'sssdssiiiiisss', 
                $property_title, 
                $property_details, 
                $delivery_type, 
                $availablility, 
                $price, 
                $image_path, 
                $property_address, 
                $bed_room, 
                $liv_room, 
                $parking, 
                $kitchen, 
                $utility, 
                $property_type, 
                $floor_space
            );
        }
        
        if (mysqli_stmt_execute($stmt)) {
            $success = 'Property has been ' . ($property_id > 0 ? 'updated' : 'added') . ' successfully.';
            $property_id = $property_id > 0 ? $property_id : mysqli_insert_id($con);
            
            // Redirect to properties list after successful save
            if (isset($_POST['save_and_list'])) {
                header('Location: properties.php?success=1');
                exit;
            }
        } else {
            $error = 'Error saving property: ' . mysqli_error($con);
        }
        
        mysqli_stmt_close($stmt);
    }
} elseif (isset($_GET['id'])) {
    // Load existing property for editing
    $property_id = intval($_GET['id']);
    $query = "SELECT * FROM properties WHERE property_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 'i', $property_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($property = mysqli_fetch_assoc($result)) {
        $is_edit = true;
    } else {
        header('Location: properties.php');
        exit;
    }
    
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $is_edit ? 'Edit' : 'Add'; ?> Property - Admin Panel</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { padding-top: 60px; }
        .property-image-preview {
            max-width: 200px;
            max-height: 150px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            padding: 5px;
            border-radius: 4px;
        }
        .form-section {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        .btn-toolbar {
            margin-top: 20px;
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
                <h2><?php echo $is_edit ? 'Edit Property' : 'Add New Property'; ?></h2>
                <hr>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form method="post" enctype="multipart/form-data" class="form-section">
                    <input type="hidden" name="property_id" value="<?php echo $property['property_id']; ?>">
                    <input type="hidden" name="current_image" value="<?php echo $property['property_img']; ?>">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Property Title *</label>
                                <input type="text" name="property_title" class="form-control" required 
                                       value="<?php echo htmlspecialchars($property['property_title']); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label>Property Details *</label>
                                <textarea name="property_details" class="form-control" rows="6" required><?php 
                                    echo htmlspecialchars($property['property_details']); 
                                ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label>Property Address *</label>
                                <input type="text" name="property_address" class="form-control" required
                                       value="<?php echo htmlspecialchars($property['property_address']); ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Property Image</label>
                                <div>
                                    <img src="../<?php echo $property['property_img']; ?>" class="property-image-preview" 
                                         id="imagePreview" onerror="this.src='../images/properties/default.jpg'">
                                </div>
                                <input type="file" name="property_img" class="form-control" 
                                       onchange="previewImage(this)">
                            </div>
                            
                            <div class="form-group">
                                <label>Price *</label>
                                <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" name="price" class="form-control" required step="0.01"
                                           value="<?php echo $property['price']; ?>">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Delivery Type *</label>
                                <select name="delivery_type" class="form-control" required>
                                    <option value="Sale" <?php echo $property['delivery_type'] === 'Sale' ? 'selected' : ''; ?>>For Sale</option>
                                    <option value="Rent" <?php echo $property['delivery_type'] === 'Rent' ? 'selected' : ''; ?>>For Rent</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Property Type *</label>
                                <select name="property_type" class="form-control" required>
                                    <option value="Apartment" <?php echo $property['property_type'] === 'Apartment' ? 'selected' : ''; ?>>Apartment</option>
                                    <option value="House" <?php echo $property['property_type'] === 'House' ? 'selected' : ''; ?>>House</option>
                                    <option value="Villa" <?php echo $property['property_type'] === 'Villa' ? 'selected' : ''; ?>>Villa</option>
                                    <option value="Office" <?php echo $property['property_type'] === 'Office' ? 'selected' : ''; ?>>Office</option>
                                    <option value="Building" <?php echo $property['property_type'] === 'Building' ? 'selected' : ''; ?>>Building</option>
                                    <option value="Townhouse" <?php echo $property['property_type'] === 'Townhouse' ? 'selected' : ''; ?>>Townhouse</option>
                                </select>
                            </div>
                            
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="availablility" value="1" 
                                           <?php echo $property['availablility'] ? 'checked' : ''; ?>>
                                    Mark as Sold/Not Available
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Bedrooms</label>
                                <input type="number" name="bed_room" class="form-control" min="0"
                                       value="<?php echo $property['bed_room']; ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Living Rooms</label>
                                <input type="number" name="liv_room" class="form-control" min="0"
                                       value="<?php echo $property['liv_room']; ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Parking Spaces</label>
                                <input type="number" name="parking" class="form-control" min="0"
                                       value="<?php echo $property['parking']; ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Kitchens</label>
                                <input type="number" name="kitchen" class="form-control" min="0"
                                       value="<?php echo $property['kitchen']; ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Utilities</label>
                        <input type="text" name="utility" class="form-control" 
                               value="<?php echo htmlspecialchars($property['utility']); ?>">
                        <p class="help-block">Separate utilities with commas (e.g., Electricity, Water, Gas)</p>
                    </div>
                    
                    <div class="form-group">
                        <label>Floor Space (sq ft)</label>
                        <input type="text" name="floor_space" class="form-control" 
                               value="<?php echo htmlspecialchars($property['floor_space']); ?>">
                    </div>
                    
                    <div class="btn-toolbar">
                        <button type="submit" name="save" class="btn btn-primary">
                            <i class="fa fa-save"></i> Save
                        </button>
                        <button type="submit" name="save_and_list" class="btn btn-success">
                            <i class="fa fa-save"></i> Save & Return to List
                        </button>
                        <a href="properties.php" class="btn btn-default">
                            <i class="fa fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets/jquery-1.9.1.min.js"></script>
    <script src="../assets/bootstrap/js/bootstrap.js"></script>
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function(e) {
                    $('#imagePreview').attr('src', e.target.result);
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
