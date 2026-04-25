<?php
// admin can edit any event regardless of who created it

session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_role('admin');

// get event id from URL on GET, or from hidden field on POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
} else {
    $eventId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
}

if ($eventId <= 0) {
    header('Location: dashboard.php');
    exit();
}

// fetch the event — no organiserId check, admin can edit any
$stmt = $conn->prepare(
    "SELECT eventId, title, description, location, eventDate, capacity, categoryId, status
     FROM events
     WHERE eventId = ?");
$stmt->bind_param("i", $eventId);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$event) {
    header('Location: dashboard.php');
    exit();
}

// fetch categories for the dropdown
$catResult = $conn->query("SELECT categoryId, name FROM categories ORDER BY name ASC");
$categories = [];
$row = $catResult->fetch_assoc();
while ($row !== null) {
    $categories[] = $row;
    $row = $catResult->fetch_assoc();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = isset($_POST['title'])       ? trim($_POST['title'])       : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $location    = isset($_POST['location'])    ? trim($_POST['location'])    : '';
    $eventDate   = isset($_POST['eventDate'])   ? trim($_POST['eventDate'])   : '';
    $capacity    = isset($_POST['capacity'])    ? (int)$_POST['capacity']     : 0;
    $categoryId  = isset($_POST['categoryId'])  ? (int)$_POST['categoryId']  : 0;
    $status      = isset($_POST['status'])      ? trim($_POST['status'])      : '';

    if ($title === '') {
        $errors[] = 'Title is required.';
    } elseif (strlen($title) > 150) {
        $errors[] = 'Title must be 150 characters or fewer.';
    }

    if ($location === '' ) {
        $errors[] = 'Location is required.';
    } elseif (strlen($location) > 200) {
        $errors[] = 'Location must be 200 characters or fewer.';
    }

    if ($eventDate === '') {
        $errors[] = 'Event date is required.';
    }

    if ($capacity <= 0) {
        $errors[] = 'Capacity must be a positive number.';
    }

    $allowedStatuses = ['active', 'full', 'completed', 'cancelled'];
    if (!in_array($status, $allowedStatuses)) {
        $errors[] = 'Invalid status selected.';
    }

    if (empty($errors)) {
        $stmt = $conn->prepare(
            "UPDATE events
             SET title = ?, description = ?, location = ?, eventDate = ?,
                 capacity = ?, categoryId = ?, status = ?
             WHERE eventId = ?");
        // categoryId can be null if 0
        $catParam = $categoryId > 0 ? $categoryId : null;
        $stmt->bind_param("ssssiisi", $title, $description, $location, $eventDate,
                          $capacity, $catParam, $status, $eventId);
        $stmt->execute();
        $stmt->close();

        $_SESSION['flash'] = 'Event updated successfully.';
        header('Location: dashboard.php');
        exit();
    }

} else {
    // pre-fill form values from the fetched event
    $title       = $event['title'];
    $description = $event['description'];
    $location    = $event['location'];
    $eventDate   = date('Y-m-d\TH:i', strtotime($event['eventDate']));
    $capacity    = $event['capacity'];
    $categoryId  = $event['categoryId'];
    $status      = $event['status'];
}
?>
<?php require_once '../includes/header.php'; ?>

<main>
    <h1>Edit Event</h1>

    <?php if (!empty($errors)): ?>
        <ul class="formErrors">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="edit_event.php">
        <input type="hidden" name="id" value="<?= (int)$event['eventId'] ?>">

        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>">

        <label for="description">Description</label>
        <textarea id="description" name="description"><?= htmlspecialchars($description ?? '') ?></textarea>

        <label for="location">Location</label>
        <input type="text" id="location" name="location" value="<?= htmlspecialchars($location ?? '') ?>">

        <label for="eventDate">Date and Time</label>
        <input type="datetime-local" id="eventDate" name="eventDate" value="<?= htmlspecialchars($eventDate) ?>">

        <label for="capacity">Capacity</label>
        <input type="number" id="capacity" name="capacity" min="1" value="<?= (int)$capacity ?>">

        <label for="categoryId">Category</label>
        <select id="categoryId" name="categoryId">
            <option value="0">-- None --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= (int)$cat['categoryId'] ?>"
                    <?php if ((int)$cat['categoryId'] === (int)$categoryId) echo 'selected'; ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="status">Status</label>
        <select id="status" name="status">
            <option value="active"    <?php if ($status === 'active')    echo 'selected'; ?>>Active</option>
            <option value="full"      <?php if ($status === 'full')      echo 'selected'; ?>>Full</option>
            <option value="completed" <?php if ($status === 'completed') echo 'selected'; ?>>Completed</option>
            <option value="cancelled" <?php if ($status === 'cancelled') echo 'selected'; ?>>Cancelled</option>
        </select>

        <button type="submit">Save Changes</button>
        <a href="dashboard.php">Cancel</a>
    </form>
</main>

<?php require_once '../includes/footer.php'; ?>
