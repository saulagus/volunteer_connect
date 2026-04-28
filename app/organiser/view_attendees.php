<?php 
// Session-check
include 'organiser_check.php'; 
require_once '../db.php'; 
require_once '../includes/auth.php';
require_role('organiser');

$organiserId = (int)$_SESSION['user_id'];

// Check if the Event ID is provided
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}
$eventId = (int)$_GET['id'];

// Security Check to ensure event actually belong to this organiser
$check_sql = "SELECT title FROM events WHERE eventId = ? AND organiserId = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $eventId, $organiserId);
$check_stmt->execute();
$event_result = $check_stmt->get_result();
$event_data = $event_result->fetch_assoc(); 

if (!$event_data) {
    die("Error: Event not found or you do not have permission to view it.");
}

// Fetch Attendees using JOIN
// We join 'bookings' with 'users' to get the volunteer details
// Using b as an alias for the bookings table and u as an alias for the users table
$sql = "SELECT u.name, u.email, b.bookingDate, b.status 
        FROM bookings b
        INNER JOIN users u ON b.userId = u.userId
        WHERE b.eventId = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $eventId);
$stmt->execute();
$attendees = $stmt->get_result();
?>

<?php require_once '../includes/header.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>View Attendees</title>
</head>
<body>
    <h1>Attendees for: <?php echo htmlspecialchars($event_data['title']); ?></h1>
    <a href="dashboard.php">Back to Dashboard</a>

    <hr>

    <?php if ($attendees->num_rows > 0): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Volunteer Name</th>
                    <th>Email</th>
                    <th>Date Signed Up</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $attendees->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['bookingDate']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No volunteers have signed up for this event yet.</p>
    <?php endif; ?>

</body>
</html>

<?php 
$stmt->close();
$check_stmt->close();
$conn->close(); 
?>

<?php require_once '../includes/footer.php'; ?>