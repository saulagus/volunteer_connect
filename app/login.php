<?php
session_start();
// Database connection
require_once 'db_connect.php'; 

// Check if form was submitted
if (isset($_POST['login_btn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepared Statements for security
    // Query the database for the user (by email because we need to fetch the hash to verify it)
    $stmt = $conn->prepare("SELECT userId, name, password, role FROM users WHERE email = ?");
    // Bind the email parameter
    $stmt->bind_param("s", $email);
    // Execute the query
    $stmt->execute();
    // Get the result
    $result = $stmt->get_result();

    //IF we get any results back:
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify the hashed password
        if (password_verify($password, $user['password'])) {
            
            // Regenerate session ID for security
            session_regenerate_id();

            // Store data for your group members to use
            $_SESSION['user_id'] = $user['userId'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            // Redirect based on role
            switch ($_SESSION['user_role']) {
            case 'admin':
                header("Location: admin/dashboard.php");
                break;
            case 'organiser':
                header("Location: organiser/dashboard.php");
                break;
            case 'attendee':
                header("Location: attendeeS/dashboard.php");
                break;
            default:
                header("Location: login.php");
            }
            exit();
            
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "No account found with that email.";
    }
    
    $stmt->close();
}

// If there's an error, redirect back to login with a message
if (isset($error)) {
    header("Location: login.php?error=" . urlencode($error));
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Volunteer Connect</title>
</head>
<body>
    <h2>Login to Manage Events</h2>
    <form action="login_process.php" method="POST">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>
        
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        
        <button type="submit" name="login_btn">Login</button>
    </form>
</body>
</html>

