<?php
// admin can permanently delete any event regardless of who created it

session_start();
require_once '../db_connect.php';
require_once '../includes/auth.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit();
}

if (isset($_POST['id'])) {
    $eventId = (int)$_POST['id'];
} else {
    $eventId = 0;
}

if ($eventId <= 0) {
    header('Location: dashboard.php');
    exit();
}

// delete the event row — no organiserId check, admin can delete any event
$stmt = $conn->prepare("DELETE FROM events WHERE eventId = ?");
$stmt->bind_param("i", $eventId);
$stmt->execute();
$stmt->close();

$_SESSION['flash'] = 'Event deleted.';
header('Location: dashboard.php');
exit();
