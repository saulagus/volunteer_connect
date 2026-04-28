<?php 
// Session-check
include 'organiser_check.php'; 
require_once '../db.php'; 
require_once '../includes/auth.php';
require_role('organiser');

$myId = (int)$_SESSION['user_id'];

// For Data Integrity, fetch only events created by this organiser
$sql = "SELECT eventId, title, eventDate, status FROM events WHERE organiserId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $myId);
$stmt->execute();
$result = $stmt->get_result();
?>
<?php require_once '../includes/header.php'; ?>

<!DOCTYPE html>
<html>
    <head>
        <title>Organiser Dashboard</title>
        <link rel="stylesheet" href="public/style.css"> 
    </head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
    <p>Role: Organiser</p>

    <?php if (isset($_GET['msg'])): ?>
        <?php if ($_GET['msg'] == 'created'): ?>
            <div class="alert alert-success">Event created successfully!</div>
        <?php elseif ($_GET['msg'] == 'cancelled'): ?>
            <div class="alert alert-warning">Event has been cancelled.</div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] == 'notfound'): ?>
        <div class="alert alert-danger">Security Alert: Event not found or access denied.</div>
    <?php endif; ?>

    <div class="dashboard-actions">
        <a href="create_event.php" class="btn-primary">+ Create New Event</a>
    </div>

    <h2>My Volunteering Events</h2>
    <table class="event-table">
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
                <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                <td><?php echo htmlspecialchars($row['eventDate']); ?></td>
                <td>
                    <span class="badge badge-<?php echo $row['status']; ?>">
                        <?php echo htmlspecialchars(ucfirst($row['status'])); ?>
                    </span>
                </td>
                <td class="table-actions">
                    <a href="view_attendees.php?id=<?php echo (int)$row['eventId']; ?>" class="btn-small">Attendees</a>
                    <a href="edit_event.php?id=<?php echo (int)$row['eventId']; ?>" class="btn-small">Edit</a>
                    
                    <form action="cancel_event.php" method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo (int)$row['eventId']; ?>">
                        <button type="submit" class="btn-danger btn-small" onclick="return confirm('Are you sure?')">
                            Cancel
                        </button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>

<?php 
$stmt->close();
$conn->close();
?>

<?php require_once '../includes/footer.php'; ?>