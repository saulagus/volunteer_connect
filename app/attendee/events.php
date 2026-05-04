<?php
// shows all active events attendees can browse and sign up for

session_start();
require_once '../db_connect.php';
require_once '../includes/auth.php';
require_role('attendee');
// only show events that are still open and haven't already happened, soonest first.
// the subquery gives us the booked count per event so we can show places left.
$result = $conn->query(
    "SELECT events.eventId, events.title, events.location, events.eventDate, events.capacity,
            categories.name AS categoryName,
            users.name AS organiserName,
            (SELECT COUNT(*) FROM bookings
             WHERE bookings.eventId = events.eventId AND bookings.status = 'booked') AS bookedCount
     FROM events
     LEFT JOIN categories ON events.categoryId = categories.categoryId
     JOIN users ON events.organiserId = users.userId
     WHERE events.status = 'active' AND events.eventDate >= NOW()
     ORDER BY events.eventDate ASC");
// collect all rows into an array to loop through in the HTML
$events = [];
$row = $result->fetch_assoc();
while ($row !== null) {
    // add current row to events array
    $events[] = $row;
    // go to next row
    $row = $result->fetch_assoc();
}
?>
<?php require_once '../includes/header.php'; ?>

<main>
    <h1>Browse Events</h1>

    <!-- upcoming events as a card grid -->
    <div class="eventGrid">
        <?php if (empty($events)): ?>
            <div class="eventGridEmpty">No upcoming events right now. Check back soon.</div>
        <?php endif; ?>
        <?php foreach ($events as $event): ?>
            <?php $placesLeft = (int)$event['capacity'] - (int)$event['bookedCount']; ?>
            <article class="eventCard">
                <span class="eventCardCategory"><?= htmlspecialchars($event['categoryName'] ?? 'General') ?></span>

                <h2 class="eventCardTitle"><?= htmlspecialchars($event['title']) ?></h2>

                <div class="eventCardMeta">
                    <span><strong>When</strong> <?= htmlspecialchars(date('d M Y, H:i', strtotime($event['eventDate']))) ?></span>
                    <span><strong>Where</strong> <?= htmlspecialchars($event['location']) ?></span>
                    <span><strong>Hosted by</strong> <?= htmlspecialchars($event['organiserName']) ?></span>
                </div>

                <div class="eventCardCapacity">
                    <span>Places left</span>
                    <span class="eventCardCapacityNum"><?= max(0, $placesLeft) ?> / <?= (int)$event['capacity'] ?></span>
                </div>

                <div class="eventCardActions">
                    <a href="event_detail.php?id=<?= (int)$event['eventId'] ?>" class="btn btnPrimary">View &amp; Book</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
