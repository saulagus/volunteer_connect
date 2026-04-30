<?php 
// Session-check
require_once '../db_connect.php'; 
require_once '../includes/auth.php';
session_start();
require_role('organiser');

$organiserId = $_SESSION['user_id'];
$error_msg = "";
$success_msg = "";

// Validate first if the ID exist in the URL
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}
$eventId = (int)$_GET['id'];

// Run if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_event'])) {
    //Sanitise data
    $title = strip_tags($_POST['title']);
    $description = strip_tags($_POST['description']);
    $location = strip_tags($_POST['location']);
    $eventDate = $_POST['eventDate'];
    $capacity = (int)$_POST['capacity'];
    $categoryId = (int)$_POST['categoryId'];
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
            $error_msg = "Error updating event: " . htmlspecialchars($stmt->error);
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
$result = $fetch_stmt->get_result(); // Get the result object
$event = $result->fetch_assoc(); //Fetch the associative array

if (!$event) {
    $fetch_stmt->close();
    // If no event found, or unauthorised ID show error
    header("Location: dashboard.php?error=notfound");
    exit();
}

// Also fetch categories for the dropdown
$cat_result = $conn->query("SELECT categoryId, name FROM categories");
?>

<?php require_once '../includes/header.php'; ?>

<main>
    <div class="sectionHeader">
        <h1>Edit Event</h1>
        <a href="dashboard.php" class="btn btnSecondary">Return</a>
    </div>

    <div class="formWrap">
        
        <?php if ($success_msg): ?>
            <div class="formSuccess"><?= htmlspecialchars($success_msg) ?></div>
        <?php endif; ?>

        <?php if ($error_msg): ?>
            <div class="formErrors"><?= $error_msg ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div>
                <label>Event Title</label>
                <input type="text" name="title" value="<?= htmlspecialchars($event['title']) ?>" required>
            </div>

            <div>
                <label>Description</label>
                <textarea name="description" required><?= htmlspecialchars($event['description']) ?></textarea>
            </div>

            <div>
                <label>Location</label>
                <input type="text" name="location" value="<?= htmlspecialchars($event['location']) ?>" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-4);">
                <div>
                    <label>Date and Time</label>
                    <input type="datetime-local" name="eventDate" 
                           value="<?= date('Y-m-d\TH:i', strtotime($event['eventDate'])) ?>" required>
                </div>
                <div>
                    <label>Capacity</label>
                    <input type="number" name="capacity" value="<?= (int)$event['capacity'] ?>" min="1" required>
                </div>
            </div>

            <div>
                <label>Category</label>
                <select name="categoryId" required>
                    <?php while($cat = $cat_result->fetch_assoc()): ?>
                        <option value="<?= (int)$cat['categoryId'] ?>" 
                                <?= ($cat['categoryId'] == $event['categoryId']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div>
                <label>Event Status</label>
                <select name="status">
                    <option value="active" <?= ($event['status'] == 'active') ? 'selected' : '' ?>>Active</option>
                    <option value="full" <?= ($event['status'] == 'full') ? 'selected' : '' ?>>Full</option>
                    <option value="cancelled" <?= ($event['status'] == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>

            <div class="formActions">
                <button type="submit" name="update_event" class="btn btnPrimary">Save Changes</button>
            </div>
        </form>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>