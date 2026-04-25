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
?>
<?php require_once '../includes/header.php'; ?>

<main>
    <h1>Admin Dashboard</h1>

    <div class="statCards">

        <div class="statCard">
            <h2><?= htmlspecialchars($userCount) ?></h2>
            <p>Registered Users</p>
            <a href="/admin/users.php">Manage Users</a>
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
</main>

<?php require_once '../includes/footer.php'; ?>
