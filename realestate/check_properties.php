<?php
// Include database connection
require_once('connection.php');

// Query to get all properties with their availability status
$query = "SELECT property_id, property_title, availablility FROM properties";
$result = mysqli_query($con, $query);

if (!$result) {
    die("Error: " . mysqli_error($con));
}

echo "<h2>Properties in Database</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Title</th><th>Available</th></tr>";

$available_count = 0;
$total_count = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $available = $row['availablility'] == 1 ? 'Yes' : 'No';
    if ($row['availablility'] == 1) $available_count++;
    $total_count++;
    
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['property_id']) . "</td>";
    echo "<td>" . htmlspecialchars($row['property_title']) . "</td>";
    echo "<td>" . $available . "</td>";
    echo "</tr>";
}

echo "</table>";
echo "<p>Total properties: " . $total_count . "</p>";
echo "<p>Available properties: " . $available_count . "</p>";

// Form to update availability
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['make_available'])) {
    $update_query = "UPDATE properties SET availablility = 1 WHERE property_id IN (" . 
                   implode(',', array_map('intval', $_POST['properties'])) . ")";
    
    if (mysqli_query($con, $update_query)) {
        echo "<p style='color:green;'>Updated " . mysqli_affected_rows($con) . " properties to available.</p>";
        // Refresh the page to show updated data
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "<p style='color:red;'>Error updating properties: " . mysqli_error($con) . "</p>";
    }
}

// Reset result set
mysqli_data_seek($result, 0);
?>

<h2>Update Property Availability</h2>
<form method="post" action="">
    <table border='1' cellpadding='5'>
        <tr><th>Select</th><th>ID</th><th>Title</th><th>Current Status</th></tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><input type="checkbox" name="properties[]" value="<?php echo $row['property_id']; ?>"></td>
                <td><?php echo htmlspecialchars($row['property_id']); ?></td>
                <td><?php echo htmlspecialchars($row['property_title']); ?></td>
                <td><?php echo $row['availablility'] == 1 ? 'Available' : 'Not Available'; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
    <br>
    <input type="submit" name="make_available" value="Mark Selected as Available">
</form>
