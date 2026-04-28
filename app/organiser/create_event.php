<?php 
// Session-check
include 'organiser_check.php'; 
require_once '../db.php'; 
require_once '../includes/auth.php';
require_role('organiser');

$error_msg = "";

// Run when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_event'])) {
    // Collect data from form and Sanitise 
    $title = strip_tags($_POST['title']);
    $description = strip_tags($_POST['description']);
    $location = strip_tags($_POST['location']);
    $eventDate = $_POST['eventDate'];
    $capacity = (int)$_POST['capacity'];
    $categoryId = (int)$_POST['categoryId'];
    // Linking the event to the logged-in user (organiserId)
    $organiserId = (int)$_SESSION['user_id']; 

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
        $sql = "INSERT INTO events (title, description, location, eventDate, capacity, categoryId, organiserId) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssiii", $title, $description, $location, $eventDate, $capacity, $categoryId, $organiserId);

        if ($stmt->execute()) {
            // Close statement here
            $stmt->close(); 
            // Close connection before leaving
            $conn->close(); 
            // If successful, go back to dashboard
            header("Location: dashboard.php?msg=created");
            // Always exit after a redirect
            exit();
        } else {
            $error_msg = "Database error: " . htmlspecialchars($stmt->error);
            $stmt->close(); // Close if execute fails
        }
    } else {
        // If validation failed, then show all error messages
        $error_msg = implode("<br>", $errors);
    }
}

// Fetch categories to populate the dropdown
$cat_sql = "SELECT categoryId, name FROM categories";
$cat_result = $conn->query($cat_sql);

?>
<?php require_once '../includes/header.php'; ?>


<!DOCTYPE html>
<html>
<head>
    <title>Create Event</title>
</head>
<body>
    <h1>Create New Volunteering Event</h1>
    <a href="dashboard.php">Back to Dashboard</a>

    <?php if ($error_msg): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error_msg); ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <label>Event Title:</label><br>
        <input type="text" name="title" required><br><br>

        <label>Description:</label><br>
        <textarea name="description" required></textarea><br><br>

        <label>Location:</label><br>
        <input type="text" name="location" required><br><br>

        <label>Event Date and Time:</label><br>
        <input type="datetime-local" name="eventDate" required><br><br>

        <label>Capacity (Number of volunteers):</label><br>
        <input type="number" name="capacity" min="1" required><br><br>

        <label>Category:</label><br>
        <select name="categoryId" required>
            <option value="">-- Select Category --</option>
            <?php while($cat = $cat_result->fetch_assoc()): ?>
                <option value="<?php echo (int)$cat['categoryId']; ?>">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <button type="submit" name="submit_event">Create Event</button>
    </form>
</body>
</html>

<?php require_once '../includes/footer.php'; ?>