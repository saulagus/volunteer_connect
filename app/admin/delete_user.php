<?php
// deletes a user by id on post, only redirects, no HTML

session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_role('admin');
// reject anything that isn't a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: users.php');
    exit();
}
// get the id from the form, default to 0 if not set
if (isset($_POST['id'])) {
    $id = (int)$_POST['id'];
} else {
    $id = 0;
}
// if no valid id, go back to the users list
if ($id === 0) {
    header('Location: users.php');
    exit();
}
// prevent admin from deleting their own account
if ($id === (int)$_SESSION['user_id']) {
    $_SESSION['flash_error'] = 'You cannot delete your own account.';
    header('Location: users.php');
    exit();
}

// delete the user - bookings and events cascade automatically
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

$_SESSION['flash'] = 'User deleted.';
header('Location: users.php');
exit();