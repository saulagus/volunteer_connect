<?php 
include 'organiser_check.php'; 
require_once '../db_connect.php'; 

$organiserId = $_SESSION['user_id'];
$error_msg = "";
$success_msg = "";

// Validate first if the ID exist in the URL
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}
$eventId = $_GET['id'];

// Run if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_event'])) {
    //Sanitise data
    $title = strip_tags($_POST['title']);
    $description = strip_tags($_POST['description']);
    $location = strip_tags($_POST['location']);
    $eventDate = $_POST['eventDate'];
    $capacity = $_POST['capacity'];
    $categoryId = $_POST['categoryId'];
    $status = $_POST['status'];

    // Server-Side Validation
    $errors = [];

    // Text Fields Validation
    if (empty($title) || empty($description) || empty($location)) {
        $errors[] = "All text fields are required.";
    }

    // Capacity Validation
    if (!is_numeric($capacity) || $capacity <= 0) {
        $errors[] = "Capacity must be a positive number.";
    }

    // Date Validation
    $currentDate = date('Y-m-d H:i');
    if (strtotime($eventDate) < strtotime($currentDate)) {
        $errors[] = "Event date cannot be in the past.";
    }

    if (empty($categoryId)) {
        $errors[] = "Please select a valid category.";
    }

    // Check if we can proceed
    if (empty($errors)) {
        // If no errors, then proceed to Database
        // Prepared Statements
        $update_sql = "UPDATE events SET title=?, description=?, location=?, eventDate=?, capacity=?, categoryId=?, status=? 
                    WHERE eventId=? AND organiserId=?";
        
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssssiisii", $title, $description, $location, $eventDate, $capacity, $categoryId, $status, $eventId, $organiserId);

        if ($stmt->execute()) {
            $success_msg = "Event updated successfully!";
        } else {
            $error_msg = "Error updating event: " . $stmt->error;
        }
        $stmt->close();
    } else {
        // If validation failed, then show all error messages
        $error_msg = implode("<br>", $errors);
    }    
}

// Fetch current data to fill the form (run on every load)
$fetch_sql = "SELECT * FROM events WHERE eventId = ? AND organiserId = ?";
$fetch_stmt = $conn->prepare($fetch_sql);
$fetch_stmt->bind_param("ii", $eventId, $organiserId);
$fetch_stmt->execute();
$event = $fetch_stmt->get_result()->fetch_assoc();

if (!$event) {
    // If no event found, or unauthorised ID show error
    header("Location: dashboard.php?error=notfound");
    exit();
}

// Also fetch categories for the dropdown
$cat_result = $conn->query("SELECT * FROM categories");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Event</title>
</head>
<body>
    <h1>Edit Event: <?php echo htmlspecialchars($event['title']); ?></h1>
    <a href="dashboard.php">Back to Dashboard</a>

    <?php if ($success_msg): ?>
        <p style="color: green;"><?php echo $success_msg; ?></p>
    <?php endif; ?>
    
    <?php if ($error_msg): ?>
        <p style="color: red;"><?php echo $error_msg; ?></p>
    <?php endif; ?>

    <form action="edit_event.php?id=<?php echo $eventId; ?>" method="POST">
        <label>Event Title:</label><br>
        <input type="text" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required><br><br>

        <label>Description:</label><br>
        <textarea name="description" required><?php echo htmlspecialchars($event['description']); ?></textarea><br><br>

        <label>Location:</label><br>
        <input type="text" name="location" value="<?php echo htmlspecialchars($event['location']); ?>" required><br><br>

        <label>Event Date and Time:</label><br>
        <input type="datetime-local" name="eventDate" value="<?php echo date('Y-m-d\TH:i', strtotime($event['eventDate'])); ?>" required><br><br>

        <label>Capacity:</label><br>
        <input type="number" name="capacity" value="<?php echo $event['capacity']; ?>" required><br><br>

        <label>Category:</label><br>
        <select name="categoryId" required>
            <?php while($cat = $cat_result->fetch_assoc()): ?>
                <option value="<?php echo $cat['categoryId']; ?>" <?php echo ($cat['categoryId'] == $event['categoryId']) ? 'selected' : ''; ?>>
                    <?php echo $cat['name']; ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <label>Status:</label><br>
        <select name="status">
            <option value="active" <?php echo ($event['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
            <option value="full" <?php echo ($event['status'] == 'full') ? 'selected' : ''; ?>>Full</option>
            <option value="completed" <?php echo ($event['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
            <option value="cancelled" <?php echo ($event['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
        </select><br><br>

        <button type="submit" name="update_event">Save Changes</button>
    </form>
</body>
</html>

<?php 
$fetch_stmt->close();
$conn->close(); 
?>