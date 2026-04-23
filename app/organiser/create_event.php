<?php
include 'organiser_check.php';
require_once '../db_connect.php';

$error_msg = "";

// Run when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_event'])) {
    // Collect data from form
    $title = $_POST['title'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $eventDate = $_POST['eventDate'];
    $capacity = $_POST['capacity'];
    $categoryId = $_POST['categoryId'];
    // Linking the event to the logged-in user (organiserId)
    $organiserId = $_SESSION['user_id']; 

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
        $error_msg = "Database error: " . $stmt->error;
    }

    $stmt->close();
    
}

// Fetch data for the form
$cat_sql = "SELECT * FROM categories";
$cat_result = $conn->query($cat_sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Event</title>
</head>
<body>
    <h1>Create New Volunteering Event</h1>
    <a href="dashboard.php">Back to Dashboard</a>

    <?php if ($error_msg): ?>
        <p style="color: red;"><?php echo $error_msg; ?></p>
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
                <option value="<?php echo $cat['categoryId']; ?>">
                    <?php echo $cat['name']; ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <button type="submit" name="submit_event">Create Event</button>
    </form>
</body>
</html>