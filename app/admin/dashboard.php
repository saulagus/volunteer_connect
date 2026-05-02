<?php
// shows site counts and links to management pages

session_start();
require_once '../db_connect.php';
require_once '../includes/auth.php';
require_role('admin');
// get total of registered users
$userResult  = $conn->query("SELECT COUNT(*) AS total FROM users");
$userRow     = $userResult->fetch_assoc();
$userCount   = $userRow['total'];
// get total events
$eventResult = $conn->query("SELECT COUNT(*) AS total FROM events");
$eventRow    = $eventResult->fetch_assoc();
$eventCount  = $eventRow['total'];
// get total bookings across all events
$bookingResult = $conn->query("SELECT COUNT(*) AS total FROM bookings");
$bookingRow    = $bookingResult->fetch_assoc();
$bookingCount  = $bookingRow['total'];

// fetch all events with organiser name and booking count
$eventStmt = $conn->prepare(
    "SELECT events.eventId, events.title, events.eventDate, events.capacity, events.status,
            users.name AS organiserName,
            COUNT(bookings.eventId) AS bookingCount
     FROM events
     JOIN users ON users.userId = events.organiserId
     LEFT JOIN bookings ON bookings.eventId = events.eventId
     GROUP BY events.eventId
     ORDER BY events.eventDate ASC");
$eventStmt->execute();
$eventResult = $eventStmt->get_result();
$events = [];
$eventRow = $eventResult->fetch_assoc();
while ($eventRow !== null) {
    $events[] = $eventRow;
    $eventRow = $eventResult->fetch_assoc();
}
$eventStmt->close();
?>
<?php require_once '../includes/header.php'; ?>

<main>
    <h1>Admin Dashboard</h1>

    <!-- site-wide counts -->
    <div class="statCards">

        <div class="statCard">
            <h2><?= htmlspecialchars($userCount) ?></h2>
            <p>Registered Users</p>
            <a href="/volunteer_connect/app/admin/users.php">Manage Users</a>
        </div>

        <div class="statCard">
            <h2><?= htmlspecialchars($eventCount) ?></h2>
            <p>Total Events</p>
        </div>

        <div class="statCard">
            <h2><?= htmlspecialchars($bookingCount) ?></h2>
            <p>Total Bookings</p>
        </div>

    </div>

    <!-- all events table -->
    <div class="sectionHeader">
        <h2>All Events</h2>
    </div>

    <div class="tableWrap">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Organiser</th>
                    <th>Date</th>
                    <th>Capacity</th>
                    <th>Bookings</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($events)): ?>
                    <tr>
                        <td colspan="7" class="emptyState">No events found.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($events as $event): ?>
                    <tr>
                        <td><?= htmlspecialchars($event['title']) ?></td>
                        <td><?= htmlspecialchars($event['organiserName']) ?></td>
                        <td><?= htmlspecialchars(date('d M Y, H:i', strtotime($event['eventDate']))) ?></td>
                        <td><?= (int)$event['capacity'] ?></td>
                        <td><?= (int)$event['bookingCount'] ?></td>
                        <td>
                            <?php
                            $status = htmlspecialchars($event['status']);
                            if ($event['status'] === 'active') {
                                $badgeClass = 'statusActive';
                            } elseif ($event['status'] === 'full') {
                                $badgeClass = 'statusFull';
                            } elseif ($event['status'] === 'cancelled') {
                                $badgeClass = 'statusCancelled';
                            } else {
                                $badgeClass = 'statusCompleted';
                            }
                            ?>
                            <span class="statusBadge <?= $badgeClass ?>"><?= $status ?></span>
                        </td>
                        <td>
                            <div class="tableActions">
                                <a href="edit_event.php?id=<?= (int)$event['eventId'] ?>" class="btn btnSecondary">Edit</a>
                                <?php if ($event['status'] !== 'cancelled'): ?>
                                    <form method="POST" action="cancel_event.php" class="inlineForm">
                                        <input type="hidden" name="id" value="<?= (int)$event['eventId'] ?>">
                                        <button type="submit" class="btn btnWarning">Cancel</button>
                                    </form>
                                <?php endif; ?>

                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
