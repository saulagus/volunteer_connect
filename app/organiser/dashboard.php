<?php 
// Session-check
include 'organiser_check.php'; 
require_once '../includes/db.php'; 
require_once '../includes/auth.php';
require_role('organiser');

$myId = (int)$_SESSION['user_id'];

// Adding the logic for Stat Cards
// Total Events
$totalQuery = $conn->query("SELECT COUNT(*) as count FROM events WHERE organiserId = $myId");
$totalCount = $totalQuery->fetch_assoc()['count'];

// Total Volunteers (across all your events)
$volQuery = $conn->query("SELECT COUNT(*) as count FROM bookings b JOIN events e ON b.eventId = e.eventId WHERE e.organiserId = $myId AND b.status = 'booked'");
$volCount = $volQuery->fetch_assoc()['count'];

// For Data Integrity, fetch only events created by this organiser
$sql = "SELECT eventId, title, eventDate, status FROM events WHERE organiserId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $myId);
$stmt->execute();
$result = $stmt->get_result();
?>
<?php require_once '../includes/header.php'; ?>

<main>
    <h1>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></h1>

    <div class="statCards">
        <div class="statCard">
            <h2><?= $totalCount ?></h2>
            <p>Events Created</p>
        </div>

        <div class="statCard">
            <h2><?= $volCount ?></h2>
            <p>Total Volunteers</p>
        </div>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert <?= ($_GET['msg'] == 'created') ? 'alertSuccess' : 'alertWarning' ?>">
            <?= ($_GET['msg'] == 'created') ? 'Event created successfully!' : 'Event cancelled.' ?>
        </div>
    <?php endif; ?>

    <div class="sectionHeader">
        <h2>My Volunteering Events</h2>
        <a href="create_event.php" class="btn btnPrimary">+ Create Event</a>
    </div>

    <div class="tableWrap" id="eventsTable">
        <table>
            <thead>
                <tr>
                    <th>Event Title</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows === 0): ?>
                    <tr>
                        <td colspan="4" class="emptyState">You haven't created any events yet.</td>
                    </tr>
                <?php endif; ?>

                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['title']) ?></strong></td>
                        <td><?= htmlspecialchars(date('d M Y, H:i', strtotime($row['eventDate']))) ?></td>
                        <td>
                            <?php
                                // Matching the badge logic from Attendee dashboard
                                $badgeClass = match($row['status']) {
                                    'active' => 'statusActive',
                                    'full' => 'statusFull',
                                    'cancelled' => 'statusCancelled',
                                    default => 'statusCompleted'
                                };
                            ?>
                            <span class="statusBadge <?= $badgeClass ?>">
                                <?= htmlspecialchars(ucfirst($row['status'])) ?>
                            </span>
                        </td>
                        <td>
                            <div class="tableActions">
                                <a href="view_attendees.php?id=<?= (int)$row['eventId'] ?>" class="btn btnSecondary">Attendees</a>
                                <a href="edit_event.php?id=<?= (int)$row['eventId'] ?>" class="btn btnSecondary">Edit</a>
                                <?php if ($row['status'] !== 'cancelled'): ?>
                                    <form action="cancel_event.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= (int)$row['eventId'] ?>">
                                        <button type="submit" class="btn btnDanger" onclick="return confirm('Are you sure?')">
                                            Cancel
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <!-- Disabled state for cancelled events -->
                                    <button class="btn" disabled style="background: var(--line); color: var(--ink-subtle); cursor: not-allowed;">
                                        Cancelled
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>

<?php 
$stmt->close();
$conn->close();
?>

<?php require_once '../includes/footer.php'; ?>