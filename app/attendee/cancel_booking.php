<?php
// cancels a booking, only the user who made the booking can cancel it

session_start();
require_once '../db_connect.php';
require_once '../includes/auth.php';
require_role('attendee');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: my_bookings.php');
    exit();
}

if (isset($_POST['id'])) {
    $bookingId = (int)$_POST['id'];
} else {
    $bookingId = 0;
}

if ($bookingId <= 0) {
    header('Location: my_bookings.php');
    exit();
}

$userId = (int)$_SESSION['user_id'];

// confirm the booking exists, belongs to this user, and is currently 'booked'.
// combining the lookup and ownership check in one query avoids leaking that the booking exists but isn't theirs.
$stmt = $conn->prepare(
    "SELECT bookingId
     FROM bookings
     WHERE bookingId = ? AND userId = ? AND status = 'booked'");
$stmt->bind_param("ii", $bookingId, $userId);
$stmt->execute();
$check = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$check) {
    // not theirs, doesn't exist, or already cancelled/attended — bounce back without doing anything
    header('Location: my_bookings.php');
    exit();
}

// flip the booking to cancelled rather than deleting it, so we keep the history
$stmt = $conn->prepare(
    "UPDATE bookings SET status = 'cancelled' WHERE bookingId = ?");
$stmt->bind_param("i", $bookingId);
$stmt->execute();
$stmt->close();

$_SESSION['flash'] = 'Booking cancelled.';
header('Location: my_bookings.php');
exit();
