<?php
// landing page or redirects logged-in users straight to their dashboard

session_start();
require_once 'includes/auth.php';

// send logged-in users to the right place
if (is_logged_in()) {
    $role = current_role();
    if ($role === 'admin') {
        header('Location: /admin/dashboard.php');
        exit();
    }
    if ($role === 'organiser') {
        header('Location: /organiser/dashboard.php');
        exit();
    }
    if ($role === 'attendee') {
        header('Location: /attendee/events.php');
        exit();
    }
}
?>
<?php require_once 'includes/header.php'; ?>

<main>
    <div class="hero">
        <h1>Make a difference in your community</h1>
        <p>
            Volunteer Connect brings people together. Browse upcoming volunteer events,
            sign up in seconds, or create your own and manage your attendees.
        </p>
        <div class="heroBtns">
            <a href="/login.php"    class="btn btnPrimary">Log In</a>
            <a href="/register.php" class="btn btnSecondary">Create an Account</a>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
