<?php
// shows all active events attendees can browse and sign up for

session_start();
require_once '../db_connect.php';
require_once '../includes/auth.php';
require_role('attendee');
// only show events that are still open and haven't already happened, soonest first.
// the subquery gives us the booked count per event so we can show places left.
$result = $conn->query(
    "SELECT e.eventId, e.title, e.location, e.eventDate, e.capacity,
            c.name AS categoryName,
            u.name AS organiserName,
            (SELECT COUNT(*) FROM bookings b
             WHERE b.eventId = e.eventId AND b.status = 'booked') AS bookedCount
     FROM events e
     LEFT JOIN categories c ON e.categoryId = c.categoryId
     JOIN users u ON e.organiserId = u.userId
     WHERE e.status = 'active' AND e.eventDate >= NOW()
     ORDER BY e.eventDate ASC");
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

    <!-- upcoming events table -->
    <div class="tableWrap">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Location</th>
                    <th>Date</th>
                    <th>Organiser</th>
                    <th>Places Left</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($events)): ?>
                    <tr>
                        <td colspan="7" class="emptyState">No upcoming events right now. Check back soon.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($events as $event): ?>
                    <?php $placesLeft = (int)$event['capacity'] - (int)$event['bookedCount']; ?>
                    <tr>
                        <td><?= htmlspecialchars($event['title']) ?></td>
                        <td><?= htmlspecialchars($event['categoryName'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($event['location']) ?></td>
                        <td><?= htmlspecialchars(date('d M Y, H:i', strtotime($event['eventDate']))) ?></td>
                        <td><?= htmlspecialchars($event['organiserName']) ?></td>
                        <td><?= max(0, $placesLeft) ?> of <?= (int)$event['capacity'] ?></td>
                        <td>
                            <div class="tableActions">
                                <a href="event_detail.php?id=<?= (int)$event['eventId'] ?>" class="btn btnSecondary">View</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
