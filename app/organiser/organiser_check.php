<?php
session_start();
// CHECK first
// If not logged in OR role is not organiser, boot them out
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'organiser') {
    header("Location: ../login.php?error=unauthorized");
    exit();
}
?>