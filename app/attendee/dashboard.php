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
?>
<?php require_once '../includes/header.php'; ?>

<main>
    <h1>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></h1>

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
</main>

<?php require_once '../includes/footer.php'; ?>
