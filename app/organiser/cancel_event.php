<?php
include 'organiser_check.php';
require_once '../db_connect.php';

if (isset($_GET['id'])) {
    $eventId = $_GET['id'];
    $organiserId = $_SESSION['user_id'];

    // We check organiserId to make sure they own the event they are trying to cancel!
    $sql = "UPDATE events SET status = 'cancelled' WHERE eventId = ? AND organiserId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $eventId, $organiserId);
    
    if ($stmt->execute()) {
        header("Location: dashboard.php?msg=cancelled");
    } else {
        echo "Error cancelling event.";
    }
}
?>