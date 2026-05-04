<?php
// shows all of the attendee's bookings (booked, cancelled, attended)

session_start();
require_once '../db_connect.php';
require_once '../includes/auth.php';
require_role('attendee');

$userId = (int)$_SESSION['user_id'];

// pull all bookings for this user along with the event detail.
// upcoming events naturally sort to the top with DESC so the user sees what's next first.
$stmt = $conn->prepare(
    "SELECT bookings.bookingId, bookings.status AS bookingStatus, bookings.bookingDate,
            events.eventId, events.title, events.location, events.eventDate, events.status AS eventStatus
     FROM bookings
     JOIN events ON bookings.eventId = events.eventId
     WHERE bookings.userId = ?
     ORDER BY events.eventDate DESC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$bookings = [];
$row = $result->fetch_assoc();
while ($row !== null) {
    $bookings[] = $row;
    $row = $result->fetch_assoc();
}
$stmt->close();
?>
<?php require_once '../includes/header.php'; ?>

<main>
    <h1>My Bookings</h1>

    <!-- flash message after a cancel -->
    <?php if (isset($_SESSION['flash'])): ?>
        <p class="formSuccess"><?= htmlspecialchars($_SESSION['flash']) ?></p>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <!-- bookings table -->
    <div class="tableWrap">
        <table>
            <thead>
                <tr>
                    <th>Event</th>
                    <th>Location</th>
                    <th>Date</th>
                    <th>Booking Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($bookings)): ?>
                    <tr>
                        <td colspan="5" class="emptyState">You haven't booked any events yet.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($bookings as $booking): ?>
                    <?php
                    if ($booking['bookingStatus'] === 'booked') {
                        $badgeClass = 'statusBooked';
                    } elseif ($booking['bookingStatus'] === 'cancelled') {
                        $badgeClass = 'statusCancelled';
                    } else {
                        $badgeClass = 'statusAttended';
                    }
                    // only show cancel for active bookings on events that haven't happened yet
                    $isPast    = strtotime($booking['eventDate']) < time();
                    $canCancel = ($booking['bookingStatus'] === 'booked') && !$isPast;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($booking['title']) ?></td>
                        <td><?= htmlspecialchars($booking['location']) ?></td>
                        <td><?= htmlspecialchars(date('d M Y, H:i', strtotime($booking['eventDate']))) ?></td>
                        <td>
                            <span class="statusBadge <?= $badgeClass ?>"><?= htmlspecialchars($booking['bookingStatus']) ?></span>
                        </td>
                        <td>
                            <div class="tableActions">
                                <a href="event_detail.php?id=<?= (int)$booking['eventId'] ?>" class="btn btnSecondary">View</a>
                                <?php if ($canCancel): ?>
                                    <form method="POST" action="cancel_booking.php" class="inlineForm">
                                        <input type="hidden" name="id" value="<?= (int)$booking['bookingId'] ?>">
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
