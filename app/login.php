<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Initialize error as empty
$error = ""; 

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

            // Close connection before redirecting
            $stmt->close();
            $conn->close();

            // Redirect based on role
            switch ($_SESSION['user_role']) {
            case 'admin':
                header("Location: admin/dashboard.php");
                break;
            case 'organiser':
                header("Location: organiser/dashboard.php");
                break;
            case 'attendee':
                header("Location: attendee/dashboard.php");
                break;
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

?>
<?php require_once 'includes/header.php'; ?>

<main>
    <div class="authCard">
        <h1>Log In</h1>

        <!-- show login errors -->
        <?php if ($error !== ""): ?>
            <ul class="formErrors">
                <li><?= htmlspecialchars($error) ?></li>
            </ul>
        <?php endif; ?>

        <?php if (isset($_GET['error']) && $_GET['error'] === 'unauthorized'): ?>
            <ul class="formErrors">
                <li>Please log in to access that page.</li>
            </ul>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <div class="formActions">
                <button type="submit" name="login_btn" class="btn btnPrimary">Log In</button>
            </div>
        </form>

        <p class="authFooter">
            Don't have an account? <a href="/register.php">Register here</a>
        </p>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
<?php $conn->close(); ?>