<?php
// Set strict reporting to catch errors
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Database configuration
$conn = new mysqli("localhost", "root", "", "volunteerconnect");

// Check connection
if ($conn->connect_error) {
    // Without displaying specific database error messages to users
    die("Connection failed");
}
?>