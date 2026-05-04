<?php
// shows a single event's details and lets an attendee book a place

session_start();
require_once '../db_connect.php';
require_once '../includes/auth.php';
require_role('attendee');

// pull the event id from the query string and reject anything that isn't a positive int
$eventId = 0;
if (isset($_GET['id'])) {
    $eventId = (int)$_GET['id'];
}
if ($eventId <= 0) {
    header('Location: events.php');
    exit();
}

$userId = (int)$_SESSION['user_id'];
$message = '';
$error   = '';

// handle a booking submission before fetching state, so the page reflects the new booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_btn'])) {
    // re-check the event is still bookable at the moment of submission, not just at page load
    $stmt = $conn->prepare(
        "SELECT capacity, status, eventDate
         FROM events
         WHERE eventId = ?");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $check = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$check) {
        $error = 'Event no longer exists.';
    } elseif ($check['status'] !== 'active') {
        $error = 'This event is not open for bookings.';
    } elseif (strtotime($check['eventDate']) < time()) {
        $error = 'This event has already taken place.';
    } else {
        // count current bookings to make sure we're under capacity
        $stmt = $conn->prepare(
            "SELECT COUNT(*) AS total
             FROM bookings
             WHERE eventId = ? AND status = 'booked'");
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        $countRow = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ((int)$countRow['total'] >= (int)$check['capacity']) {
            $error = 'This event is full.';
        } else {
            // check if this user already has a booking row for this event,
            // since the unique key (userId, eventId) blocks a second insert
            $stmt = $conn->prepare(
                "SELECT bookingId, status
                 FROM bookings
                 WHERE userId = ? AND eventId = ?");
            $stmt->bind_param("ii", $userId, $eventId);
            $stmt->execute();
            $existing = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($existing && $existing['status'] === 'booked') {
                $error = 'You are already booked on this event.';
            } elseif ($existing) {
                // they cancelled before, flip the existing row back to booked
                $stmt = $conn->prepare(
                    "UPDATE bookings
                     SET status = 'booked', bookingDate = CURRENT_TIMESTAMP
                     WHERE bookingId = ?");
                $stmt->bind_param("i", $existing['bookingId']);
                $stmt->execute();
                $stmt->close();
                $message = 'Booking confirmed.';
            } else {
                $stmt = $conn->prepare(
                    "INSERT INTO bookings (userId, eventId, status)
                     VALUES (?, ?, 'booked')");
                $stmt->bind_param("ii", $userId, $eventId);
                $stmt->execute();
                $stmt->close();
                $message = 'Booking confirmed.';
            }
        }
    }
}

// fetch the event with its category and organiser for display
$stmt = $conn->prepare(
    "SELECT events.eventId, events.title, events.description, events.location, events.eventDate,
            events.capacity, events.status,
            categories.name AS categoryName,
            users.name AS organiserName
     FROM events
     LEFT JOIN categories ON events.categoryId = categories.categoryId
     JOIN users ON events.organiserId = users.userId
     WHERE events.eventId = ?");
$stmt->bind_param("i", $eventId);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$event) {
    require_once '../includes/header.php';
    echo '<main><h1>Event not found</h1><p><a href="events.php" class="btn btnSecondary">Back to events</a></p></main>';
    require_once '../includes/footer.php';
    exit();
}

// count booked seats so we can show remaining and decide whether to show the book button
$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM bookings
     WHERE eventId = ? AND status = 'booked'");
$stmt->bind_param("i", $eventId);
$stmt->execute();
$bookedRow = $stmt->get_result()->fetch_assoc();
$stmt->close();
$bookedCount = (int)$bookedRow['total'];
$remaining   = (int)$event['capacity'] - $bookedCount;

// figure out this user's current relationship to the event
$stmt = $conn->prepare(
    "SELECT status
     FROM bookings
     WHERE userId = ? AND eventId = ?");
$stmt->bind_param("ii", $userId, $eventId);
$stmt->execute();
$myBookingRow = $stmt->get_result()->fetch_assoc();
$stmt->close();
$myStatus = $myBookingRow ? $myBookingRow['status'] : null;

$isPast   = strtotime($event['eventDate']) < time();
$isActive = $event['status'] === 'active';

// pick a badge class for the event status, matches admin convention
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
<?php require_once '../includes/header.php'; ?>

<main>
    <h1><?= htmlspecialchars($event['title']) ?></h1>

    <!-- booking feedback messages -->
    <?php if ($message !== ''): ?>
        <p class="formSuccess"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <?php if ($error !== ''): ?>
        <ul class="formErrors">
            <li><?= htmlspecialchars($error) ?></li>
        </ul>
    <?php endif; ?>

    <!-- event details -->
    <div class="detailWrap">
        <dl>
            <dt>Status</dt>
            <dd><span class="statusBadge <?= $badgeClass ?>"><?= htmlspecialchars($event['status']) ?></span></dd>

            <dt>Category</dt>
            <dd><?= htmlspecialchars($event['categoryName'] ?? '-') ?></dd>

            <dt>Organiser</dt>
            <dd><?= htmlspecialchars($event['organiserName']) ?></dd>

            <dt>Location</dt>
            <dd><?= htmlspecialchars($event['location']) ?></dd>

            <dt>Date</dt>
            <dd><?= htmlspecialchars(date('d M Y, H:i', strtotime($event['eventDate']))) ?></dd>

            <dt>Places Left</dt>
            <dd><?= max(0, $remaining) ?> of <?= (int)$event['capacity'] ?></dd>

            <dt>Description</dt>
            <dd><?= nl2br(htmlspecialchars($event['description'] ?? '')) ?></dd>
        </dl>
    </div>

    <!-- booking action -->
    <div class="formActions">
        <?php if ($myStatus === 'booked'): ?>
            <p>You're booked on this event.</p>
            <a href="my_bookings.php" class="btn btnSecondary">View My Bookings</a>
        <?php elseif (!$isActive || $isPast): ?>
            <p>Bookings are closed for this event.</p>
        <?php elseif ($remaining <= 0): ?>
            <p>This event is full.</p>
        <?php else: ?>
            <form method="POST" action="event_detail.php?id=<?= (int)$event['eventId'] ?>" class="inlineForm">
                <button type="submit" name="book_btn" class="btn btnPrimary">Book a Place</button>
            </form>
        <?php endif; ?>
        <a href="events.php" class="btn btnSecondary">Back to Events</a>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
