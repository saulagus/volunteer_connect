<?php 
// Session-check
include 'organiser_check.php'; 
require_once '../includes/db.php'; 
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


<main>
    <div class="sectionHeader">
        <h1>Create New Volunteering Event</h1>
        <a href="dashboard.php" class="btn btnSecondary">Back to Dashboard</a>
    </div>

    <!-- The CSS 'formWrap' provides the white card look and max-width -->
    <div class="formWrap">
        
        <?php if ($error_msg): ?>
            <!-- Using the semantic danger style from style.css -->
            <div class="formErrors">
                <?= $error_msg ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div>
                <label>Event Title</label>
                <input type="text" name="title" placeholder="e.g. Community Garden Cleanup" value="<?= htmlspecialchars($title) ?>" required>
            </div>

            <div>
                <label>Description</label>
                <textarea name="description" placeholder="Describe the tasks and requirements..." required><?= htmlspecialchars($description) ?></textarea>
            </div>

            <div>
                <label>Location</label>
                <input type="text" name="location" placeholder="Enter address or venue" value="<?= htmlspecialchars($location) ?>" required>
            </div>

            <!-- Using a simple inline grid for date and capacity -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-4);">
                <div>
                    <label>Event Date and Time</label>
                    <input type="datetime-local" name="eventDate" value="<?= htmlspecialchars($eventDate) ?>" required>
                </div>

                <div>
                    <label>Capacity</label>
                    <input type="number" name="capacity" min="1" placeholder="Number of volunteers" value="<?= htmlspecialchars($capacity) ?>" required>
                </div>
            </div>

            <div>
                <label>Category</label>
                <select name="categoryId" required>
                    <option value="">-- Select Category --</option>
                    <?php while($cat = $cat_result->fetch_assoc()): ?>
                        <option value="<?= (int)$cat['categoryId'] ?>" <?= ($categoryId == $cat['categoryId']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="formActions">
                <button type="submit" name="submit_event" class="btn btnPrimary">Create Event</button>
            </div>
        </form>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>