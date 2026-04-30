<?php 
// Session-check
require_once '../db.php'; 
require_once '../includes/auth.php';
session_start();
require_role('organiser');

// Check if the request is a POST and the ID exists
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    
    //Cast to int for safety
    $eventId = (int)$_POST['id'];
    $organiserId = (int)$_SESSION['user_id'];

    // We check organiserId to make sure they own the event they are trying to cancel!
    $sql = "UPDATE events SET status = 'cancelled' WHERE eventId = ? AND organiserId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $eventId, $organiserId);
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        // Redirect with exit()
        header("Location: dashboard.php?msg=cancelled");
        exit(); 
    } else {
        echo "Error cancelling event.";
    }
    $stmt->close();
} else {
    // Fallback redirect if someone tries to access this page directly without a POST ID
    header("Location: dashboard.php");
    exit();
}
?>