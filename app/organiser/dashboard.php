<?php 
// Session-check
include 'organiser_check.php'; 
require_once '../db_connect.php'; 

$myId = $_SESSION['user_id'];

// For Data Integrity, fetch only events created by this organiser
$sql = "SELECT eventId, title, eventDate, status FROM events WHERE organiserId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $myId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Organiser Dashboard</title>
        <link rel="stylesheet" href="public/style.css"> 
    </head>
<body>
    <h1>Welcome, <?php echo $_SESSION['user_name']; ?>!</h1>
    <p>Role: Organiser</p>

    <?php if (isset($_GET['error']) && $_GET['error'] == 'notfound'): ?>
    <p style="color: red;">Security Alert: Event not found or access denied.</p>
    <?php endif; ?>

    <a href="create_event.php" style="button"> + Create New Event</a>

    <h2>My Volunteering Events</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Event Title</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo $row['eventDate']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td>
                    <a href="view_attendees.php?id=<?php echo $row['eventId']; ?>">Attendees</a> |
                    <a href="edit_event.php?id=<?php echo $row['eventId']; ?>">Edit</a> |
                    <a href="cancel_event.php?id=<?php echo $row['eventId']; ?>" 
                       onclick="return confirm('Are you sure?')">Cancel</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>