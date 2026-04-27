<?php
// shows all active events attendees can browse and sign up for

session_start();
require_once '../db_connect.php';
require_once '../includes/auth.php';
require_role('attendee');
// only show events that are still open and haven't already happened, soonest first
$result = $conn->query(
    "SELECT e.eventId, e.title, e.location, e.eventDate, e.capacity,
            c.name AS categoryName,
            u.name AS organiserName
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
    <?php if (empty($events)): ?>
        <p>No upcoming events right now. Check back soon.</p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Location</th>
                <th>Date</th>
                <th>Organiser</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($events as $event): ?>
            <tr>
                <td><?= htmlspecialchars($event['title']) ?></td>
                <td><?= htmlspecialchars($event['categoryName'] ?? '-') ?></td>
                <td><?= htmlspecialchars($event['location']) ?></td>
                <td><?= htmlspecialchars($event['eventDate']) ?></td>
                <td><?= htmlspecialchars($event['organiserName']) ?></td>
                <td>
                    <a href="event_detail.php?id=<?= (int)$event['eventId'] ?>">View</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</main>

<?php require_once '../includes/footer.php'; ?>
