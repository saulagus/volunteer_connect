<?php
// admin can edit any event regardless of who created it

session_start();
require_once '../db_connect.php';
require_once '../includes/auth.php';
require_role('admin');

// get event id from URL on GET, or from hidden field on POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $eventId = (int)$_POST['id'];
    } else {
        $eventId = 0;
    }
} else {
    if (isset($_GET['id'])) {
        $eventId = (int)$_GET['id'];
    } else {
        $eventId = 0;
    }
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
$result = $stmt->get_result();
$event  = $result->fetch_assoc();
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
    if (isset($_POST['title'])) {
        $title = trim($_POST['title']);
    } else {
        $title = '';
    }
    if (isset($_POST['description'])) {
        $description = trim($_POST['description']);
    } else {
        $description = '';
    }
    if (isset($_POST['location'])) {
        $location = trim($_POST['location']);
    } else {
        $location = '';
    }
    if (isset($_POST['eventDate'])) {
        $eventDate = trim($_POST['eventDate']);
    } else {
        $eventDate = '';
    }
    if (isset($_POST['capacity'])) {
        $capacity = (int)$_POST['capacity'];
    } else {
        $capacity = 0;
    }
    if (isset($_POST['categoryId'])) {
        $categoryId = (int)$_POST['categoryId'];
    } else {
        $categoryId = 0;
    }
    if (isset($_POST['status'])) {
        $status = trim($_POST['status']);
    } else {
        $status = '';
    }

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
        if ($categoryId > 0) {
            $catParam = $categoryId;
        } else {
            $catParam = null;
        }
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
