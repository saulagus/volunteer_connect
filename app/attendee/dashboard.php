<?php
// landing page for attendees, shows their booking summary and links to browse/manage

session_start();
require_once '../db_connect.php';
require_once '../includes/auth.php';
require_role('attendee');

$userId = (int)$_SESSION['user_id'];

// count this user's upcoming bookings (still booked and the event hasn't happened yet)
$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM bookings b
     JOIN events e ON b.eventId = e.eventId
     WHERE b.userId = ? AND b.status = 'booked' AND e.eventDate >= NOW()");
$stmt->bind_param("i", $userId);
$stmt->execute();
$upcomingRow   = $stmt->get_result()->fetch_assoc();
$upcomingCount = (int)$upcomingRow['total'];
$stmt->close();

// count events the user could still sign up for
$availableResult = $conn->query(
    "SELECT COUNT(*) AS total
     FROM events
     WHERE status = 'active' AND eventDate >= NOW()");
$availableRow   = $availableResult->fetch_assoc();
$availableCount = (int)$availableRow['total'];

// fetch the user's upcoming bookings with event detail for the table below
$stmt = $conn->prepare(
    "SELECT e.eventId, e.title, e.eventDate, e.location, e.status,
            u.name AS organiserName
     FROM bookings b
     JOIN events e ON b.eventId = e.eventId
     JOIN users  u ON e.organiserId = u.userId
     WHERE b.userId = ? AND b.status = 'booked' AND e.eventDate >= NOW()
     ORDER BY e.eventDate ASC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$bookingsResult = $stmt->get_result();
$bookings = [];
$bookingRow = $bookingsResult->fetch_assoc();
while ($bookingRow !== null) {
    $bookings[] = $bookingRow;
    $bookingRow = $bookingsResult->fetch_assoc();
}
$stmt->close();
?>
<?php require_once '../includes/header.php'; ?>

<main>
    <h1>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></h1>

    <!-- attendee summary counts -->
    <div class="statCards">

        <div class="statCard">
            <h2><?= htmlspecialchars($upcomingCount) ?></h2>
            <p>Upcoming Bookings</p>
            <a href="my_bookings.php">View My Bookings</a>
        </div>

        <div class="statCard">
            <h2><?= htmlspecialchars($availableCount) ?></h2>
            <p>Events Available</p>
            <a href="events.php">Browse Events</a>
        </div>

    </div>

    <!-- upcoming bookings table -->
    <div class="sectionHeader">
        <h2>My Upcoming Bookings</h2>
    </div>

    <div class="tableWrap">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Organiser</th>
                    <th>Location</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($bookings)): ?>
                    <tr>
                        <td colspan="6" class="emptyState">You have no upcoming bookings.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?= htmlspecialchars($booking['title']) ?></td>
                        <td><?= htmlspecialchars($booking['organiserName']) ?></td>
                        <td><?= htmlspecialchars($booking['location']) ?></td>
                        <td><?= htmlspecialchars(date('d M Y, H:i', strtotime($booking['eventDate']))) ?></td>
                        <td>
                            <?php
                            if ($booking['status'] === 'active') {
                                $badgeClass = 'statusActive';
                            } elseif ($booking['status'] === 'full') {
                                $badgeClass = 'statusFull';
                            } elseif ($booking['status'] === 'cancelled') {
                                $badgeClass = 'statusCancelled';
                            } else {
                                $badgeClass = 'statusCompleted';
                            }
                            ?>
                            <span class="statusBadge <?= $badgeClass ?>"><?= htmlspecialchars($booking['status']) ?></span>
                        </td>
                        <td>
                            <div class="tableActions">
                                <a href="event_detail.php?id=<?= (int)$booking['eventId'] ?>" class="btn btnSecondary">View</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
