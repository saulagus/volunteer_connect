<?php
// admin can cancel any event regardless of who created it

session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_role('admin');

$eventId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($eventId <= 0) {
    header('Location: dashboard.php');
    exit();
}

// set status to cancelled — no organiserId check, admin can cancel any event
$stmt = $conn->prepare(
    "UPDATE events SET status = 'cancelled' WHERE eventId = ?");
$stmt->bind_param("i", $eventId);
$stmt->execute();
$stmt->close();

$_SESSION['flash'] = 'Event cancelled.';
header('Location: dashboard.php');
exit();
