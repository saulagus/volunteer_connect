<?php 
// Session-check
require_once '../db_connect.php'; 
require_once '../includes/auth.php';
session_start();
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
$sql = "SELECT users.name, users.email, bookings.bookingDate, bookings.status
        FROM bookings
        INNER JOIN users ON bookings.userId = users.userId
        WHERE bookings.eventId = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $eventId);
$stmt->execute();
$attendees = $stmt->get_result();
?>

<?php require_once '../includes/header.php'; ?>

<main>
    <div class="sectionHeader">
        <h1>Attendees</h1>
        <p>Event: <strong><?= htmlspecialchars($event_data['title']) ?></strong></p>
        <a href="dashboard.php" class="btn btnSecondary" style="margin-top: var(--space-3)">Back to Dashboard</a>
    </div>

    <!-- Using the tableWrap pattern from the CSS -->
    <div class="tableWrap">
        <table>
            <thead>
                <tr>
                    <th>Volunteer Name</th>
                    <th>Email</th>
                    <th>Signed Up</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($attendees->num_rows > 0): ?>
                    <?php while($row = $attendees->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars(date('d M Y', strtotime($row['bookingDate']))) ?></td>
                        <td>
                            <span class="statusBadge statusBooked">
                                <?= htmlspecialchars(ucfirst($row['status'])) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="emptyState">No volunteers have signed up yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>